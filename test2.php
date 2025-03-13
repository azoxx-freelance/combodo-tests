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

function createPhpFileFromXml (SimpleXMLElement $xmlObject, $fileName){
	
	$phpContent = generatePhpClassesFromXML($xmlObject);
	echo 'Génération du fichier '.$fileName.".php avec ce contenu :\n\n".$phpContent."\n\n";
	
	return file_put_contents($fileName.'.php', $phpContent);
}


if(is_array($argv) && count($argv) >= 2){
	$filePath = mb_ereg_replace("([^\w\s\-_~,;\[\]\(\).:/?#@!$&'*=+%])", '', trim($argv[1]));
	$xmlContent = @file_get_contents($filePath);
	if ($xmlContent !== false) {
		$fileName = pathinfo($filePath, PATHINFO_FILENAME);
		$xmlObject = simplexml_load_string($xmlContent);
		
		if(createPhpFileFromXml($xmlObject, $fileName)){
			echo "Fichier ".$fileName.".php généré avec succès !\n\n";
		} else  {
			exit('Erreur lors de la génération du fichier');
		}
	} else {
		exit('Failed to open xml.');
	}
} else {
	exit("File Argument missing.\nPHP test1.php [XML File]\n");
}
?>