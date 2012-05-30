<?php
// Prevent some childish-hackish things
if(!defined('GITHUB')) {
    file_put_contents('./log.txt', 'Direct access to debug.php is not allowed');
    die;
}

/**
 *  Create appropriate folders if they don't exists
 */
function create_folders($file){
    $path = dirname($file);
    if(is_dir($path))
        return;

    // recursion
    if (!mkdir($path, 0755, true)) {
        file_put_contents('./log.txt', 'Failed to create folders: ' . $path);
    }
}

/**
 *  Display variable content in a better way
 */
if(!function_exists('print_var')) {
    function print_var($var, $die = false){
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

?>