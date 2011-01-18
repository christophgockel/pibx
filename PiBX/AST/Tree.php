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
 * Base, or root, type of the abstract-syntax-tree.
 *
 * @author Christoph Gockel
 */
abstract class PiBX_AST_Tree {
    /**
     * @var string Name of the current node
     */
    protected $name;
    /**
     * @var PiBX_AST_Tree[] Child-nodes (composite)
     */
    protected $children;
    /**
     * @var PiBX_AST_Tree The nodes parent-node
     */
    protected $parent;
    /**
     * @var string The type of the current node (e.g. "string", "long" for XSD base types or names of complexTypes in a schema).
     */
    protected $type;

    public function  __construct($name = '', $type = '') {
        $this->name = $name;
        $this->children = array();
        $this->type = $type;
    }

    public function setParent(PiBX_AST_Tree $tree) {
        $this->parent = $tree;
    }

    public function getParent() {
        return $this->parent;
    }

    public function add(PiBX_AST_Tree $tree) {
        $this->children[] = $tree;
        $tree->setParent($this);
        
        return $this;
    }

    public function get($index) {
        if (!isset($this->children[$index])) {
            throw new RuntimeException('Invalid child index "'.$index.'"');
        }
        return $this->children[$index];
    }

    public function remove(PiBX_AST_Tree $tree) {
        $index = array_search($tree, $this->children, true);

        if ($index === false) {
            return false;
        }

        array_splice($this->children, $index, 1);

        return true;
    }

    public function hasChildren() {
        return count($this->children) > 0;
    }

    public function countChildren() {
        return count($this->children);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    abstract function accept(PiBX_AST_Visitor_VisitorAbstract $v);
}
