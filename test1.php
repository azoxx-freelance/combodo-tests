<?php

if(is_array($argv) && count($argv) >= 2){
	$filePath = mb_ereg_replace("([^\w\s\-_~,;\[\]\(\).:/?#@!$&'*=+%])", '', trim($argv[1]));
	$xmlContent = @file_get_contents($filePath);
	if ($xmlContent !== false) {
		$xmlObject = simplexml_load_string($xmlContent);
		
		var_dump($xmlObject);
	} else {
		exit('Failed to open xml.');
	}
} else {
	exit("File Argument missing.\nPHP test1.php [XML File]\n");
}
?>