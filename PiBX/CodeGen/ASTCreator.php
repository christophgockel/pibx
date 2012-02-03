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
require_once 'PiBX/CodeGen/TypeUsage.php';
require_once 'PiBX/CodeGen/ASTFactory.php';
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
require_once 'PiBX/ParseTree/Visitor/VisitorAbstract.php';
require_once 'PiBX/ParseTree/Tree.php';
/**
 * An ASTCreator is a Visitor of a ParseTree.
 * It traverses a ParseTree-structure to produce an abstract syntax tree.
 *
 * @author Christoph Gockel
 */

class PiBX_CodeGen_ASTCreator implements PiBX_ParseTree_Visitor_VisitorAbstract {
    protected $factory;
    protected $asts;
    protected $currentLevel;
    protected $lastLevel;
    protected $subtrees;
    
    public function __construct() {
        $this->factory      = new PiBX_CodeGen_ASTFactory();
        $this->currentLevel = $this->lastLevel = 0;
        $this->subtrees     = array();
        $this->asts         = array();
    }

    public function getTypeList() {
        // be sure that all remaining level 0 (i.e. root level) types are built
        $this->handleTypeCreationForLevel(-1);
        
        return $this->asts;
    }
    
    public function visitAttributeNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $ast = $this->factory->createFromAttributeNode($tree);

        $this->addSubtreeToAST($ast);
        $this->lastLevel = $this->currentLevel;
    }
    
    public function visitElementNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        
        $ast = $this->factory->createFromElementNode($tree);
        
        $this->addSubtreeToAST($ast);
        
        $this->lastLevel = $this->currentLevel;
    }

    public function visitSimpleTypeNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $ast = $this->factory->createFromSimpleTypeNode($tree);
        if ($ast != null) {
            $this->addSubtreeToAST($ast);
        }
        $this->lastLevel = $this->currentLevel;
    }
    
    public function visitComplexTypeNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);

        $ast = $this->factory->createFromComplexTypeNode($tree);
        if ($ast != null) {
            $this->addSubtreeToAST($ast);
        }
        $this->lastLevel = $this->currentLevel;
    }

    public function visitSequenceNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $this->lastLevel = $this->currentLevel;
    }

    public function visitGroupNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $this->lastLevel = $this->currentLevel;
    }

    public function visitAllNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $this->lastLevel = $this->currentLevel;
    }

    public function visitChoiceNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $ast = $this->factory->createFromChoiceNode($tree);
        if ($ast !== null) {
            $this->addSubtreeToAST($ast);
        }
        $this->lastLevel = $this->currentLevel;
    }
    
    public function visitRestrictionNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $ast = $this->factory->createFromRestrictionNode($tree);
        if ($ast !== null) {
            $this->addSubtreeToAST($ast);
        }
        $this->lastLevel = $this->currentLevel;
    }

    public function visitEnumerationNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);

        $ast = $this->factory->createFromEnumerationNode($tree);
        if ($ast !== null) {
            $this->addSubtreeToAST($ast);
        }
        
        $this->lastLevel = $this->currentLevel;
    }

    public function visitComplexContentNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $this->lastLevel = $this->currentLevel;
    }

    public function visitExtensionNode(PiBX_ParseTree_Tree $tree) {
        $this->currentLevel = $tree->getLevel();
        $this->handleTypeCreationForLevel($this->currentLevel);
        $this->lastLevel = $this->currentLevel;
    }
    
    private function handleTypeCreationForLevel($parseTreeLevel) {
        if ($this->parsedElementsAreCreatableInLevel($parseTreeLevel)) {
            $previousSubtrees = null;
            $previousSubtrees = end($this->subtrees);
            $subtrees         = prev($this->subtrees);
            
            do {
                if ($subtrees === false) {
                    break;
                }
                
                $lastElementInCurrentSubtree = $subtrees[ count($subtrees) - 1 ];

                foreach ($previousSubtrees as &$previousSubtree) {
                    $lastElementInCurrentSubtree->add($previousSubtree);
                }

                $previousSubtrees = $subtrees;
            } while ($subtrees = prev($this->subtrees));

            if ($parseTreeLevel <= 0) {
                $this->asts = array_merge($this->asts, $previousSubtrees);
            }
            
            $this->unsetCurrentSubtrees($parseTreeLevel);
        }
    }
    
    private function unsetCurrentSubtrees($parseTreeLevel) {
        foreach ($this->subtrees as $level => &$subteesInLevel) {
            if ($level < $parseTreeLevel) {
                continue;
            }
            
            unset($this->subtrees[$level]);
        }
    }
    
    private function parsedElementsAreCreatableInLevel($parseTreeLevel) {
        return $this->parsedElementIsParentOfLastElement($parseTreeLevel) ||
               $this->subtreeIsCreatable($parseTreeLevel);
    }

    private function subtreeIsCreatable($parseTreeLevel) {
        return $this->lastLevel < $parseTreeLevel
               && isset($this->subtrees[$parseTreeLevel])
               && count($this->subtrees[$parseTreeLevel]) > 0;
    }
    
    private function parsedElementIsParentOfLastElement($parseTreeLevel) {
        return $parseTreeLevel < $this->lastLevel;
    }
    
    private function addSubtreeToAST(PiBX_AST_Tree $ast) {
        $this->subtrees[$this->currentLevel][] = $ast;
    }
}
