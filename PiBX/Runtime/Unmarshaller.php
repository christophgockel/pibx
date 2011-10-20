<?php
/**
 * Copyright (c) 2010-2011, Christoph Gockel <christoph@pibx.de>.
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
require_once 'PiBX/Util/XsdType.php';
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
    private function parseXml(SimpleXMLElement $xml, PiBX_AST_Tree $ast, $parentObject) {
        $count = $ast->countChildren();
        
        if (!$xml->children()) {
            // a leaf node in the XML documents doesn't need to be parsed any further
            return (string) $xml;
        }

        if ($count == 0) {
            if ($ast instanceof PiBX_AST_Structure) {
                $newObject = $this->parseStructure($xml, $ast, $parentObject);
            } else {
                throw new RuntimeException('Not supported yet');
            }
        } else {
        
            $newObject = $parentObject;
        
            for ($i = 0; $i < $count; $i++) {
                $child = $ast->get($i);

                $name = $child->getName();
                $childXml = $xml->$name;
                
                if ($child instanceof PiBX_AST_Structure) {
                    $newObject = $this->parseStructure($xml->{$name}, $child, $parentObject);
                } elseif ($child instanceof PiBX_AST_Collection) {
                    $name = $ast->getName();
                    $list = $this->parseCollection($xml, $child, $parentObject);
                    $setter = $child->getSetMethod();
                    $parentObject->$setter( $list );
                } elseif ($child instanceof PiBX_AST_TypeAttribute) {
                    $newObject = $this->parseTypeAttribute($xml, $child, $parentObject);
                } else {
                    throw new RuntimeException('Not supported yet');
                }
            }
        }
        return $newObject;
    }

    /**
     * 
     * @param SimpleXMLElement $xml
     * @param PiBX_AST_Structure $ast
     * @param object $parentObject
     */
    private function parseStructure(SimpleXMLElement $xml, PiBX_AST_Structure $ast, $parentObject) {
        $name = $ast->getName();
        $type = $ast->getType();

        if ($type == '') {
            if ($ast->getStructureType() === PiBX_AST_StructureType::CHOICE()) {
                return $this->parseChoiceStructure($xml, $ast, $parentObject);
            } elseif ($ast->getStructureType() === PiBX_AST_StructureType::ORDERED()) {
                throw new RuntimeException('Not supported yet.');
            } else {
                throw new RuntimeException('Invalid <structure>.');
            }
        } else { // a structure with a type is a reference to the type
            if (!PiBX_Util_XsdType::isBaseType($type)) {
                
                $structAst = $this->binding->getASTForClass($type);
                $newObject = new $type();
                
                $parsedObject = $this->parseXml($xml, $structAst, $newObject);
                $setter = $ast->getSetMethod();
                
                $parentObject->$setter( $parsedObject );
            } else {
                return $parentObject;
            }
        }

    }

    private function parseChoiceStructure(SimpleXMLElement $xml, PiBX_AST_Structure $ast, $parentObject) {
        $choiceCount = $ast->countChildren();
        
        for ($i = 0; $i < $choiceCount; $i++) {
            $child = $ast->get($i);
            $name = $child->getName();
            
            if ($xml->{$name}) {// choice in XML found
                $newObject = $this->parseXml($xml->{$name}, $child, $parentObject);
                $setter = $child->getSetMethod();
                
                $parentObject->$setter( $newObject );
                
                break;
            }
            
        }
        
        return $parentObject;
    }

    private function parseTypeAttribute(SimpleXMLElement $xml, PiBX_AST_TypeAttribute $ast, $parentObject) {
        $name = $ast->getName();
        
        if ($ast->getStyle() == 'element') {
            $value = (string) $xml->$name;

        } elseif ($ast->getStyle() == 'attribute') {
            $attributes = $xml->attributes();
            $value = (string) $attributes[$name];
        }
        $setter = $ast->getSetMethod();

        $parentObject->$setter( $value );
        
        return $parentObject;
    }

    /**
     *
     * @param SimpleXMLElement $xml
     * @param PiBX_AST_Collection $ast
     * @param <type> $parentObject
     * @return mixed[] The collection entries
     */
    private function parseCollection(SimpleXMLElement $xml, PiBX_AST_Collection $ast, $parentObject) {
        if ($ast->getName() == '') {
            return $this->parseAnonymCollection($xml, $ast, $parentObject);
        } else {
            return $this->parseNamedCollection($xml, $ast, $parentObject);
        }
    }

    /**
     *
     * @param SimpleXMLElement $xml
     * @param PiBX_AST_Collection $ast
     * @param <type> $parentObject
     * @return mixed[] The collection entries
     */
    private function parseNamedCollection(SimpleXMLElement $xml, PiBX_AST_Collection $ast, $parentObject) {
        $count = $ast->countChildren();
        $list = array();
        $collectionName = $ast->getName();
        
        for ($i = 0; $i < $count; $i++) {
            $child = $ast->get($i);

            if ($child instanceof PiBX_AST_Structure) {
                $name = $child->getName();
                $structAst = $this->binding->getASTForName($name);
                $class = $structAst->getType();
                
                // somehow xpath didn't work well with node names containing a dash
                // so fetch the nodes with "{}", e.g. "{'node-containing-a-dash'}"
                $itemCount = count($xml->{$collectionName}->{$name});
                
                if ($itemCount > 0) {
                    // collection items/nodes are directly in the current node
                    for ($j = 0; $j < $itemCount; $j++) {
                        $newObject = new $class();
                        $listNode = $xml->{$collectionName}->{$name}[$j];
                        
                        $list[] = $this->parseXml($listNode, $structAst, $newObject);
                    }
                }
            } elseif ($child instanceof PiBX_AST_CollectionItem) {
                $name = $child->getName();
                $itemCount = count($xml->{$collectionName}->{$name});

                if ($itemCount > 0) {
                    for ($j = 0; $j < $itemCount; $j++) {
                        $listNode = $xml->{$collectionName}->{$name}[$j];

                        $list[] = $this->parseXml($listNode, $ast, $parentObject);
                    }
                }
            } else {
                throw new RuntimeException('Invalid <collection>');
            }
        }
        
        return $list;
    }

    /**
     * 
     * @param SimpleXMLElement $xml
     * @param PiBX_AST_Collection $ast
     * @param <type> $parentObject
     * @return mixed[] The collection entries
     */
    private function parseAnonymCollection(SimpleXMLElement $xml, PiBX_AST_Collection $ast, $parentObject) {
        $count = $ast->countChildren();
        $list = array();

        for ($i = 0; $i < $count; $i++) {
            $child = $ast->get($i);

            if ($child instanceof PiBX_AST_Structure) {
                $name = $child->getName();
                $structAst = $this->binding->getASTForName($name);
                $class = $structAst->getType();

                // somehow xpath didn't work well with node names containing a dash
                // so fetch the nodes with "{}", e.g. "{'node-containing-a-dash'}"
                $itemCount = count($xml->{$name});
                
                if ($itemCount > 0) {
                    // collection items/nodes are directly in the current node
                    for ($j = 0; $j < $itemCount; $j++) {
                        $newObject = new $class();
                        $listNode = $xml->{$name}[$j];
                        
                        $list[] = $this->parseXml($listNode, $structAst, $newObject);
                    }
                }
            } else {
                throw new RuntimeException('Invalid <collection>');
            }
        }
        
        return $list;
    }
}
