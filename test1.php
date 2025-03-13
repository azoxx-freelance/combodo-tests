<?php

if (file_exists('sample1.xml')) {
    $xml = simplexml_load_file('sample1.xml');
	
	echo '<pre>';
    print_r($xml);
	echo '</pre>';
} else {
    exit('Failed to open xml.');
}
?>