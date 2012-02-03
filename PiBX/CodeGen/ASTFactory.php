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
require_once 'PiBX/ParseTree/AttributeHelper.php';
require_once 'PiBX/ParseTree/AttributeNode.php';
require_once 'PiBX/ParseTree/ChoiceNode.php';
require_once 'PiBX/ParseTree/ComplexContentNode.php';
require_once 'PiBX/ParseTree/ComplexTypeNode.php';
require_once 'PiBX/ParseTree/ElementNode.php';
require_once 'PiBX/ParseTree/EnumerationNode.php';
require_once 'PiBX/ParseTree/ExtensionNode.php';
require_once 'PiBX/ParseTree/RestrictionNode.php';
require_once 'PiBX/ParseTree/RootNode.php';
require_once 'PiBX/ParseTree/SequenceNode.php';
require_once 'PiBX/ParseTree/SimpleTypeNode.php';
require_once 'PiBX/ParseTree/Tree.php';
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
 * Factory to create an AST subnode for a given ParseTree-node.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ASTFactory {
    public function createFromComplexTypeNode(PiBX_ParseTree_ComplexTypeNode $complexType) {
        if ($complexType->getLevel() == 0) {
            $name = $complexType->getName();
            $rootNode = $complexType->getParent();
            $baseType = $this->lookupExtensionBase($complexType);

            $newType = new PiBX_AST_Type($complexType->getName(), '', $baseType);
            $newType->setNamespaces($complexType->getNamespaces());
            $newType->setTargetNamespace($rootNode->getTargetNamespace());

            return $newType;
        }
    }
    
    /**
     * The extension base is used as a base type for an AST Type-node.
     *
     * ComplexTypes can be extended via complexContent/extension elements.
     * When there is an extension defined, the "base" attribute is used a the
     * Type's base-type.
     *
     * @param PiBX_ParseTree_ComplexTypeNode $complexType
     * @return string The base-type or empty string
     */
    private function lookupExtensionBase(PiBX_ParseTree_ComplexTypeNode $complexType) {
        if (!$complexType->hasChildren()) {
            return '';
        }

        $firstChild = $complexType->get(0);
        if ($firstChild instanceof PiBX_ParseTree_ComplexContentNode) {
            if (!$firstChild->hasChildren()) {
                return '';
            }

            $firstChildOfFirstChild = $firstChild->get(0);

            if ($firstChildOfFirstChild instanceof PiBX_ParseTree_ExtensionNode) {
                return $firstChildOfFirstChild->getBase();
            }
        }

        return '';
    }

    private function inspectNode(PiBX_ParseTree_Tree $tree) {

        if ($tree instanceof PiBX_ParseTree_ComplexTypeNode) {
            return $this->inspectComplexTypeNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_ElementNode) {
            return $this->inspectElementNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_SequenceNode) {
            $firstChild = $tree->get(0);

            return $this->inspectNode($firstChild);
        } elseif ($tree instanceof PiBX_ParseTree_ChoiceNode) {
            $firstChild = $tree->get(0);

            return $this->inspectNode($firstChild);
        } elseif ($tree instanceof PiBX_ParseTree_SimpleTypeNode) {
            $firstChild = $tree->get(0);

            return $this->inspectNode($firstChild);
        } elseif ($tree instanceof PiBX_ParseTree_RestrictionNode) {
            $firstChild = $tree->get(0);

            return $this->inspectNode($firstChild);
        } elseif ($tree instanceof PiBX_ParseTree_EnumerationNode) {
            return $this->inspectEnumerationNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_ComplexContentNode) {
            $firstChild = $tree->get(0);

            return $this->inspectNode($firstChild);
        } elseif ($tree instanceof PiBX_ParseTree_ExtensionNode) {
            return $this->inspectExtensionNode($tree);
        }

        die('TODO: implement inspectNode() for ' . get_class($tree));
    }

    public function createFromSimpleTypeNode(PiBX_ParseTree_SimpleTypeNode $simpleType) {
        if ($simpleType->getLevel() == 0) {
            $newType = new PiBX_AST_Type($simpleType->getName());
            $newType->setAsRoot();
            $newType->setNamespaces($simpleType->getNamespaces());
            $rootNode = $simpleType->getParent();
            $newType->setTargetNamespace($rootNode->getTargetNamespace());

            return $newType;
        }
    }

    public function createFromElementNode(PiBX_ParseTree_ElementNode $element) {
        $ast = $this->inspectElementNode($element);

        return $ast;
    }

    public function createFromEnumerationNode(PiBX_ParseTree_EnumerationNode $enumeration) {
        $name = $enumeration->getValue();
        $type = $enumeration->getParent()->getBase();

        return new PiBX_AST_EnumerationValue($name, $type);
    }

    public function createFromRestrictionNode(PiBX_ParseTree_RestrictionNode $restriction) {
        $type = $restriction->getBase();

        $restrictionParent = $restriction->getParent();

        if ( ($restrictionParent instanceof PiBX_ParseTree_SimpleTypeNode) ) {
            if ($restrictionParent->getLevel() == 0) {
                // when the current restriction's simpleType is a root-level
                // simpleType, an enumeration container is needed for the
                // next-to-be-added EnumerationValues.
                return new PiBX_AST_Enumeration();
            }
        }
    }

    public function createFromChoiceNode(PiBX_ParseTree_ChoiceNode $choice) {
        $choiceParent = $choice->getParent();

        if ( ($choiceParent instanceof PiBX_ParseTree_ComplexTypeNode) ) {
            if ($choiceParent->getLevel() == 0) {
                // when the current choice's complexType is a root-level
                // complexType, a structure container is needed for the
                // next-to-be-added StructureElement.
                $structure = new PiBX_AST_Structure();
                $structure->setStructureType(PiBX_AST_StructureType::CHOICE());

                return $structure;
            }
        }
    }

    public function createFromAttributeNode(PiBX_ParseTree_AttributeNode $attribute) {
        $name = $attribute->getName();
        $type = $attribute->getType();

        if ($attribute->getLevel() == 0) {
            $rootNode = $attribute->getParent();

            $newType = new PiBX_AST_Type($name, $type);
            $newType->setNamespaces($attribute->getNamespaces());
            $newType->setTargetNamespace($rootNode->getTargetNamespace());
            $newType->setValueStyle('attribute');

            return $newType;
        }

        $newAttribute = new PiBX_AST_TypeAttribute($name, $type);
        $newAttribute->setStyle('attribute');

        return $newAttribute;
    }

    private function inspectExtensionNode(PiBX_ParseTree_ExtensionNode $extension) {
        return new PiBX_AST_Structure('', $extension->getBase());
    }

    private function inspectElementNode(PiBX_ParseTree_ElementNode $element) {
        if ($element->hasChildren()) {
            return $this->inspectElementNodeWithChildren($element);
        } else {
            return $this->inspectElementNodeWithoutChildren($element);
        }
    }

    private function inspectElementNodeWithChildren(PiBX_ParseTree_ElementNode $element) {
        $name       = $element->getName();
        $type       = $element->getType();
        $isOptional = $element->isOptional();

        if ($element->getLevel() == 0) {
            $newType = new PiBX_AST_Type($name, $type);
            $newType->setAsRoot();
            $newType->setNamespaces($element->getNamespaces());
            $rootNode = $element->getParent();
            $newType->setTargetNamespace($rootNode->getTargetNamespace());

            return $newType;
        }

        // to know what kind of AST the current element-node will be,
        // further information about its children is needed.
        $firstChild = $element->get(0);

        $deepestNode = $this->inspectNode($firstChild);

        if ($deepestNode instanceof PiBX_AST_CollectionItem) {
            return new PiBX_AST_Collection($name, $type);
        } elseif ($deepestNode instanceof PiBX_AST_StructureElement) {
            $newStructure = new PiBX_AST_Structure($name, $type);
            $structureType = $this->lookupStructureType($element);

            if ($structureType === PiBX_AST_StructureType::CHOICE()) {
                $newStructure->setStructureType(PiBX_AST_StructureType::CHOICE());
            }

            return $newStructure;
        } elseif ($deepestNode instanceof PiBX_AST_EnumerationValue) {
            $newEnumeration = new PiBX_AST_Enumeration($name, $type);

            return $newEnumeration;
        } elseif ($deepestNode instanceof PiBX_AST_Structure) {
            // when the deepest node returns a structure, an adaption of the
            // current type needs to be done (e.g. extension node in an
            // element node).
            $structureType = $deepestNode->getType();

            if ($structureType !== '') {
                return new PiBX_AST_Structure($name, $deepestNode->getType());
            }
        }

        return new PiBX_AST_TypeAttribute($name, $type, $isOptional);
    }

    private function lookupStructureType(PiBX_ParseTree_ElementNode $element) {
        if (!$element->hasChildren()) {
            return PiBX_AST_StructureType::STANDARD();
        }

        $firstChild = $element->get(0);

        if ($firstChild instanceof PiBX_ParseTree_ComplexTypeNode) {
            if (!$firstChild->hasChildren()) {
                return PiBX_AST_StructureType::STANDARD();
            }

            $firstChildOfFirstChild = $firstChild->get(0);

            if ($firstChildOfFirstChild instanceof PiBX_ParseTree_ChoiceNode) {
                return PiBX_AST_StructureType::CHOICE();
            }
        }

        return PiBX_AST_StructureType::STANDARD();
    }

    private function inspectElementNodeWithoutChildren(PiBX_ParseTree_ElementNode $element) {
        $parent = $element->getParent();
        $name = $element->getName();
        $type = $element->getType();
        $isOptional = $element->isOptional();

        if ($element->getLevel() == 0) {
            $newType = new PiBX_AST_Type($name, $type);
            $newType->setAsRoot();
            $newType->setNamespaces($element->getNamespaces());
            $rootNode = $element->getParent();
            $newType->setTargetNamespace($rootNode->getTargetNamespace());

            return $newType;
        }

        if ($element->getMaxOccurs() == 'unbounded') {
            return new PiBX_AST_CollectionItem($name, $type);
        }

        if ($this->elementIsPartOfGlobalType($element)) {
            return new PiBX_AST_TypeAttribute($name, $type, $isOptional);
        } else {
            return new PiBX_AST_StructureElement($name, $type);
        }
    }

    /**
     * Checks if the give ElementNode is part of a global element/type or if it's
     * used in a sub-structure (e.g. local element complexType, or something similar).
     *
     * @param PiBX_ParseTree_ElementNode $element
     * @return boolean Whether the element is part of a global type or not
     */
    private function elementIsPartOfGlobalType(PiBX_ParseTree_ElementNode $element) {
        $parent = $element->getParent();
        $isPartOfGlobalType = false;

        while ($parent !== null) {
            if ($parent instanceof PiBX_ParseTree_ComplexTypeNode
               || $parent instanceof PiBX_ParseTree_SimpleTypeNode
               || $parent instanceof PiBX_ParseTree_ElementNode
            ) {
                if ($parent->getLevel() == 0) {
                    $isPartOfGlobalType = true;
                    break;
                } else {
                    $grandparent = $parent->getParent();

                    if ($grandparent->getLevel() == 0) {
                        $isPartOfGlobalType = true;
                        break;
                    } else {
                        $isPartOfGlobalType = false;
                        break;
                    }
                }
            } elseif ($parent instanceof PiBX_ParseTree_ChoiceNode) {
                $isPartOfGlobalType = false;
                break;
            }

            $parent = $parent->getParent();
        }

        return $isPartOfGlobalType;
    }

    private function inspectComplexTypeNode(PiBX_ParseTree_ComplexTypeNode $complexType) {
        if (!$complexType->hasChildren()) {
            throw new RuntimeException('Invalid complexType definition');
        }

        if ($complexType->getLevel() == 0) {
            $newType = new PiBX_AST_Type($complexType->getName());
            $newType->setAsRoot();
            $newType->setNamespaces($complexType->getNamespaces());

            return $newType;
        }

        $firstChild = $complexType->get(0);

        return $this->inspectNode($firstChild);
    }

    private function inspectEnumerationNode(PiBX_ParseTree_EnumerationNode $enumeration) {
        return new PiBX_AST_EnumerationValue($enumeration->getValue(), $enumeration->getValue());
    }
}
