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
/**
 * Abstract syntax tree visitor.
 * 
 * It is a Hierarchical Visitor, hence the <code>visitXXXEnter()</code> and
 * <code>visitXXXLeave()</code> methods.
 * Since not every AST-Node is a composite (but a leaf), not every node-type
 * needs to have a corresponding <code>...Enter()</code> and
 * <code>...Leave()</code> method.
 *
 * @author Christoph Gockel
 */
interface PiBX_AST_Visitor_VisitorAbstract {
    public function visitCollectionEnter(PiBX_AST_Tree $tree);
    public function visitCollectionLeave(PiBX_AST_Tree $tree);

    public function visitCollectionItem(PiBX_AST_Tree $tree);

    public function visitEnumerationEnter(PiBX_AST_Tree $tree);
    public function visitEnumerationLeave(PiBX_AST_Tree $tree);
    public function visitEnumeration(PiBX_AST_Tree $tree);

    public function visitEnumerationValue(PiBX_AST_Tree $tree);

    public function visitStructureEnter(PiBX_AST_Tree $tree);
    public function visitStructureLeave(PiBX_AST_Tree $tree);

    public function visitStructureElementEnter(PiBX_AST_Tree $tree);
    public function visitStructureElementLeave(PiBX_AST_Tree $tree);

    public function visitTypeEnter(PiBX_AST_Tree $tree);
    public function visitTypeLeave(PiBX_AST_Tree $tree);

    public function visitTypeAttributeEnter(PiBX_AST_Tree $tree);
    public function visitTypeAttributeLeave(PiBX_AST_Tree $tree);
}
