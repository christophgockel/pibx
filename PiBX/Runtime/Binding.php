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
require_once 'PiBX/AST/Collection.php';
require_once 'PiBX/AST/CollectionItem.php';
require_once 'PiBX/AST/Enumeration.php';
require_once 'PiBX/AST/EnumerationValue.php';
require_once 'PiBX/AST/Structure.php';
require_once 'PiBX/AST/StructureElement.php';
require_once 'PiBX/AST/StructureType.php';
require_once 'PiBX/AST/Tree.php';
require_once 'PiBX/AST/Type.php';
require_once 'PiBX/AST/TypeAttribute.php';
/**
 * The PiBX_Runtime_Binding is an object-oriented approach/access to a given
 * <code>binding.xml</xml> file.
 * The Binding-class parses the <code>binding.xml</code> file. While parsing,
 * it creates an abstract syntax tree on the fly.
 * This AST can be used to serialize the object-structure later.
 *
 * @author Christoph Gockel
 */
class PiBX_Runtime_Binding {
    /**
     * @var SimpleXMLEement 
     */
    private $xml;

    /**
     * @var PiBX_AST_Tree[]
     */
    private $asts;

    /**
     * @var array Hashmap XSD-type => PHP-classname
     */
    private $classMap;

    /**
     * @var array Hashmap element-name => AST, for faster lookup of ASTs
     */
    private $elementAsts;

    /**
     * @var array Hashmap Element-name => structure-name. Needed for temporary use while parsing the binding-mapping.
     */
    private $structuralReferences;

    public function  __construct($bindingFile) {
        if ( !$this->isValidFile($bindingFile) ) {
            throw new InvalidArgumentException('"' . $bindingFile . '" is not a valid binding file.');
        }

        $this->xml = simplexml_load_file($bindingFile);
        $this->asts = null;
    }

    private function isValidFile($filename) {
        return trim($filename) != '' && file_exists($filename);
    }

    public function parse() {
        $this->asts = array();

        // starts a straight-forward schema parsing
        $this->parseBinding($this->xml);
        
        return $this->asts;
    }

    private function parseBinding(SimpleXMLElement $xml) {
        $nodes = $xml->xpath('/binding/mapping');

        foreach ($nodes as $mapping) {
            $attributes = $mapping->attributes();

            $name = (string)$attributes['name'];
            if ($name == '') {
                // abstract type?
                $name = (string)$attributes['type-name'];
            }
            $class = (string)$attributes['class'];
            
            $ast = new PiBX_AST_Type($name);
            
            $abstract = (string)$attributes['abstract'];
            if ($abstract !== 'true') {
                $ast->setAsRoot();
                $ast->setTargetNamespace($this->getTargetNamespace());
            }
            
            $ast->setType($class);
            
            $this->parseMapping($mapping, $ast);
            
            $this->asts[] = $ast;
            // update the ASTs if the current mapping is a referenced mapping.
            // i.e. an abstract mapping
            if (key_exists($name, $this->structuralReferences)) {
                $referencedName = $this->structuralReferences[$name];
                $this->elementAsts[$referencedName] = $ast;
            } else {
                $this->elementAsts[$name] = $ast;
            }
        }
    }

    private function getTargetNamespace() {
        $nodes = $this->xml->xpath('/binding/namespace');
        
        if (count($nodes) > 0) {
            // at the moment only one namespace is supported
            list($node) = $nodes;
            $attributes = $node->attributes();
            $ns = (string)$attributes['uri'];
            
            return $ns;
        }

        return '';
    }

