<?php
// Prevent some childish-hackish things
if(!isset($_POST['payload']) || empty($_POST['payload'])) die;

define('GITHUB', true);

// now we can connect and manipulate data
include(dirname(__FILE__) . '/helpers/debug.php');
// get all the variables we may need in $config
include(dirname(__FILE__) . '/helpers/debug.php');

/**
 *  Now lets do something different
 *  Below is the so called Business Logic
 */


if($config['repo_type'] !== 'public') die;



?>
