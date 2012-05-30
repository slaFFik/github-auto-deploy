<?php
// Prevent some childish-hackish things
if(!defined('GITHUB')) {
    file_put_contents('./log.txt', 'Direct access to config.php is not allowed');
    die;
}

$config = array(
    'username'      => 'slaFFik',
    'repo'          => 'github-auto-deploy',
    // currenty supporting only public. Possible values: public|private
    'repo_type'     => 'public',
    // what branch should we take care of?
    'branch'        => 'master',
    // where you want to deploy the github project files. No trailing slash
    'upload_path'   => dirname(__FILE__) . '/gad'
);

?>