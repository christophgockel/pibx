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
require_once 'PiBX/AST/Visitor/VisitorAbstract.php';
require_once 'PiBX/Binding/Names.php';
require_once 'PiBX/ParseTree/BaseType.php';
/**
 * An Binding_Creator is a Visitor of a AST.
 * It traverses a abstract-syntax-tree-structure to produce an xml output of the
 * corresponding binding.
 *
 * @author Christoph Gockel
 */
class PiBX_Binding_Creator implements PiBX_AST_Visitor_VisitorAbstract {
    private $xml;

    private $astNodes;

    private $targetNamespaceHasBeenAdded;

    private $typeList;

    public function  __construct(array $typeList) {
        $this->xml = '';
        
        $this->astNodes = array();
        $this->targetNamespaceHasBeenAdded = false;
        $this->typeList = $typeList;
    }

    public function getXml() {
        $this->xml = '<binding>' . $this->xml . '</binding>';
        $dom = new DomDocument();
        $dom->loadXML($this->xml);
        $dom->formatOutput = true;
        $formattedXml = $dom->saveXML();

        return $formattedXml;
    }

    public function visitCollectionEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getParent()->getName();
        
        $this->xml .= '<collection name="'.$name.'"';
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);
        $setter = PiBX_Binding_Names::createSetterNameFor($tree);
        $this->xml .= ' get-method="'.$getter.'"';
        $this->xml .= ' set-method="'.$setter.'"';
        $this->xml .= '>';
        
        return true;
    }
    public function visitCollectionLeave(PiBX_AST_Tree $tree) {
        $this->xml .= "</collection>";
        
        return true;
    }

    public function visitCollectionItem(PiBX_AST_Tree $tree) {
        if ($tree->getParent() instanceof PiBX_AST_TypeAttribute) {
            // a sequence of items (not a named list)
            $name = $tree->getParent()->getName();

            $this->xml .= '<collection';
            $getter = PiBX_Binding_Names::createGetterNameFor($tree);
            $setter = PiBX_Binding_Names::createSetterNameFor($tree);
            $this->xml .= ' get-method="'.$getter.'"';
            $this->xml .= ' set-method="'.$setter.'"';
            $this->xml .= '>';

            $usedTypeHasToBeMapped = false;

            if (!PiBX_ParseTree_BaseType::isBaseType($tree->getType())) {
                $usedTypeHasToBeMapped = true;
            }

            if ($usedTypeHasToBeMapped) {
                $this->xml .= '<structure map-as="'.$tree->getType().'" name="'.$tree->getName().'"/>';
            } else {
                $this->xml .= '<value style="element" name="'.$tree->getName().'" type="'.$tree->getType().'"/>';
            }

            $this->xml .= "</collection>";
            
        } elseif ($tree->getParent()->countChildren() == 1) {
            if (PiBX_ParseTree_BaseType::isBaseType($tree->getType())) {
                $this->xml .= '<value style="element" name="'.$tree->getName().'" type="'.$tree->getType().'"/>';
            } else {
                $this->xml .= '<structure map-as="'.$tree->getType().'" name="'.$tree->getName().'"/>';
            }
        } else {
            throw new RuntimeException('Collections with > 1 children are currently not supported');
        }
        
        return true;
    }

    public function visitEnumerationEnter(PiBX_AST_Tree $tree) {
        $parent = $tree->getParent();

        if ($parent instanceof PiBX_AST_TypeAttribute) {
            $this->xml .= '<value style="element" name="'.$parent->getName().'"';
            $getter = PiBX_Binding_Names::createGetterNameFor($tree);
            $setter = PiBX_Binding_Names::createSetterNameFor($tree);
            $this->xml .= ' get-method="'.$getter.'"';
            $this->xml .= ' set-method="'.$setter.'"';
            $this->xml .= '/>';
            return false;
        }
        
        return true;
    }
    public function visitEnumerationLeave(PiBX_AST_Tree $tree) {
        return true;
    }
    public function visitEnumeration(PiBX_AST_Tree $tree) {
    }

    public function visitEnumerationValue(PiBX_AST_Tree $tree) {
    }

    public function visitStructureEnter(PiBX_AST_Tree $tree) {
        $this->xml .= '<structure';
        $this->xml .= ' name="'.$tree->getName().'"';
        $this->xml .= '>';
        if ($tree->getStructureType() == PiBX_AST_StructureType::CHOICE()) {
            $this->xml .= '<structure ordered="false" choice="true">';
        }
        return true;
    }
    public function visitStructureLeave(PiBX_AST_Tree $tree) {
        if ($tree->getStructureType() == PiBX_AST_StructureType::CHOICE()) {
            $this->xml .= '</structure>';
        }
        $this->xml .= "</structure>";
        return true;
    }

    public function visitStructureElementEnter(PiBX_AST_Tree $tree) {
        $this->xml .= '<value';
        $this->xml .= ' style="element"';
        $this->xml .= ' name="'.$tree->getName().'"';
        $testMethod = PiBX_Binding_Names::createTestFunctionFor($tree);
        $getMethod = PiBX_Binding_Names::createGetterNameFor($tree);
        $setMethod = PiBX_Binding_Names::createSetterNameFor($tree);
        $this->xml .= ' test-method="'.$testMethod.'"';
        $this->xml .= ' get-method="'.$getMethod.'"';
        $this->xml .= ' set-method="'.$setMethod.'"';
        $this->xml .= ' usage="optional"';
        $this->xml .= '/>';
        return true;
    }
    public function visitStructureElementLeave(PiBX_AST_Tree $tree) {
        return true;
    }

    public function visitTypeEnter(PiBX_AST_Tree $tree) {
        // root types can define a default target namespace
        // which has to be included in the binding
        if ($tree->isRoot()) {
            $targetNamespace = $tree->getTargetNamespace();
            
            if ($targetNamespace != '' && $this->targetNamespaceHasBeenAdded == false) {
                $availableNamespaces = $tree->getNamespaces();
                $key = array_search($targetNamespace, $availableNamespaces);
                
                $this->xml .= '<namespace uri="' . $targetNamespace . '"';
                $this->xml .= ' default="elements"';
                if ($key !== false && $key != '') {
                    $this->xml .= ' prefix="' . $key . '"';
                }
                $this->xml .= '/>';
                $this->targetNamespaceHasBeenAdded = true;
            }
        }

        if ($tree->isStandardType()) {
            $className = PiBX_Binding_Names::createClassnameFor($tree);
            $this->xml .= '<mapping class="' . $className . '"';

            if ($tree->isRoot()) {
                $this->xml .= ' name="'.$tree->getName().'"';
            } else {
                $this->xml .= ' abstract="true"';
                $this->xml .= ' type-name="'.$tree->getName().'"';
            }

            $this->xml .= '>';

            if ( !PiBX_ParseTree_BaseType::isBaseType($tree->getType()) && !$tree->hasChildren()) {
                $usedType = $tree->getType();
                $referencedType = $this->getTypeByName($usedType);

                if ($referencedType == null) {
                    // an empty type. not common but possible
                    return true;
                }

                if ($referencedType->isEnumerationType()) {
                    $this->xml .= '<value style="text"';

                    $getter = PiBX_Binding_Names::createGetterNameFor($tree);
                    $setter = PiBX_Binding_Names::createSetterNameFor($tree);

                    $this->xml .= ' get-method="'.$getter.'"';
                    $this->xml .= ' set-method="'.$setter.'"';
                    $this->xml .= ' format="' . $usedType . '"';
                    $this->xml .= '/>';
                } else {
                    $this->xml .= '<structure map-as="';
                    $this->xml .= $tree->getType();
                    $this->xml .= '"/>';
                }
            } elseif ( !$tree->hasChildren() ) {
                $this->xml .= '<value style="text"';

                $getter = PiBX_Binding_Names::createGetterNameFor($tree);
                $setter = PiBX_Binding_Names::createSetterNameFor($tree);

                $this->xml .= ' get-method="'.$getter.'"';
                $this->xml .= ' set-method="'.$setter.'"';
                $this->xml .= '/>';
            }
        } elseif ($tree->isEnumerationType()) {
            $labelName = PiBX_Binding_Names::createClassnameFor($tree);
            $className = $labelName . '1';
            
            $this->xml .= '<format';
            $this->xml .= ' label="' . $labelName . '"';
            $this->xml .= ' type="' . $className . '"';
            $this->xml .= ' enum-value-method="toString"';
            $this->xml .= '/>';
        }
        
        return true;
    }
    public function visitTypeLeave(PiBX_AST_Tree $tree) {
        if ($tree->isStandardType()) {
            $this->xml .= "</mapping>";
        }
        
        return true;
    }

    public function visitTypeAttributeEnter(PiBX_AST_Tree $tree) {
        if ($tree->countChildren() == 0) {
            if (PiBX_ParseTree_BaseType::isBaseType($tree->getType())) {
                $this->xml .= '<value style="'.$tree->getStyle().'"';
                $this->xml .= ' name="'.$tree->getName().'"';

                $getter = PiBX_Binding_Names::createGetterNameFor($tree);
                $setter = PiBX_Binding_Names::createSetterNameFor($tree);
                $this->xml .= ' get-method="'.$getter.'"';
                $this->xml .= ' set-method="'.$setter.'"';
                if ($tree->isOptional()) {
                    $this->xml .= ' usage="optional"';
                }
                $this->xml .= '/>';
            } else {
                $usedTypeHasToBeMapped = true;
                $name = PiBX_Binding_Names::createClassnameFor($tree->getType());
                
                $currentType = $tree;
                do {
                    // following the path of used types to get information about the "leaf type"
                    // based on the leaf type it's decided whether the type has to be mapped or not
                    $referencedType = $this->getTypeByName($currentType->getType());

                    if ($referencedType == null) {
                        if ($currentType->isRoot()) {
                            $usedTypeHasToBeMapped = false;
                        }
                        break;
                    }

                    if (PiBX_ParseTree_BaseType::isBaseType($referencedType->getType())) {
                        $usedTypeHasToBeMapped = false;
                    } else {
                        $usedTypeHasToBeMapped = true;
                    }
                    
                    $currentType = $referencedType;
                } while ($referencedType != null);

                $getter = PiBX_Binding_Names::createGetterNameFor($tree);
                $setter = PiBX_Binding_Names::createSetterNameFor($tree);

                $this->xml .= '<structure';

                if ($usedTypeHasToBeMapped) {
                    $this->xml .= ' map-as="' . $name . '"';
                    $this->xml .= ' get-method="'.$getter.'"';
                    $this->xml .= ' set-method="'.$setter.'"';
                    $this->xml .= ' name="' . $tree->getType() . '"';
                } else {
                    $this->xml .= ' type="' . $name . '"';
                    $this->xml .= ' get-method="'.$getter.'"';
                    $this->xml .= ' set-method="'.$setter.'"';
                }

                $this->xml .= '/>';
            }
            return false;
        } else {
            return true;
        }
    }
    public function visitTypeAttributeLeave(PiBX_AST_Tree $tree) {
        return true;
    }

    private function getTypeByName($typeName) {
        if ($typeName == '') {
            return null;
        }
        
        foreach ($this->typeList as &$type) {
            if ($type->getName() == $typeName) {
                return $type;
            }
        }
        
        return null;
    }
}
