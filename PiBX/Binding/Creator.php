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
    public function visitCollectionEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);
        $setter = PiBX_Binding_Names::createSetterNameFor($tree);
        $node = $this->dom->createElement('collection');
        
        $node->setAttribute('name', $name);
        $node->setAttribute('get-method', $getter);
        $node->setAttribute('set-method', $setter);
        
        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];
            $lastNode->appendChild($node);
        array_push($this->nodeStack, $node);
        
        return true;
    }
    public function visitCollectionLeave(PiBX_AST_Tree $tree) {
        array_pop($this->nodeStack);
        
        return true;
    }

    public function visitCollectionItem(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $type = $tree->getType();

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

        if ( !($tree->getParent() instanceof PiBX_AST_Collection) ) {
            // create an anonymous collection whenever there is no parent Collection
            // which would handle this
            $parentNode = $this->dom->createElement('collection');
            $getter = PiBX_Binding_Names::createGetterNameFor($tree);
            $setter = PiBX_Binding_Names::createSetterNameFor($tree);

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

    public function visitEnumerationEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);
        $setter = PiBX_Binding_Names::createSetterNameFor($tree);

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
    public function visitEnumerationLeave(PiBX_AST_Tree $tree) {
        array_pop($this->nodeStack);
        return true;
    }
    public function visitEnumeration(PiBX_AST_Tree $tree) {
        return true;
    }

    public function visitEnumerationValue(PiBX_AST_Tree $tree) {
        return true;
    }

    public function visitStructureEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $type = $tree->getType();
        
        $node = $this->dom->createElement('structure');
        $node->setAttribute('name', $name);
        $subnode = null;

        if ($tree->getStructureType() == PiBX_AST_StructureType::CHOICE()) {
            $subnode = $this->dom->createElement('structure');
            
            $subnode->setAttribute('ordered', 'false');
            $subnode->setAttribute('choice', 'true');
            $node->appendChild($subnode);
        } elseif ($tree->getStructureType() == null ) {//TODO: implement "null case" with PiBX_AST_StructureType::NONE()
        }
        
        $lastNode = $this->nodeStack[ count($this->nodeStack) - 1 ];
        
        $lastNode->appendChild($node);
        

        if ($tree->getStructureType() == PiBX_AST_StructureType::CHOICE()) {
            array_push($this->nodeStack, $node, $subnode);
        } else {
            array_push($this->nodeStack, $node);
        }
        return true;
    }
    public function visitStructureLeave(PiBX_AST_Tree $tree) {
        array_pop($this->nodeStack);

        if ($tree->getStructureType() !== null) {//TODO: implement "null case" with PiBX_AST_StructureType::NONE()
            // on every non-default structure, a second level has been added,
            // which has to be removed as well
            array_pop($this->nodeStack);
        }
        
        return true;
    }

    public function visitStructureElementEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $type = $tree->getType();
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);
        $setter = PiBX_Binding_Names::createSetterNameFor($tree);
        $tester = PiBX_Binding_Names::createTestFunctionFor($tree);

        $node = $this->dom->createElement('value');

        $node->setAttribute('style', 'element');
        $node->setAttribute('name', $name);

        $parent = $tree->getParent();
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
    public function visitStructureElementLeave(PiBX_AST_Tree $tree) {
        array_pop($this->nodeStack);
        return true;
    }

    public function visitTypeEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $type = $tree->getType();
        $className = PiBX_Binding_Names::createClassnameFor($name);

        $this->checkAndAddNamespace($tree);

        if ($tree->isEnumerationType()) {
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
        
        if ($tree->isRoot()) {
            $node->setAttribute('name', $name);
        } else {
            $node->setAttribute('abstract', 'true');
            $node->setAttribute('type-name', PiBX_Binding_Names::createClassnameFor($name));
        }

        if ($tree->hasBaseType()) {
            // when a base-type is available, the "inheritance" hierarchy is
            // expressed via a mapped structure
            $subnode = $this->dom->createElement('structure');
            $subnode->setAttribute('map-as', $tree->getBaseType());
            $node->appendChild($subnode);
        }
        
        if ($type != '') {
            $referencedType = $this->getTypeByName($type);
            
            // a base-type will be mapped directly
            if (PiBX_Util_XsdType::isBaseType($type)) {
                $getter = PiBX_Binding_Names::createGetterNameFor($tree);
                $setter = PiBX_Binding_Names::createSetterNameFor($tree);
                
                $subnode = $this->dom->createElement('value');

                $style = 'text';
                if ($tree->getValueStyle() == 'attribute') {
                    $style = 'attribute';
                }
                $subnode->setAttribute('style',      $style);
                $subnode->setAttribute('get-method', $getter);
                $subnode->setAttribute('set-method', $setter);
                
                $node->appendChild($subnode);
            } elseif ( !$tree->hasChildren() && (/*!PiBX_Util_XsdType::isBaseType($type) ||*/ $referencedType->isEnumerationType()) ) {
                $getter = PiBX_Binding_Names::createGetterNameFor($tree);
                $setter = PiBX_Binding_Names::createSetterNameFor($tree);

                $subnode = $this->dom->createElement('value');

                $subnode->setAttribute('style', 'text');
                $subnode->setAttribute('get-method', $getter);
                $subnode->setAttribute('set-method', $setter);

                $subnode->setAttribute('format', $type);

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
    public function visitTypeLeave(PiBX_AST_Tree $tree) {
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

    public function visitTypeAttributeEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $type = $tree->getType();
        $style = $tree->getStyle();
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);
        $setter = PiBX_Binding_Names::createSetterNameFor($tree);

        if ($name == '') {
            $node = $this->dom->createElement('structure');
            $referencedType = $tree->getParent();
            $typeOfReferencedType = $type;
            $nameOfReferencedType = PiBX_Binding_Names::createClassnameFor($type);
            $referencedType = $this->getTypeByName($type);

            $deep = $this->getDeepestReferencedType($tree);
            
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
            if ($type == '' || PiBX_Util_XsdType::isBaseType($type) || $tree->getStyle() == 'attribute') {
                $node = $this->dom->createElement('value');


                $node->setAttribute('style', $style);
                $node->setAttribute('name', $name);
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
            } else {
                $getter = PiBX_Binding_Names::createGetterNameFor($tree);
                $setter = PiBX_Binding_Names::createSetterNameFor($tree);

                $node = $this->dom->createElement('structure');
                $node->setAttribute('map-as', $type);
                $node->setAttribute('get-method', $getter);
                $node->setAttribute('set-method', $setter);
                $node->setAttribute('name', $name);
            }

            if ($tree->isOptional()) {
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
    public function visitTypeAttributeLeave(PiBX_AST_Tree $tree) {
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
