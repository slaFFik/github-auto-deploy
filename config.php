<?php
// Prevent some childish-hackish things
if(!defined('GITHUB')) {
    file_put_contents('./hook.txt', 'Direct access to config.php is not allowed');
    die;
}

$config = array(
    'username'      => 'slaFFik',
    'repo'          => 'github-auto-deploy',
    // currenty support only public. Possible values: public|private
    'repo_type'     => 'public',
    // what branch should we take care of?
    'branch'        => 'master',
    // no trailing slash
    'upload_path'   => dirname(__FILE__) . '/gad'
);