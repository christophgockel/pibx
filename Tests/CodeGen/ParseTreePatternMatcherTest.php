<?php
/**
 * Copyright (c) 2010-2011, Christoph Gockel.
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
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/CodeGen/ParseTreePatternMatcher.php';
require_once 'PiBX/ParseTree/RootNode.php';
require_once 'PiBX/ParseTree/ElementNode.php';
require_once 'PiBX/ParseTree/SimpleTypeNode.php';
require_once 'PiBX/AST/Type.php';
/**
 * 
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ParseTreePatternMatcherTest extends PHPUnit_Framework_TestCase {
    public function testSingleElementNodeShouldBeRootType() {
        $element = new PiBX_ParseTree_ElementNode(array('name' => 'test'), 0);

        $matcher = new PiBX_CodeGen_ParseTreePatternMatcher();
        $matcher->addElement($element);

        $this->assertTrue($matcher->elementsMatch());
        $this->assertTrue($matcher->elementsMatchDistinct());
        $this->assertTrue($matcher->getMatchedAST() instanceof PiBX_AST_Type);
    }

    public function testSingleSimpleTypeNodeShouldBeRootType() {
        $element = new PiBX_ParseTree_SimpleTypeNode(array('name' => 'test'), 0);

        $matcher = new PiBX_CodeGen_ParseTreePatternMatcher();
        $matcher->addElement($element);

        $this->assertTrue($matcher->elementsMatch());
        $this->assertTrue($matcher->elementsMatchDistinct());
        $this->assertTrue($matcher->getMatchedAST() instanceof PiBX_AST_Type);
    }

    public function testComplexTypeWithAttribute() {
        $complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'testType'), 0);
            $sequence = new PiBX_ParseTree_SequenceNode(array(), 1);
                $element = new PiBX_ParseTree_ElementNode(array('name' => 'text', 'type' => 'string', 'minOccurs' => '0'), 2);
            $attribute = new PiBX_ParseTree_AttributeNode(array('name' => 'string', 'type' => 'string'), 1);


        $matcher = new PiBX_CodeGen_ParseTreePatternMatcher();
        $matcher->addElement($complexType);
        $matcher->addElement($sequence);
        $matcher->addElement($element);
        $matcher->addElement($attribute);

        $this->assertTrue($matcher->elementsMatch());
        $this->assertTrue($matcher->elementsMatchDistinct());
        $this->assertTrue($matcher->getMatchedAST() instanceof PiBX_AST_Type);
    }
}
