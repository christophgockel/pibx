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
require_once 'PiBX/ParseTree/Tree.php';
/**
 * Defines the structure of the Visitor-Pattern used for the ParseTree.
 * 
 * @author Christoph Gockel
 */
interface PiBX_ParseTree_Visitor_VisitorAbstract {
    public function visitAttributeNode(PiBX_ParseTree_Tree $tree);
    public function visitElementNode(PiBX_ParseTree_Tree $tree);
    public function visitSimpleTypeNode(PiBX_ParseTree_Tree $tree);
    public function visitComplexTypeNode(PiBX_ParseTree_Tree $tree);
    public function visitSequenceNode(PiBX_ParseTree_Tree $tree);
    public function visitGroupNode(PiBX_ParseTree_Tree $tree);
    public function visitAllNode(PiBX_ParseTree_Tree $tree);
    public function visitChoiceNode(PiBX_ParseTree_Tree $tree);
    public function visitRestrictionNode(PiBX_ParseTree_Tree $tree);
    public function visitEnumerationNode(PiBX_ParseTree_Tree $tree);
}
