<?php

function processRecursivlyXml (SimpleXMLElement $node, &$output) {
    if (count($node->children()) > 0) {
		$checkClassDefinition = ($node->getName() === 'classes');
        foreach ($node as $keyNewNode=>$newNode) {
			$className = null;
			if($checkClassDefinition && $keyNewNode == 'class'){
				$attributesXML = $newNode->attributes();
				$className = (string) $attributesXML['id'];
				if (isset($className) && !array_key_exists($className, $output)) {
					$output[$className] = [];
					
					if(isset($newNode->parent)){
						$output[$className]['parent'] = (string) $newNode->parent->class->attributes()->id;
					}
				}
			}
			
            processRecursivlyXml($newNode, $output);
        }
    }
}

function generatePhpClassesFromXML (SimpleXMLElement $xmlObject){
	$result = "<?php\n";
	
	$classesInXML = [];
	processRecursivlyXml($xmlObject, $classesInXML);
	
	foreach ($classesInXML as $className=>$classAttributes){
		$result .= 'class '.$className.((array_key_exists('parent', $classAttributes))?' extends '.$classAttributes['parent']:'')."\n{\n}\n";
	}
	
	return $result;
}

function createPhpFileFromXml (SimpleXMLElement $xmlObject, $fileName, &$output){
	
	$phpContent = generatePhpClassesFromXML($xmlObject);
	$output = $phpContent;
	
	return file_put_contents($fileName.'.php', $phpContent);
}


$response = [];
if(is_array($argv) && count($argv) >= 2){
	$filePath = mb_ereg_replace("([^\w\s\-_~,;\[\]\(\).:/?#@!$&'*=+%])", '', trim($argv[1]));
	$xmlContent = @file_get_contents($filePath);
	if ($xmlContent !== false) {
		$fileName = pathinfo($filePath, PATHINFO_FILENAME);
		$xmlObject = simplexml_load_string($xmlContent);
		
		$details = [];
		if(createPhpFileFromXml($xmlObject, $fileName, $details)){
			$response = ['code'=>200, 'message'=>'Successfully generated file '.$fileName.'.php !', 'file'=>$fileName, 'content'=>$details];
		} else  {
			$response = ['code'=>500, 'message'=>'Failed generate PHP Class from XML'];
		}
	} else {
		$response = ['code'=>500, 'message'=>'Failed to open xml, check file integrity'];
	}
} else {
	$response = ['code'=>500, 'message'=>'File Argument missing: PHP '.basename(__FILE__).' [Path or URL of XML File]'];
}


exit(json_encode($response));
?>