    private function parseMapping(SimpleXMLElement $xml, PiBX_AST_Tree $part) {
        $nodes = $xml->xpath('./*');
        
        foreach ($nodes as &$child) {
            $name = (string) $child->getName();
            $attributes = $child->attributes();

            if ($name == 'collection') {
                $name = (string)$attributes['name'];
                $setter = (string)$attributes['set-method'];
                $getter = (string)$attributes['get-method'];
                
                $newPart = new PiBX_AST_Collection($name);
                $newPart->setSetMethod($setter);
                $newPart->setGetMethod($getter);

                $class = (string)$attributes['class'];
                $this->classMap[$name] = $class;
                $this->elementAsts[$name] = $newPart;
                
                $this->parseMapping($child, $newPart);
            } elseif ($name == 'structure') {
                $name = (string)$attributes['name'];

                if ($part instanceof PiBX_AST_Collection) {
                    // a structure in a collection is a reference to the actual structure
                    $referencedType = (string)$attributes['map-as'];
                    $type = $this->getClassnameForName($referencedType);
                    $newPart = new PiBX_AST_Structure($name);
                    $newPart->setType($type);
                    $this->structuralReferences[$referencedType] = $name;
                } elseif ($part instanceof PiBX_AST_Type) {
                    $referencedType = (string)$attributes['map-as'];
                    $type = $this->getClassnameForName($referencedType);

                    // a structure in a type is the structure "container"
                    $newPart = new PiBX_AST_Structure($name, $type);
                    $getMethod = (string)$attributes['get-method'];
                    $setMethod = (string)$attributes['set-method'];

                    $newPart->setGetMethod($getMethod);
                    $newPart->setSetMethod($setMethod);
                } elseif ($part instanceof PiBX_AST_Structure) {
                    $ordered = (string)$attributes['ordered'];
                    $ordered = strtolower($ordered);
                    $choice = (string)$attributes['choice'];
                    $choice = strtolower($choice);

                    if ($ordered == 'true' && $choice == 'false') {
                        $part->setStructureType(PiBX_AST_StructureType::ORDERED());
                    } elseif ($ordered == 'false' && $choice == 'true') {
                        $part->setStructureType(PiBX_AST_StructureType::CHOICE());
                    } else {
                        throw new RuntimeException('Invalid structure state!');
                    }

                    $structureValues = $child->xpath('./*');

                    // this handling is a bit clunky but needed,
                    // since the choice structures are defined
                    // somewhat redundant in the binding.xml
                    foreach ($structureValues as &$struct) {
                        $valueName = (string) $struct->getName();
                        if ($valueName != 'value') {
                            throw new RuntimeException('No value-element within the structure.');
                        }
                        $attributes = $struct->attributes();
                        $nameAttribute = (string)$attributes['name'];

                        $newPart = new PiBX_AST_StructureElement($nameAttribute);

                        $style = (string)$attributes['style'];
                        $testMethod = (string)$attributes['test-method'];
                        $getMethod = (string)$attributes['get-method'];
                        $setMethod = (string)$attributes['set-method'];
                        
                        $newPart->setStyle($style);
                        $newPart->setTestMethod($testMethod);
                        $newPart->setGetMethod($getMethod);
                        $newPart->setSetMethod($setMethod);
                        $part->add($newPart);
                        $this->elementAsts[$nameAttribute] = $newPart;
                    }
                    // no further processing needed
                    // all structure values have been added
                    break;
                }
                $this->elementAsts[$name] = $newPart;
                
                $this->parseMapping($child, $newPart);
            } elseif ($name == 'value') {
                $name = (string)$attributes['name'];
                if ($part instanceof PiBX_AST_Collection) {
                    $newPart = new PiBX_AST_CollectionItem($name);
                } elseif ($part instanceof PiBX_AST_Type) {
                    $newPart = new PiBX_AST_TypeAttribute($name);
                    $style = (string)$attributes['style'];
                    $setMethod = (string)$attributes['set-method'];
                    $getMethod = (string)$attributes['get-method'];
                    $newPart->setStyle($style);
                    $newPart->setSetMethod($setMethod);
                    $newPart->setGetMethod($getMethod);
                }
                $this->elementAsts[$name] = $newPart;
            } else {
                throw new InvalidArgumentException('Unexpected binding element "' . $name . '"');
            }
            
            $part->add($newPart);
        }
    }

    /**
     * Returns the marshalling classname for a given binding name (which
     * is the name of the XSD-type).
     * 
     * @param $name The XSD-type name or binding-element name
     * @return string The PHP classname or empty string if $name can't be found.
     */
    public function getClassnameForName($name) {
        if (trim($name) == '') {
            return '';
        }
        // first lookup in global types
        $typeNodes = $this->xml->xpath('/binding/mapping[@name="'.$name.'"]');

        if (count($typeNodes) == 0) {
            // second lookup in abstract types
            $typeNodes = $this->xml->xpath('/binding/mapping[@type-name="'.$name.'"]');

            if (count($typeNodes) == 0) {
                return '';
            }
        }

        // we're interested in the first match only
        list($mappingElement) = $typeNodes;
        $attributes = $mappingElement->attributes();
        $classname = (string)$attributes['class'];

        return $classname;
    }

    /**
     * Tests if a given typename/classname is valid in the current binding-context.
     *
     * @param string $typename
     * @return boolean
     */
    public function isValidType($typename) {
        if (trim($typename) == '') {
            return false;
        }

        // first lookup in global types
        $typeNodes = $this->xml->xpath('/binding/mapping[@class="'.$typename.'"]');

        if (count($typeNodes) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns the AST for a given elementname.
     * 
     * @param string $elementname
     * @return PiBX_AST_Tree
     */
    public function getASTForName($elementname) {
        if (count($this->elementAsts) == 0) {
            $this->asts = $this->parse();
        }

        if (array_key_exists($elementname, $this->elementAsts)) {
            return $this->elementAsts[$elementname];
        }
        
        throw new RuntimeException('Couldn\'t find AST for element "'.$elementname.'".');
    }

    /**
     * Returns the corresponding AST for a given classname.
     *
     * @param $classname string The classname
     * @return PiBX_AST_Tree
     * @throws RuntimeException When no AST can be found for the given classname
     */
    public function getASTForClass($classname) {
        if (count($this->asts) == 0) {
            $this->asts = $this->parse();
        }

        foreach ($this->asts as &$ast) {
            $name = $ast->getType();

            if ($name == $classname) {
                return $ast;
            }
        }

        throw new RuntimeException('Couldn\'t find AST for class "' . $classname . '"');
    }
}
