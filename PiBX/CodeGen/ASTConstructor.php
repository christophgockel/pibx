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
require_once 'PiBX/ParseTree/AttributeNode.php';
require_once 'PiBX/ParseTree/ChoiceNode.php';
require_once 'PiBX/ParseTree/ComplexTypeNode.php';
require_once 'PiBX/ParseTree/ElementNode.php';
require_once 'PiBX/ParseTree/EnumerationNode.php';
require_once 'PiBX/ParseTree/RestrictionNode.php';
require_once 'PiBX/ParseTree/SequenceNode.php';
require_once 'PiBX/ParseTree/SimpleTypeNode.php';
/**
 * The ASTConstructor converts/constructs an AST-sub-tree out of a given
 * ParseTree-stack.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ASTConstructor {
    private $stackOfElements;

    private $currentAST;
    private $temporarySubnodeStack;
    private $currentBaseType;
    private $lastAddedNode;

    public function __construct(array $stackOfParseTreeElements) {
        if (count($stackOfParseTreeElements) == 0) {
            throw new InvalidArgumentException('Cannot construct an AST without any ParseTree elements');
        }
        
        $this->stackOfElements = $stackOfParseTreeElements;
        $this->temporarySubnodeStack = array();
        $this->currentBaseType = '';
        $this->lastAddedNode = null;
    }

    public function construct() {
        // to iterate from top to bottom (leave nodes to root node)
        $reversedElements = new ArrayIterator(array_reverse($this->stackOfElements));

        foreach ($reversedElements as &$element) {
            $this->handleParseTreeElement($element);
        }

        return $this->currentAST;
    }

    private function handleParseTreeElement(PiBX_ParseTree_Tree $tree) {
        if ($tree instanceof PiBX_ParseTree_ElementNode) {
            $this->handleElementNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_ComplexTypeNode) {
            $this->handleComplexTypeNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_SequenceNode) {
            //TODO: ignored at the moment
        } elseif ($tree instanceof PiBX_ParseTree_AttributeNode) {
            $this->handleAttributeNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_EnumerationNode) {
            $this->handleEnumerationNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_SimpleTypeNode) {
            $this->handleSimpleTypeNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_RestrictionNode) {
            $this->handleRestrictionNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_ExtensionNode) {
            $this->handleExtensionNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_ComplexContentNode) {
            $this->handleComplexContentNode($tree);
        } elseif ($tree instanceof PiBX_ParseTree_ChoiceNode) {
            $this->handleChoiceNode($tree);
        } else {
            throw new RuntimeException(get_class($tree) . ' not supported yet for constructing an AST');
        }
    }

    private function handleElementNode(PiBX_ParseTree_ElementNode $element) {
        if ($element->getLevel() == 0) {
            // an element at root level is a global-type
            $type = new PiBX_AST_Type(/*ucfirst*/($element->getName()), $element->getType());
            $type->setAsRoot();
            $type->setNamespaces($element->getNamespaces());
            $type->setTargetNamespace($element->getParent()->getTargetNamespace());//TODO: get rid of these trainwrecks
            
            if ($this->hasTemporaryNodes()) {
                $this->addTemporaryNodesToTree($type);
            }
            
            $this->currentAST = $type;
        } else {
            if ($element->hasChildren()) {
                // an element with children is a local type definition
                $structure = new PiBX_AST_Structure($element->getName());

                if ($this->hasTemporaryNodes()) {
                    $reversedSubnodes = new ArrayIterator(array_reverse($this->temporarySubnodeStack));

                    foreach ($reversedSubnodes as &$node) {
                        $structureElement = new PiBX_AST_StructureElement($node->getName(), $node->getType());
                        $structure->add($structureElement);
                    }
                }

                $this->temporarySubnodeStack = array();
                $this->temporarySubnodeStack[] = $structure;
            } else {
                $elementIsOptional = $element->isOptional();
                $typeAttribute = new PiBX_AST_TypeAttribute($element->getName(), $element->getType(), $elementIsOptional);

                if ($element->getMaxOccurs() == 'unbounded') {
                    $collectionItem = new PiBX_AST_CollectionItem($element->getName(), $element->getType());
                    $typeAttribute->add($collectionItem);
                }

                $this->temporarySubnodeStack[] = $typeAttribute;
            }
        }
    }

    private function handleAttributeNode(PiBX_ParseTree_AttributeNode $attribute) {
        if ($attribute->getLevel() == 0) {
            $type = new PiBX_AST_Type($attribute->getName(), $attribute->getType());
            $type->setNamespaces($attribute->getNamespaces());
            $type->setTargetNamespace($attribute->getParent()->getTargetNamespace());//TODO: get rid of these trainwrecks
            $type->setValueStyle('attribute');

            $this->currentAST = $type;
        } else {
            $isOptional = $attribute->getUse() == 'optional';
            $typeAttribute = new PiBX_AST_TypeAttribute($attribute->getName(), $attribute->getType(), $isOptional);
            $typeAttribute->setStyle('attribute');//TODO: create enum for $style
            $this->temporarySubnodeStack[] = $typeAttribute;
        }
    }

    private function handleComplexTypeNode(PiBX_ParseTree_ComplexTypeNode $complexType) {
        if ($complexType->getLevel() == 0) {
            $type = new PiBX_AST_Type($complexType->getName(), null, $this->currentBaseType);
            $type->setNamespaces($complexType->getNamespaces());
            $type->setTargetNamespace($complexType->getParent()->getTargetNamespace());//TODO: get rid of these trainwrecks

            if ($this->hasTemporaryNodes()) {
                $this->addTemporaryNodesToTree($type);
            }

            $this->currentAST = $type;
            $this->clearCurrentBaseType();
        } else {
        }
    }

    private function clearCurrentBaseType() {
        $this->currentBaseType = '';
    }

    private function handleEnumerationNode(PiBX_ParseTree_EnumerationNode $enumeration) {
        $enumerationValue = new PiBX_AST_EnumerationValue($enumeration->getValue(), $enumeration->getParent()->getBase());
        $this->temporarySubnodeStack[] = $enumerationValue;
    }

    private function handleSimpleTypeNode(PiBX_ParseTree_SimpleTypeNode $simpleType) {
        if ($simpleType->getLevel() == 0) {
            $type = new PiBX_AST_Type($simpleType->getName());
            $type->setAsRoot();
            $type->setNamespaces($simpleType->getNamespaces());
            //$type->setTargetNamespace($simpleType->getTargetNamespace());//TODO: get rid of these trainwrecks

            if ($this->hasTemporaryNodes()) {
                $this->addTemporaryNodesToTree($type);
            }

            $this->currentAST = $type;
        } else {
            throw new RuntimeException('TODO: non-global simpleType element');
        }
    }

    private function handleRestrictionNode(PiBX_ParseTree_RestrictionNode $restriction) {
        if (!$this->hasTemporaryNodes()) {
            throw new RuntimeException('Restriction without values.');
        }

        list($firstRestriction) = $this->temporarySubnodeStack;

        if ($firstRestriction instanceof PiBX_AST_EnumerationValue) {
            $enumeration = new PiBX_AST_Enumeration();
            $this->addTemporaryNodesToTree($enumeration);
            $this->temporarySubnodeStack[] = $enumeration;
        } else {
            throw new RuntimeException('Restriction type not supported');
        }
    }

    private function handleExtensionNode(PiBX_ParseTree_ExtensionNode $extension) {
        $this->currentBaseType = $extension->getBase();
    }

    private function handleComplexContentNode(PiBX_ParseTree_ComplexContentNode $extension) {
        //TODO
    }

    private function handleChoiceNode(PiBX_ParseTree_ChoiceNode $choice) {
        $structure = new PiBX_AST_Structure();
        $structure->setStructureType(PiBX_AST_StructureType::CHOICE());

        $reversedSubnodes = new ArrayIterator(array_reverse($this->temporarySubnodeStack));

        foreach ($reversedSubnodes as &$node) {
            $structureElement = new PiBX_AST_StructureElement($node->getName(), $node->getType());
            $structure->add($structureElement);
        }

        $this->temporarySubnodeStack = array();
        $this->temporarySubnodeStack[] = $structure;
    }

    private function hasTemporaryNodes() {
        return count($this->temporarySubnodeStack) > 0;
    }

    private function addTemporaryNodesToTree(PiBX_AST_Tree $tree) {
        $reversedSubnodes = new ArrayIterator(array_reverse($this->temporarySubnodeStack));

        foreach ($reversedSubnodes as &$node) {
            $tree->add($node);
        }

        $this->temporarySubnodeStack = array();
    }
}
