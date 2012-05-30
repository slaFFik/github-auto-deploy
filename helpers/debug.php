<?php
// Prevent some childish-hackish things
if(!defined('GITHUB')) {
    file_put_contents('./hook.txt', 'Direct access to debug.php is not allowed');
    die;
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