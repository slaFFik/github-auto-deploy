<?php

/**
 *  Github automatic deployment script for php projects
 *
 *  This file will make your project available on a live server several seconds
 *      after each push was made to a repository by any user
 */

// Prevent some childish-hackish things
if(!isset($_POST['payload']) || empty($_POST['payload'])) {
    GAD::log('error', 'No payload content', true);
}

/**
 *  Below are configs used for a deploy. Double check them.
 */
// Your github username
if (!defined('GH_USERNAME'))
    define('GH_USERNAME', 'slaFFik');
// Slug of the epo you want to autodeploy
if (!defined('GH_REPO'))
    define('GH_REPO', 'github-auto-deploy');
//Type of the repository. Possible values: public|private
// Currenty supporting only public
if (!defined('GH_REPO_TYPE'))
    define('GH_REPO_TYPE', 'public');
// What branch should we take care of? Only one can be used
if (!defined('GH_BRANCH'))
    define('GH_BRANCH', 'master');
// Where you want to deploy the github project files. No trailing slash
if (!defined('GH_UPLOAD_PATH'))
    define('GH_UPLOAD_PATH', dirname(__FILE__) . '/project');

/**
 *  Main class itself where all the magic happens
 */
class GAD{
    // where to save all deploy results
    const LOG_FILE = './log.txt';

    // what we received from Github
    public $data   = false;

    // list of files to process on a server
    public $files  = array();

    // list of allowed IPs for a deploy. Defaults are Github IPs
    public $ips    = array(
                        '207.97.227.253',
                        '50.57.128.197',
                        '108.171.174.178'
                    );

    /**
     *  List of files you want to exclude from a deploy
     *  This path should be relative to a GH_UPLOAD_PATH, without facing and trailing slashes
     */
    public $ex_files = array();

    /**
     *  List of folders you want to exclude from a deploy
     *  All files in that folders will be ignored and not deployed too
     *  This path should be relative to a GH_UPLOAD_PATH, without facing and trailing slashes
     */
    public $ex_dirs  = array();

    /**
     *  Now time for a deploy - get the POST data
     */
    function __construct($payload){
        // currently we can process only public repositories. Private will die.
        if (GH_REPO_TYPE !== 'public') {
            GAD::log('error', 'Repo is private', true);
        }

        // check that we have rights to deploy - IP check
        if (!in_array($_SERVER['REMOTE_ADDR'], $this->ips)) {
            GAD::log('error', 'Attempt to make a deploy from a not allowed IP: ' . $_SERVER['REMOTE_ADDR'], true);
        }

        GAD::log('note', 'Deploy started');

        // We received json object - decode it
        $this->data = json_decode($payload);

        // if commit data is empty - exit
        if(empty($this->data->commits) || !is_array($this->data->commits)) {
            GAD::log('error', 'Commits data is empty (no commits?)', true);
        }

        // create list of files to process
        $this->files = $this->get_files();

        // the main deploy itself
        $this->deploy();

        GAD::log('note', 'Deploy finished');
    }

    /**
     *  Get the files that are needed to be uploaded
     */
    protected function get_files(){
        $added = $removed = $modified = array();
        $save  = new Stdclass;

        // get the list of all files we need to upload
        foreach($this->data->commits as $commit){
            $added    = array_merge($added, $commit->added);
            $modified = array_merge($modified, $commit->modified);
            $removed  = array_merge($removed, $commit->removed);
        }

        $save->added    = array_unique($added);
        $save->modified = array_unique($modified);
        $save->removed  = array_unique($removed);

        /**
         *  Create raw links to the sources of that files, like:
         *      https://raw.github.com/slaFFik/github-auto-deploy/master/config.php
         *  Also paths are created (place where to upload)
         */
        $i = 0;
        foreach ($save->added as $add) {
            // exclude those files we don't need to be synced with a repo
            if ($this->excluding_file($add)) continue;

            $files['download'][$i]['name'] = $add;
            $files['download'][$i]['url']  = 'https://raw.github.com/' . GH_USERNAME . '/' . GH_REPO . '/' . GH_BRANCH . '/' . $add;
            $files['download'][$i]['path'] = GH_UPLOAD_PATH . '/' . $add;
            $this->create_dir($files['download'][$i]['path']);
            $i++;
        }
        foreach ($save->modified as $modify) {
            // exclude those files we don't need to be synced with a repo
            if ($this->excluding_file($modify)) continue;

            $files['download'][$i]['name'] = $modify;
            $files['download'][$i]['url']  = 'https://raw.github.com/' . GH_USERNAME . '/' . GH_REPO . '/' . GH_BRANCH . '/' . $modify;
            $files['download'][$i]['path'] = GH_UPLOAD_PATH . '/' . $modify;
            $this->create_dir($files['download'][$i]['path']);
            $i++;
        }
        foreach ($save->removed as $remove) {
            // exclude those files we don't need to be synced with a repo
            if ($this->excluding_file($remove)) continue;

            $files['remove'][$i]['name'] = $remove;
            $files['remove'][$i]['path'] = GH_UPLOAD_PATH . '/' . $remove;
            $i++;
        }

        return $files;
    }

    /**
     *  Check that current file is not in an exlcude list
     *      If returned true - omit.
     */
    protected function excluding_file($file){
        if (in_array($file, $this->ex_files) || in_array(dirname($file), $this->ex_dirs))
            return true;

        return false;
    }

    /**
     *  Actually the deploy is done below
     */
    protected function deploy(){
        // list of successfully written files
        $names = array();
        // process new and modified files
        foreach($this->files['download'] as $download){
            // download
            $content = file_get_contents($download['url']);
            // upload
            if(file_put_contents($download['path'], $content))
                $names[] = $download['name'];
            else
                GAD::log('error', 'Error while trying to upload this file: ' . $download['name'], true);
        }

        if (!empty($names))
            GAD::log('success', 'Modified/added files: ' . implode(', ', $names));

        // delete files that were removed
        if(isset($this->files['remove'])){
            // files that were removed
            $removed = array();
            foreach ($this->files['remove'] as $remove) {
                if (unlink($remove['path']))
                    $removed[] = $remove['name'];
                else
                    GAD::log('error', 'Error while trying to remove this file: ' . $remove['name']);
            }
            if (!empty($removed))
                GAD::log('success', 'Deleted files: ' . implode(', ', $removed));
        }
    }

    /**
     *  Save to the log all events connected with the deployment process
     */
    static function log($status, $message, $die = false){
        file_put_contents(GAD::LOG_FILE,
                            date('Y.m.d@H:i:s') . ' - ' . strtoupper($status) . ' - ' . $message . "\r\n",
                            FILE_APPEND
                        );
        if ($die)
            die;
    }

    /**
     *  Create appropriate folders if they don't exist
     */
    protected function create_dir($file){
        $path = dirname($file);
        if(is_dir($path))
            return;

        // recursion
        if (!mkdir($path, 0755, true)) {
            GAD::log('error', 'Failed to create folders: ' . $path, true);
        }
    }

    /**
     *  Display variable content in a better way
     */
    final protected function print_var($var, $die = false){
        echo '<pre>';
        if ( !empty($var))
            print_r($var);
        else
            var_dump($var);
        echo '</pre>';
        if ($die)
            die;
    }

}

$deploy = new GAD($_POST['payload']);

?>