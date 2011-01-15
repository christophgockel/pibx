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
require_once 'PiBX/AST/Visitor/VisitorAbstract.php';
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
class PiBX_Runtime_Marshaller {
    /**
     * @var PiBX_Runtime_Binding
     */
    private $binding;

    /**
     * @var string
     */
    private $xml;

    /**
     * @var PiBX_AST_Tree[] Holds all ASTs of the current Binding
     */
    private $asts;

    public function __construct(PiBX_Runtime_Binding $binding) {
        $this->binding = $binding;
        $this->xml = '';
        $this->asts = array();
    }

    public function marshal($object) {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Cannot marshal a non-object');
        }
        
        $classToMarshal = get_class($object);
        $ast = $this->getASTForClass($classToMarshal);
        $xml = $this->marshalObject($object, $ast);
        
        return $this->prettyPrint($xml);
    }

    private function marshalObject($object, PiBX_AST_Tree $ast) {
        $astName = $ast->getName();
        $xml = '';
        
        if ($astName != '') {
            $xml .= '<' . $astName . '>';
        }

        if ( $ast instanceof PiBX_AST_Type ) {
            if ($ast->hasChildren()) {
                $childrenCount = $ast->countChildren();
                for ($i = 0; $i < $childrenCount; $i++) {
                    $child = $ast->get($i);
                    $xml .= $this->marshalObject($object, $child);
                }
            }
        } elseif ($ast instanceof PiBX_AST_Collection) {
            $getter = $ast->getGetMethod();
            $collectionItems = $object->$getter();

            foreach ($collectionItems as &$item) {
                if ($ast->hasChildren()) {
                    $childrenCount = $ast->countChildren();

                    for ($i = 0; $i < $childrenCount; $i++) {
                        $child = $ast->get($i);
                        if (is_object($item)) {
                            // TODO is abstract mapping collection?
                            $classToMarshal = get_class($item);
                            $classAst = $this->getASTForClass($classToMarshal);
                            $structureName = $child->getName();
                            $classAst->setName($structureName);

                            $xml .= $this->marshalObject($item, $classAst);
                        } else {
                            // this collection is just a list of (scalar) values
                            $xml .= $this->marshalObject($item, $child);
                        }
                    }
                }
            }
        } elseif ($ast instanceof PiBX_AST_Structure) {
            if ($ast->getType() === PiBX_AST_StructureType::CHOICE()) {
                if ($ast->hasChildren()) {
                    $childrenCount = $ast->countChildren();
                    $currentChoice = null;
                    
                    for ($i = 0; $i < $childrenCount; $i++) {
                        $child = $ast->get($i);

                        $testMethod = $child->getTestMethod();

                        if ($object->$testMethod()) {
                            $currentChoice = $child;
                            break;
                        }
                    }

                    $xml .= $this->marshalObject($object, $currentChoice);
                }
            }
        } elseif ($ast instanceof PiBX_AST_TypeAttribute) {
            $getter = $ast->getGetMethod();
            $value = $object->$getter();
            $xml .= $value;
        } elseif ($ast instanceof PiBX_AST_StructureElement) {
            // currently only one choice is supported.
            $child = $ast->get(0);

            $getter = $child->getGetMethod();
            $value = $object->$getter();
            $xml .= $this->marshalObject($object, $child);
        } elseif (is_string($object) || ($ast instanceof PiBX_AST_CollectionItem)) {
            $xml .= $object;
        } else {
            $xml .= get_class($ast);
        }

        if ($astName != '') {
            $xml .= '</' . $astName . '>';
        }
        
        return $xml;
    }

    /**
     * Returns the corresponding AST for a given classname.
     * 
     * @param $classname string The classname
     * @return PiBX_AST_Tree
     * @throws RuntimeException When no AST can be found for the given classname
     */
    private function getASTForClass($classname) {
        if (count($this->asts) == 0) {
            $this->asts = $this->binding->parse();
        }

        foreach ($this->asts as &$ast) {
            $name = $ast->getClassName();

            if ($name == $classname) {
                return $ast;
            }
        }

        throw new RuntimeException('Couldn\'t find AST for class "' . $classname . '"');
    }

    /**
     * Creates a pretty printed XML string.
     * 
     * @param $xml string
     * @return string
     */
    private function prettyPrint($xml) {
        $x = new SimpleXMLElement($xml);
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($x->asXML());

        return trim($dom->saveXML());
    }
}
