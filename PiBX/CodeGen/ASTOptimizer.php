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
require_once 'PiBX/CodeGen/TypeUsage.php';
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
/**
 * After the construction of the AST, the ASTOptimizer is responsible for
 * tree optimizations.
 *
 * These can include node-reductions, or simplifications of sub-trees.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ASTOptimizer {
    private $typeList;
    private $usage;

    /**
     * 
     * @param PiBX_AST_Tree[] $typeList
     * @param PiBX_CodeGen_TypeUsage $usage
     */
    public function  __construct($typeList, PiBX_CodeGen_TypeUsage $usage) {
        $this->typeList = $typeList;
        $this->usage = $usage;
    }

    /**
     * Start the optimization.
     * 
     * @return PiBX_AST_Tree[] The optimized list of types.
     */
    public function optimize() {
        $this->removeUnusedTypes();
        
        return $this->typeList;
    }

    private function removeUnusedTypes() {
        $usages = $this->usage->getTypeUsages();
        foreach ($usages as $class => $count) {
            if ($count === 1) {
                $typeIndex = 0;
                // a single used reference can be removed
                foreach ($this->typeList as &$type) {
                    if ($type->getName() == $class) {
                        array_splice($this->typeList, $typeIndex, 1);
                        break;
                    }
                    $typeIndex++;
                }
            }
        }
    }
}