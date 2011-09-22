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
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/ParseTree/Tree.php';
require_once 'PiBX/ParseTree/RootNode.php';
require_once 'PiBX/ParseTree/ElementNode.php';
require_once 'PiBX/ParseTree/ComplexTypeNode.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/ASTOptimizer.php';
require_once 'PiBX/CodeGen/ClassGenerator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
require_once 'PiBX/CodeGen/TypeUsage.php';
require_once 'PiBX/Binding/Creator.php';
/**
 * Implementation of the W3C basic example "QualifiedLocalElements".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_QualifiedLocalElements extends PHPUnit_Framework_TestCase {
    public function testParseTree() {
        $expectedTree = $this->getParseTree();
        $xml = simplexml_load_file(dirname(__FILE__) . '/../../../_files/Reference/Basic/QualifiedLocalElements/qualifiedLocalElements.xsd');

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($xml);

        $parsedTree = $parser->parse();

        $this->assertTrue($parsedTree instanceof PiBX_ParseTree_Tree);
        $this->assertTrue($expectedTree instanceof PiBX_ParseTree_Tree);

        $this->assertEquals($expectedTree, $parsedTree);
    }

    public function testAST() {
        $expectedType = $this->getAST();
        $schema = simplexml_load_file(dirname(__FILE__) . '/../../../_files/Reference/Basic/QualifiedLocalElements/qualifiedLocalElements.xsd');

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        list($type) = $typeList;

        $this->assertEquals($expectedType, $type);
    }

    private function getAST() {
        $expectedType = new PiBX_AST_Type('qualifiedLocalElements');
        $expectedType->setAsRoot();
        $expectedType->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $namespaces = array(
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );
        $expectedType->setNamespaces($namespaces);

        $attribute1 = new PiBX_AST_TypeAttribute('element1', 'string');
        $attribute2 = new PiBX_AST_TypeAttribute('element2', 'string');

        $expectedType->add($attribute1);
        $expectedType->add($attribute2);

        return $expectedType;
    }

    public function getParseTree() {
        $tree = new PiBX_ParseTree_RootNode();
        $tree->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $options = array(
            'name' => 'qualifiedLocalElements',
            'type' => ''
        );
        $namespaces = array(
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );
        
        $element = new PiBX_ParseTree_ElementNode($options, 0);
        $element->setNamespaces($namespaces);

        $complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
        $complexType->setNamespaces($namespaces);

        $sequenceNode = new PiBX_ParseTree_SequenceNode(array(), 2);
        $sequenceNode->setNamespaces($namespaces);

        $element1 = new PiBX_ParseTree_ElementNode(array('name' => 'element1', 'type' => 'string'), 3);
        $element1->setNamespaces($namespaces);
        $element2 = new PiBX_ParseTree_ElementNode(array('name' => 'element1', 'type' => 'string'), 3);
        $element2->setNamespaces($namespaces);

        $sequenceNode->add($element1);
        $sequenceNode->add($element2);
        $complexType->add($sequenceNode);
        $element->add($complexType);
        $tree->add($element);
        
        return $tree;
    }
}