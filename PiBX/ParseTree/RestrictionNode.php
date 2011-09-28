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
require_once 'PiBX/ParseTree/AttributeHelper.php';
/**
 * Represents a <code>&lt;restriction></code>-node of an XML-Schema.
 *
 * @author Christoph Gockel
 */
class PiBX_ParseTree_RestrictionNode extends PiBX_ParseTree_Tree {

    public function  __construct($xmlOrOptions, $level = 0) {
        parent::__construct($xmlOrOptions, $level);
        $this->options = PiBX_ParseTree_AttributeHelper::getRestrictionOptions($xmlOrOptions);
    }

    /**
     * Returns the restriction-base (xml-attribute value)
     * @return string
     */
    public function getBase() {
        return $this->options['base'];
    }

    public function  accept(PiBX_ParseTree_Visitor_VisitorAbstract $v) {
        $v->visitRestrictionNode($this);

        foreach ($this->children as $child) {
            $child->accept($v);
        }
    }
}
