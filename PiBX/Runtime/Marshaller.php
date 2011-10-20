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
     * @var DomDocument The XML document/fragment that is being generated.
     */
    private $dom;

    /**
     * @var DomElement The current node that has been, or is to be added, to the DomDocument.
     */
    private $currentDomNode;

    /**
     * @var DomElement The currents node parent.
     */
    private $parentDomNode;

    public function __construct(PiBX_Runtime_Binding $binding) {
        $this->binding = $binding;
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

        $this->dom = new DomDocument('1.0');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;

        $this->currentDomNode = $this->parentDomNode = $this->dom;
        $this->currentDomNode = $this->dom->documentElement;

        $classToMarshal = get_class($object);
        $ast = $this->binding->getASTForClass($classToMarshal);
        $xml = $this->marshalObject($object, $ast);

        return trim($this->dom->saveXML());
    }

    /**
     * Converts the given parameter $object into its XML representation corresponding
     * to its AST.
     * 
     * @param object $object The object to marshal
     * @param PiBX_AST_Tree $ast object's AST
     * @return void
     */
    private function marshalObject($object, PiBX_AST_Tree $ast) {
        $astName = $ast->getName();
        
        $lastNode = $this->currentDomNode;
        
        if ($astName != '') {
            // for every named element, a new node in the resulting xml-tree is created
            $this->currentDomNode = $this->dom->createElement($astName);
            $this->parentDomNode->appendChild($this->currentDomNode);
        } else {
            // "anonymous" elements are just chained down. No new node has to be created
            $this->currentDomNode = $this->parentDomNode;
        }

        if ($ast instanceof PiBX_AST_Type ) {
            $this->marshalType($object, $ast);
        } elseif ($ast instanceof PiBX_AST_Collection) {
            $this->marshalCollection($object, $ast);
        } elseif ($ast instanceof PiBX_AST_Structure) {
            $this->marshalStructure($object, $ast);
        } elseif ($ast instanceof PiBX_AST_TypeAttribute) {
            $this->marshalTypeAttribute($object, $ast);
        } elseif ($ast instanceof PiBX_AST_StructureElement) {
            $this->marshalStructureElement($object, $ast);
        } elseif (is_string($object) || ($ast instanceof PiBX_AST_CollectionItem)) {
            $newNode = $this->dom->createTextNode($object);
            $this->currentDomNode->appendChild($newNode);
        } else {
            throw new RuntimeException('Currently not supported: ' . get_class($ast));
        }
    }

    private function marshalType($object, PiBX_AST_Type $ast) {
        $lastNode = $this->parentDomNode;
        $this->parentDomNode = $this->currentDomNode;
        if ($ast->isRoot()) {
            $targetNamespace = $ast->getTargetNamespace();
            
            if ($targetNamespace != '') {
              $this->currentDomNode->setAttribute('xmlns', $targetNamespace);
            }
        }
        if ($ast->hasChildren()) {
            $childrenCount = $ast->countChildren();
            for ($i = 0; $i < $childrenCount; $i++) {
                $child = $ast->get($i);
                $this->marshalObject($object, $child);
            }
        }
        $this->parentDomNode = $lastNode;
    }
    
    private function marshalCollection($object, PiBX_AST_Collection $ast) {
        $getter = $ast->getGetMethod();
        $collectionItems = $object->$getter();
        $lastNode = $this->parentDomNode;
        $this->parentDomNode = $this->currentDomNode;

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

                        $this->marshalObject($item, $classAst);
                    } else {
                        // this collection is just a list of (scalar) values
                        $this->marshalObject($item, $child);
                    }
                }
            }
        }

        $this->parentDomNode = $lastNode;
    }

    private function marshalStructure($object, PiBX_AST_Structure $ast) {
        $lastNode = $this->parentDomNode;

        if ($ast->getStructureType() === PiBX_AST_StructureType::CHOICE()) {
            $this->parentDomNode = $this->currentDomNode;
            
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

                $this->marshalObject($object, $currentChoice);
            }
        } elseif ($ast->getStructureType() === PiBX_AST_StructureType::ORDERED()) {
            throw new RuntimeException('Currently not supported.');
        } else {
            $this->parentDomNode->removeChild($this->currentDomNode);
            // when a structure has no type, it is a referenced complex-type
            // used as a type-attribute
            $getter = $ast->getGetMethod();
            $obj = $object->$getter();
            $classname = $this->binding->getClassnameForName($ast->getName());
            $structureAst = $this->binding->getASTForClass($classname);
            
            $this->marshalObject($obj, $structureAst);
        }
        
        $this->parentDomNode = $lastNode;
    }

    private function marshalTypeAttribute($object, PiBX_AST_TypeAttribute $ast) {
        if ($ast->getStyle() == 'element') {
            $getter = $ast->getGetMethod();
            $value = $object->$getter();

            $newNode = $this->dom->createTextNode($value);
            $this->currentDomNode->appendChild($newNode);
        } elseif ($ast->getStyle() == 'attribute') {
            $getter = $ast->getGetMethod();
            $name = $ast->getName();
            $value = $object->$getter();
            if ($value != '') {// TODO check for "optional" attribute in binding.
                $this->parentDomNode->setAttribute($name, $value);
            }
            // in marshalObject() a child is added everytime.
            // no matter what type it is ("attribute" or "element"),
            // so we can just remove it here
            $this->parentDomNode->removeChild($this->currentDomNode);
        } else {
            throw new RuntimeException('Invalid TypeAttribute style "'.$ast->getStyle().'"');
        }
    }

    private function marshalStructureElement($object, PiBX_AST_StructureElement $ast) {
        $getter = $ast->getGetMethod();
        $value = $object->$getter();
        
        $newNode = $this->dom->createTextNode($value);
        $this->currentDomNode->appendChild($newNode);
        
        return $value;
    }
}
