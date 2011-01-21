<?php
/**
 * Copyright (c) 2010, Christoph Gockel.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * * Neither the name of PiBX nor the names of its contributors may be used
 *   to endorse or promote products derived from this software without specific
 *   prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
require_once 'PiBX/AST/Tree.php';
require_once 'PiBX/AST/Collection.php';
require_once 'PiBX/AST/CollectionItem.php';
require_once 'PiBX/AST/Enumeration.php';
require_once 'PiBX/AST/EnumerationValue.php';
require_once 'PiBX/AST/Structure.php';
require_once 'PiBX/AST/StructureElement.php';
require_once 'PiBX/AST/StructureType.php';
require_once 'PiBX/AST/Type.php';
require_once 'PiBX/AST/TypeAttribute.php';
require_once 'PiBX/Runtime/Binding.php';
/**
 * The Marshaller is responsible to serialize a given object-structure into
 * a string representation.
 * This string can also be written directly into an output-file, if it's a
 * root level element/object.
 *
 * @author Christoph Gockel
 */
class PiBX_Runtime_Unmarshaller {
    /**
     * @var PiBX_Runtime_Binding
     */
    private $binding;

    public function __construct(PiBX_Runtime_Binding $binding) {
        $this->binding = $binding;
    }

    public function unmarshal($string) {
        if (!is_string($string)) {
            throw new InvalidArgumentException('Cannot unmarshal a non-string');
        }

        $xml = simplexml_load_string($string);

        // TODO maybe restrict to only one element here?
        //      since there can be only one root-element anyway
        list($rootNode) = $xml->xpath('/*');

        $attributes = $rootNode->attributes();
        $name = $rootNode->getName();
        $ast = $this->binding->getASTForName($name);
        $class = $ast->getType();
        
        $object = new $class();

        $this->parseXml($rootNode, $ast, $object);
        
        return $object;
    }

    /**
     * Parses the XML data and returns the object-structure represented by the XML.
     *
     * @param SimpleXMLElement $xml The current XML node
     * @param PiBX_AST_Tree $ast The XML corresponding AST subtree
     * @param object $parentObject The object that contains the element described by $xml and $ast
     * @return mixed string or object, dependent on the current $xml and $ast.
     *               If the current $xml is a leaf node <code>parseXml()</code> returns the literal
     *               value of $xml. When it's composite node, it returns the object-structure
     *               reflecting the $xml data.
     */
    private function parseXml(SimpleXMLElement $xml, PiBX_AST_Tree $ast = null, $parentObject = null) {
        $nodes = $xml->xpath('./*');

        if (count($nodes) == 0) {
            // simple value (leaf)
            return (string)$xml;
        }

        foreach ($nodes as &$node) {
            $name = $node->getName();

            $ast = $this->binding->getASTForName($name);
            
            if ($ast instanceof PiBX_AST_TypeAttribute) {
                $parentObject = $this->parseXmlOfTypeAttribute($node, $ast, $parentObject);
            } elseif ($ast instanceof PiBX_AST_Collection) {
                $parentObject = $this->parseXmlOfCollection($node, $ast, $parentObject);
            } elseif ($ast instanceof PiBX_AST_Structure) {
                if ($ast->getStructureType() === PiBX_AST_StructureType::CHOICE()) {
                    // in a structure we can just recurse down
                    $parentObject = $this->parseXml($node, $ast, $parentObject);
                    
                } else {
                    throw new RuntimeException("Currently only choice elements are supported.");
                }
            } elseif ($ast instanceof PiBX_AST_StructureElement) {
                $parentObject = $this->parseXmlOfStructureElement($node, $ast, $parentObject);
            }
        }
        
        return $parentObject;
    }

    /**
     * In a collection it is necessary to parse all containing child elements.
     * 
     * @param SimpleXMLElement $xml
     * @param PiBX_AST_Collection $ast
     * @param object $parentObject
     * @return mixed
     */
    private function parseXmlOfCollection(SimpleXMLElement $xml, PiBX_AST_Collection $ast, $parentObject) {
        $setter = $ast->getSetMethod();
        $collectionItems = array();
        $childNodes = $xml->xpath('./*');

        foreach ($childNodes as &$childNode) {
            $childName = $childNode->getName();
            $childAst = $this->binding->getASTForName($childName);

            $typename = $childAst->getType();
            $class = $this->binding->getClassnameForName($typename);

            if ($class != '') {
                $newObject = new $class();
            } else {
                $newObject = $parentObject;
            }
            
            $item = $this->parseXml($childNode, $ast, $newObject);
            $collectionItems[] = $item;
        }
        
        $parentObject->$setter($collectionItems);

        return $parentObject;
    }

    /**
     *
     * @param SimpleXMLElement $xml
     * @param PiBX_AST_TypeAttribute $ast
     * @param object $parentObject
     * @return mixed
     */

    private function parseXmlOfTypeAttribute(SimpleXMLElement $xml, PiBX_AST_TypeAttribute $ast, $parentObject) {
        $setter = $ast->getSetMethod();
        $value = $this->parseXml($xml, $ast, $parentObject);
        $parentObject->$setter($value);

        return $parentObject;
    }

    /**
     *
     * @param SimpleXMLElement $xml
     * @param PiBX_AST_StructureElement $ast
     * @param object $parentObject
     * @return mixed
     */
    private function parseXmlOfStructureElement(SimpleXMLElement $xml, PiBX_AST_StructureElement $ast, $parentObject) {
        $setter = $ast->getSetMethod();
        $value = $this->parseXml($xml, $ast, $parentObject);

        $parentObject->$setter($value);

        return $parentObject;
    }
}
