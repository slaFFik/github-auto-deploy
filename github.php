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
 *  Below is the so called pseudo-code
 */

// currently we can process only public repositories. Private will die.
if($config['repo_type'] !== 'public') die;

// get the list of all files we need to upload
//foreach

// create raw links to the sources of that files
//raw.url . filenames

// get the content of each file...
//file_get_contents

//...and upload to appropriate place
//file_put_contents

?>
