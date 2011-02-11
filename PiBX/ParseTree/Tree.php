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

/**
 * A PiBX_ParseTree_Tree represents the whole schema definition in a composite
 * object structure.
 *
 * @author Christoph Gockel
 */
abstract class PiBX_ParseTree_Tree {
    /**
     * @var int The current level in the tree.
     */
    private $level;

    /**
     * @var ParseTree[]
     */
    protected $children = array();

    /**
     * @var PiBX_ParseTree_Tree The current's node parent node.
     */
    protected $parent;

    /**
     * @var array List of registered namespaces of the current XML fragment
     */
    protected $namespaces;

    public function  __construct(SimpleXMLElement $xml, $level = 0) {
        $this->level = $level;
        $this->namespaces = $xml->getDocNamespaces();
    }

    public function setParent(PiBX_ParseTree_Tree $tree) {
        $this->parent = $tree;
    }

    public function getParent() {
        return $this->parent;
    }

    public function getLevel() {
        return $this->level;
    }

    /**
     * Returns used/defined namespaces in the current tree element as an
     * associative array: prefix => URI
     * 
     * @return array
     */
    public function getNamespaces() {
       return $this->namespaces;
    }

    public function add(PiBX_ParseTree_Tree $tree) {
        $this->children[] = $tree;
        $tree->setParent($this);
        return $this;
    }

    public function remove(PiBX_ParseTree_Tree $tree) {
        $index = array_search($tree, $this->children, true);

        if ($index === false) {
            return false;
        }

        array_splice($this->children, $index, 1);

        return true;
    }

    abstract function accept(PiBX_ParseTree_Visitor_VisitorAbstract $v);
}
