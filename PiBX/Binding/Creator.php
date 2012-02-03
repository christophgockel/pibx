<?php
/**
 * Copyright (c) 2010-2012, Christoph Gockel <christoph@pibx.de>.
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
require_once 'PiBX/AST/Visitor/VisitorAbstract.php';
require_once 'PiBX/Binding/Names.php';
require_once 'PiBX/CodeGen/TypeUsage.php';
require_once 'PiBX/Util/XsdType.php';
/**
 * Traverses the AST as a Visitor to produce a binding definition.
 * 
 * @author Christoph Gockel
 */
class PiBX_Binding_Creator implements PiBX_AST_Visitor_VisitorAbstract {
    private $dom;
    private $bindingRoot;
    private $nodeStack;

    private $typeList;

    private $namespaceHasBeenAdded;
    
    public function __construct(array $typeList) {
        $this->typeList = $typeList;
        $this->nodeStack = array();
        $this->namespaceHasBeenAdded = false;
        
        $this->dom = new DomDocument('1.0');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;
        
        $this->bindingRoot = $this->dom->createElement('binding');
        
        $this->dom->appendChild($this->bindingRoot);
        array_push($this->nodeStack, $this->bindingRoot);
    }
    
    public function getXml() {
        return $this->dom->saveXML();
    }
    public function visitCollectionEnter(PiBX_AST_Collection $collection) {
        $name = $collection->getName();
        $getter = PiBX_Binding_Names::createGetterNameFor($collection);
        $setter = PiBX_Binding_Names::createSetterNameFor($collection);
        $node = $this->dom->createElement('collection');
        
        $node->setAttribute('name', $name);
        $node->setAttribute('get-method', $getter);
        $node->setAttribute('set-method', $setter);
        
        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];
            $lastNode->appendChild($node);
        array_push($this->nodeStack, $node);
        
