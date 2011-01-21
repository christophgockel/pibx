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
class PiBX_Runtime_Marshaller {
    /**
     * @var PiBX_Runtime_Binding
     */
    private $binding;

    /**
     * @var string
     */
    private $xml;

    public function __construct(PiBX_Runtime_Binding $binding) {
        $this->binding = $binding;
        $this->xml = '';
    }

    /**
     * Converts the given $object into its XML representation.
     * 
     * @param object $object
     * @return string
     * @throws InvalidArgumentException When the given parameter is not an object.
     */
    public function marshal($object) {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Cannot marshal a non-object');
        }

        $classToMarshal = get_class($object);
        $ast = $this->binding->getASTForClass($classToMarshal);
        $xml = $this->marshalObject($object, $ast);
        
        return $this->prettyPrint($xml);
    }

    /**
     * Converts the given parameter $object into its XML representation corresponding
     * to its AST.
     * 
     * @param object $object The object to marshal
     * @param PiBX_AST_Tree $ast object's AST
     * @return string
     */
    private function marshalObject($object, PiBX_AST_Tree $ast) {
        $astName = $ast->getName();
        $xml = '';
        
        if ($astName != '') {
            $xml .= '<' . $astName . '>';
        }

        if ( $ast instanceof PiBX_AST_Type ) {
            $xml .= $this->marshalType($object, $ast);
        } elseif ($ast instanceof PiBX_AST_Collection) {
            $xml .= $this->marshalCollection($object, $ast);
        } elseif ($ast instanceof PiBX_AST_Structure) {
            $xml .= $this->marshalStructure($object, $ast);
        } elseif ($ast instanceof PiBX_AST_TypeAttribute) {
            $xml .= $this->marshalTypeAttribute($object, $ast);
        } elseif ($ast instanceof PiBX_AST_StructureElement) {
            $xml .= $this->marshalStructureElement($object, $ast);
        } elseif (is_string($object) || ($ast instanceof PiBX_AST_CollectionItem)) {
            $xml .= $object;
        } else {
            // at the moment this is just a dummy value
            $xml .= get_class($ast);
        }

        if ($astName != '') {
            $xml .= '</' . $astName . '>';
        }
        
        return $xml;
    }

    private function marshalType($object, PiBX_AST_Type $ast) {
        $xml = '';
        
        if ($ast->hasChildren()) {
            $childrenCount = $ast->countChildren();
            for ($i = 0; $i < $childrenCount; $i++) {
                $child = $ast->get($i);
                $xml .= $this->marshalObject($object, $child);
            }
        }

        return $xml;
    }

    private function marshalCollection($object, PiBX_AST_Collection $ast) {
        $getter = $ast->getGetMethod();
        $collectionItems = $object->$getter();
        $xml = '';

        foreach ($collectionItems as &$item) {
            if ($ast->hasChildren()) {
                $childrenCount = $ast->countChildren();

                for ($i = 0; $i < $childrenCount; $i++) {
                    $child = $ast->get($i);
                    if (is_object($item)) {
                        // TODO is abstract mapping collection?
                        $classToMarshal = get_class($item);
                        $classAst = $this->binding->getASTForClass($classToMarshal);
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

        return $xml;
    }

    private function marshalStructure($object, PiBX_AST_Structure $ast) {
        $xml = '';
        
        if ($ast->getStructureType() === PiBX_AST_StructureType::CHOICE()) {
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

        return $xml;
    }

    private function marshalTypeAttribute($object, PiBX_AST_TypeAttribute $ast) {
        if ($ast->getStyle() == 'element') {
            $getter = $ast->getGetMethod();
            $value = $object->$getter();

            return $value;
        } elseif ($ast->getStyle() == 'attribute') {
            $getter = $ast->getGetMethod();
            $value = $object->$getter();

            return $value;
        } else {
            throw new RuntimeException('Invalid TypeAttribute style "'.$ast->getStyle().'"');
        }
    }

    private function marshalStructureElement($object, PiBX_AST_StructureElement $ast) {
        $getter = $ast->getGetMethod();
        $value = $object->$getter();

        return $value;
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
