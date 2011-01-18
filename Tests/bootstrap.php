<?php
/**
 * Some bootstrapping for tests is necesary.
 * The include-path has to be adjusted, and some test-utility functions are
 * defined in this file.
 * 
 * @author Christoph Gockel
 */
error_reporting(E_ALL);
// TODO: check include path
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/..'.PATH_SEPARATOR.dirname(__FILE__).'/../PiBX'.PATH_SEPARATOR.dirname(__FILE__).'/../..');

function AddXMLElement(SimpleXMLElement $dest, SimpleXMLElement $source) {
	$new_dest = $dest->addChild($source->getName(), $source[0]);
	foreach ($source->children() as $child) {
		AddXMLElement($new_dest, $child);
	}
}
?>
