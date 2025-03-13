<?php

function processRecursivlyXml (SimpleXMLElement $node, &$output) {
    if (count($node->children()) > 0) {
        foreach ($node as $keyNewNode=>$newNode) {
			if($keyNewNode == 'class'){
				$attributesXML = $newNode->attributes();
				if (isset($attributesXML['id']) && !in_array($attributesXML['id'], $output)) {
					$output[] = (string) $attributesXML['id'];
				}
			}
			
            processRecursivlyXml($newNode, $output);
        }
    }
}

if(is_array($argv) && count($argv) >= 2){
	$filePath = mb_ereg_replace("([^\w\s\-_~,;\[\]\(\).:/?#@!$&'*=+%])", '', trim($argv[1]));
	$xmlContent = @file_get_contents($filePath);
	if ($xmlContent !== false) {
		$xmlObject = simplexml_load_string($xmlContent);
		
		$classInXML = [];
		processRecursivlyXml($xmlObject, $classInXML);
		
		echo count($classInXML);
		
	} else {
		exit('Failed to open xml.');
	}
} else {
	exit("File Argument missing.\nPHP test1.php [XML File]\n");
}
?>