        return true;
    }
    public function visitCollectionLeave(PiBX_AST_Collection $collection) {
        array_pop($this->nodeStack);
        
        return true;
    }

    public function visitCollectionItem(PiBX_AST_CollectionItem $collectionItem) {
        $name = $collectionItem->getName();
        $type = $collectionItem->getType();

        if (PiBX_Util_XsdType::isBaseType($type)) {
            $node = $this->dom->createElement('value');
            $node->setAttribute('style', 'element');
            $node->setAttribute('name', $name);
            $node->setAttribute('type', $type);
        } else {
            $node = $this->dom->createElement('structure');
            $node->setAttribute('map-as', $type);
            $node->setAttribute('name', $name);
        }

        if ( !($collectionItem->getParent() instanceof PiBX_AST_Collection) ) {
            // create an anonymous collection whenever there is no parent Collection
            // which would handle this
            $parentNode = $this->dom->createElement('collection');
            $getter = PiBX_Binding_Names::createGetterNameFor($collectionItem);
            $setter = PiBX_Binding_Names::createSetterNameFor($collectionItem);

            $parentNode->setAttribute('get-method', $getter);
            $parentNode->setAttribute('set-method', $setter);
            $parentNode->appendChild($node);
            // the parent node gets inserted on-the-fly
            $node = $parentNode;
        }

        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];
        $lastNode->appendChild($node);
        
        return true;
    }

    public function visitEnumerationEnter(PiBX_AST_Enumeration $enumeration) {
        $name = $enumeration->getName();
        $getter = PiBX_Binding_Names::createGetterNameFor($enumeration);
        $setter = PiBX_Binding_Names::createSetterNameFor($enumeration);

        $node = $this->dom->createElement('value');
        $node->setAttribute('style', 'element');
        $node->setAttribute('name', $name);
        $node->setAttribute('get-method', $getter);
        $node->setAttribute('set-method', $setter);

        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];
        $lastNode->appendChild($node);

        array_push($this->nodeStack, $node);


        return true;
    }
    public function visitEnumerationLeave(PiBX_AST_Enumeration $enumeration) {
        array_pop($this->nodeStack);
        
        return true;
    }

    public function visitEnumerationValue(PiBX_AST_EnumerationValue $enumerationValue) {
        return true;
    }

    public function visitStructureEnter(PiBX_AST_Structure $structure) {
        $name = $structure->getName();
        $type = $structure->getType();

        $node = $this->dom->createElement('structure');

        if ($structure->getStructureType() == PiBX_AST_StructureType::CHOICE()) {
            $node->setAttribute('ordered', 'false');
            $node->setAttribute('choice', 'true');
        } elseif ($structure->getStructureType() == PiBX_AST_StructureType::STANDARD()) {
            if ($type != '') {
                // // a standard structure with a given type is a reference to that type
                $getter = PiBX_Binding_Names::createGetterNameFor($structure);
                $setter = PiBX_Binding_Names::createSetterNameFor($structure);

                $node->setAttribute('map-as', $type);
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
                $node->setAttribute('name', $name);
            } else {
                $node->setAttribute('name', $name);
            }
        }

        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];

        if ($name != '' && $structure->getStructureType() != PiBX_AST_StructureType::STANDARD()) {
            $parentNode = $this->dom->createElement('structure');
            $parentNode->setAttribute('name', $name);
            $parentNode->appendChild($node);

            $lastNode->appendChild($parentNode);
            $lastNode = $parentNode;
        }

        $lastNode->appendChild($node);


        array_push($this->nodeStack, $node);
        
        return true;
    }
    public function visitStructureLeave(PiBX_AST_Structure $structure) {
        array_pop($this->nodeStack);
        
        return true;
    }

    public function visitStructureElementEnter(PiBX_AST_StructureElement $structureElement) {
        $name = $structureElement->getName();
        $type = $structureElement->getType();
        $getter = PiBX_Binding_Names::createGetterNameFor($structureElement);
        $setter = PiBX_Binding_Names::createSetterNameFor($structureElement);
        $tester = PiBX_Binding_Names::createTestFunctionFor($structureElement);

        $node = $this->dom->createElement('value');

        $node->setAttribute('style', 'element');
        $node->setAttribute('name', $name);

        $parent = $structureElement->getParent();
        if ($parent instanceof PiBX_AST_Structure) {
            if ($parent->getStructureType() == PiBX_AST_StructureType::CHOICE()) {
                $node->setAttribute('test-method', $tester);
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
                $node->setAttribute('usage', 'optional');
            } else {
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
            }
        }
        
        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];
        $lastNode->appendChild($node);
        array_push($this->nodeStack, $node);
        
        return true;
    }
    public function visitStructureElementLeave(PiBX_AST_StructureElement $structureElement) {
        array_pop($this->nodeStack);
        return true;
    }

    public function visitTypeEnter(PiBX_AST_Type $type) {
        $name = $type->getName();
        $typesType = $type->getType();
        $className = PiBX_Binding_Names::createClassnameFor($name);

        $this->checkAndAddNamespace($type);

        if ($type->isEnumerationType()) {
            $label = PiBX_Binding_Names::createClassnameFor($name);
            $typeName = $label;
            $nameUsage = $this->getNameUsagesRegardlessOfCase($label);

            if ($nameUsage > 1) {
                $typeName = $typeName . ($nameUsage - 1);
            }
            
            $node = $this->dom->createElement('format');
            $node->setAttribute('label', $label);
            $node->setAttribute('type', $typeName);
            $node->setAttribute('enum-value-method', 'toString');
            $this->bindingRoot->appendChild($node);
            // children are irrelevant for the binding defintion
            return false;
        }
        
        $node = $this->dom->createElement('mapping');
        
        $node->setAttribute('class', $className);
        
        if ($type->isRoot()) {
            $node->setAttribute('name', $name);
        } else {
            $node->setAttribute('abstract', 'true');
            
            if ($type->getValueStyle() == 'attribute') {
                $node->setAttribute('type-name', PiBX_Binding_Names::createClassnameFor($name));
            } else {
                $node->setAttribute('type-name', $name);
            }
        }

        if ($type->hasBaseType()) {
            // when a base-type is available, the "inheritance" hierarchy is
            // expressed via a mapped structure
            $subnode = $this->dom->createElement('structure');
            $subnode->setAttribute('map-as', $type->getBaseType());
            $node->appendChild($subnode);
        }
        
        if ($typesType != '') {
            $referencedType = $this->getTypeByName($typesType);
            
            // a base-type will be mapped directly
            if (PiBX_Util_XsdType::isBaseType($typesType)) {
                $getter = PiBX_Binding_Names::createGetterNameFor($type);
                $setter = PiBX_Binding_Names::createSetterNameFor($type);
                
                $subnode = $this->dom->createElement('value');

                $style = 'text';
                if ($type->getValueStyle() == 'attribute') {
                    $style = 'attribute';
                }
                $subnode->setAttribute('style',      $style);
                $subnode->setAttribute('get-method', $getter);
                $subnode->setAttribute('set-method', $setter);
                
                $node->appendChild($subnode);
            } elseif ( !$type->hasChildren() && $referencedType->isEnumerationType()) {
                $getter = PiBX_Binding_Names::createGetterNameFor($type);
                $setter = PiBX_Binding_Names::createSetterNameFor($type);

                $subnode = $this->dom->createElement('value');

                $subnode->setAttribute('style', 'text');
                $subnode->setAttribute('get-method', $getter);
                $subnode->setAttribute('set-method', $setter);

                $subnode->setAttribute('format', $typesType);

                $node->appendChild($subnode);
            } else {
                // another complex-type has to be referenced
                $referencedType = PiBX_Binding_Names::createClassnameFor($type);

                $subnode = $this->dom->createElement('structure');

                $subnode->setAttribute('map-as', $referencedType);
                
                $node->appendChild($subnode);
            }
        }
        
        $this->bindingRoot->appendChild($node);
        array_push($this->nodeStack, $node);

        return true;
    }
    public function visitTypeLeave(PiBX_AST_Type $type) {
        array_pop($this->nodeStack);
        return true;
    }

    private function checkAndAddNamespace(PiBX_AST_Type $type) {
        if ($this->namespaceHasBeenAdded) {
            return;
        }

        $targetNamespace = $type->getTargetNamespace();
        
        if ($targetNamespace != '') {
            $node = $this->dom->createElement('namespace');
            $node->setAttribute('uri', $targetNamespace);
            $node->setAttribute('default', 'elements');
            
            $availableNamespaces = $type->getNamespaces();
            $prefixKey = array_search($targetNamespace, $availableNamespaces);
            
            if ($prefixKey !== false && $prefixKey != '') {
                $node->setAttribute('prefix', $prefixKey);
            }
            
            $this->bindingRoot->appendChild($node);
            
            $this->namespaceHasBeenAdded = true;
        }
    }

    public function visitTypeAttributeEnter(PiBX_AST_TypeAttribute $typeAttribute) {
        $name = $typeAttribute->getName();
        $type = $typeAttribute->getType();
        $style = $typeAttribute->getStyle();
        $getter = PiBX_Binding_Names::createGetterNameFor($typeAttribute);
        $setter = PiBX_Binding_Names::createSetterNameFor($typeAttribute);

        if ($name == '') {
            $node = $this->dom->createElement('structure');
            $referencedType = $typeAttribute->getParent();
            $typeOfReferencedType = $type;
            $nameOfReferencedType = PiBX_Binding_Names::createClassnameFor($type);
            $referencedType = $this->getTypeByName($type);

            $deep = $this->getDeepestReferencedType($typeAttribute);
            
            $shouldBeMapped = $this->anonymTypeAttributeShouldBeMapped($type);

            if ($referencedType->getValueStyle() == 'attribute') {
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
                $node->setAttribute('usage', 'optional');
                    $subnode = $this->dom->createElement('value');
                    $subnode->setAttribute('style', 'attribute');
                    $subnode->setAttribute('name', $type);
                    $subnode->setAttribute('ns', $referencedType->getTargetNamespace());
                    $subnode->setAttribute('get-method', $getter);
                    $subnode->setAttribute('set-method', $setter);
                    $subnode->setAttribute('usage', 'optional');
                $node->appendChild($subnode);

            } else {
                if ($shouldBeMapped == false) {
                    // base types do not need to be mapped via a structure
                    $node->setAttribute('type', $nameOfReferencedType);
                    $node->setAttribute('get-method', $getter);
                    $node->setAttribute('set-method', $setter);
                } else {
                    $node->setAttribute('map-as', $nameOfReferencedType);
                    $node->setAttribute('get-method', $getter);
                    $node->setAttribute('set-method', $setter);
                    $node->setAttribute('name', $type);
                }
            }
        } else {
            if ($type == '' || PiBX_Util_XsdType::isBaseType($type) || $typeAttribute->getStyle() == 'attribute') {
                $node = $this->dom->createElement('value');


                $node->setAttribute('style', $style);
                $node->setAttribute('name', $name);
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
            } else {
                $getter = PiBX_Binding_Names::createGetterNameFor($typeAttribute);
                $setter = PiBX_Binding_Names::createSetterNameFor($typeAttribute);

                $node = $this->dom->createElement('structure');
                $node->setAttribute('map-as', $type);
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
                $node->setAttribute('name', $name);
            }

            if ($typeAttribute->isOptional()) {
                $node->setAttribute('usage', 'optional');
            }

            $referencedType = $this->getTypeByName($type);
            if ($referencedType && !$referencedType->isStandardType()) {
                $node->setAttribute('format', $type);
            }
        }
        
        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];
        $lastNode->appendChild($node);
        array_push($this->nodeStack, $lastNode);
        
        return true;
    }
    public function visitTypeAttributeLeave(PiBX_AST_TypeAttribute $typeAttribute) {
        array_pop($this->nodeStack);
        
        return true;
    }

    private function anonymTypeAttributeShouldBeMapped($name) {
        if (PiBX_Util_XsdType::isBaseType($name)) {
            $shouldBeMapped = false;
            return false;
        }

        $type = $this->getTypeByName($name);
        
        do {
            $typeName = $type->getType();
            $name = $type->getName();
            
            if ($typeName == '' || PiBX_Util_XsdType::isBaseType($typeName)) {
                return false;
            }

            $test = $this->getTypeByName($typeName);
            
            if ($test && $test->isEnumerationType()) {
                return false;
            }
            
            $type = $this->getTypeByTypeName($typeName);
        } while ($type && $typeName != '');

        return true;
    }

    /**
     * Walks down the type-list and returns the last element of chained types.
     * @return string
     */
    private function getDeepestReferencedType(PiBX_AST_Tree $type) {
        if (PiBX_Util_XsdType::isBaseType($type->getType())) {
            return $type;
        }

        $nextType = $this->getRootTypeByName($type->getType());
        if ($nextType == null) {
            return $type;
        }
        if ($nextType->getType() == '') {
            return $nextType;
        }

        return $this->getDeepestType($nextType);
    }

    private function getDeepestType(PiBX_AST_Tree $tree) {
        if (PiBX_Util_XsdType::isBaseType($tree->getType())) {
            return $tree;
        }

        $nextType = $this->getNonRootTypeByName($tree->getType());

        if ($nextType == null) {
            return $tree;
        }
        if ($nextType == null || $nextType->getType() == '') {
            return $nextType;
        }

        return $this->getDeepestType($nextType);
    }

    private function getRootTypeByName($typeName) {
        if ($typeName == '') {
            return null;
        }
        foreach ($this->typeList as &$type) {
            if (!$type->isRoot()) {
                continue;
            }

            if ($type->getName() == $typeName) {
                return $type;
            }
        }
        
        return null;
    }
    private function getNonRootTypeByName($typeName) {
        if ($typeName == '') {
            return null;
        }
        foreach ($this->typeList as &$type) {
            if ($type->isRoot()) {
                continue;
            }

            if ($type->getName() == $typeName) {
                return $type;
            }
        }

        return null;
    }


    private function getTypeByName($typeName) {
        if ($typeName == '') {
            return null;
        }
        foreach ($this->typeList as &$type) {
//            if (!$type->isRoot()) {
//                continue;
//            }
            
            if ($type->getName() == $typeName) {
                return $type;
            }
        }
//        return $type;
        return null;
    }

    private function getTypeByTypeName($typeName) {
        if ($typeName == '') {
            return null;
        }

        foreach ($this->typeList as &$type) {
            if ( $type->isRoot() ) {
                continue;
            }

            if ($type->getType() == $typeName) {
                return $type;
            }
        }

        return null;
    }
    
    private function getNameUsagesRegardlessOfCase($typeName) {
        $lowerCaseTypeName = strtolower($typeName);
        $usage = 0;

        foreach ($this->typeList as &$type) {
            if (strtolower($type->getName()) == $lowerCaseTypeName) {
                $usage++;
            }
        }

        return $usage;
    }
}
