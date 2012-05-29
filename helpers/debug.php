<?php
// Prevent some childish-hackish things
if(!defined('GITHUB')) die;

// for debug
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