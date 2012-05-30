<?php
// Prevent some childish-hackish things
if(!isset($_POST['payload']) || empty($_POST['payload'])) {
    file_put_contents('./hook.txt', 'No payload content');
    die;
};

define('GITHUB', true);

// now we can connect and manipulate data
include(dirname(__FILE__) . '/helpers/debug.php');
// get all the variables we may need in $config
include(dirname(__FILE__) . '/config.php');

/**
 *  Now lets do something different
 *  Below is the so called pseudo-code
 */

// currently we can process only public repositories. Private will die.
if($config['repo_type'] !== 'public') {
    file_put_contents('./hook.txt', 'Repo is private');
    die;
}

// We received json object - decode it
$data = json_decode($_POST['payload']);

// if commit data is empty - exit
if(empty($data->commits) || !is_array($data->commits)) {
    file_put_contents('./hook.txt', 'Commits data is empty');
    die;
}

$added = $removed = $modified = array();
$save  = new Stdclass;

// get the list of all files we need to upload
foreach($data->commits as $commit){
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
 */
foreach ($save->added as $i => $add) {
    $files['download'][$i]['url']  = 'https://raw.github.com/' . $config['username'] . '/' . $config['repo'] . '/' . $config['branch'] . '/' . $add;
    $files['download'][$i]['path'] = $config['upload_path'] . '/' . $add;
}
foreach ($save->modified as $i => $modify) {
    $files['download'][$i]['url']  = 'https://raw.github.com/' . $config['username'] . '/' . $config['repo'] . '/' . $config['branch'] . '/' . $modify;
    $files['download'][$i]['path'] = $config['upload_path'] . '/' . $modify;
}
foreach ($save->removed as $remove) {
    $files['remove'][] = $config['upload_path'] . '/' . $remove;
}

/**
 *  Actually the deploy is done below
 */
// process new and modified files
foreach($files['download'] as $download){
    // download
    $content = file_get_contents($download['url']);
    // upload
    file_put_contents($download['url'], $content);
}

// delete files that were removed
foreach ($files['remove'] as $remove) {
    unlink($remove);
}


// Debug
file_put_contents('./hook.txt', print_r($save,true));
file_put_contents('./hook.txt', print_r($files,true), FILE_APPEND);
file_put_contents('./hook.txt', print_r($data,true), FILE_APPEND);

?>
