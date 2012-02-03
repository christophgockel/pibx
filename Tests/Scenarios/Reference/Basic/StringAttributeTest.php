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
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'Tests/Scenarios/Reference/TestCase.php';
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
 * Implementation of the W3C basic example "StringAttribute".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_StringAttributeTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/StringAttribute';
        $this->schemaFile = 'StringAttribute.xsd';
    }

    public function getASTs() {
        $namespaces = array(
            '' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'p' => 'http://www.w3.org/2002/ws/databinding/patterns/6/09/',
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );

        $typeReferencedElement = new PiBX_AST_Type('stringAttribute', 'StringAttribute');
        $typeReferencedElement->setAsRoot();
        $typeReferencedElement->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $typeReferencedElement->setNamespaces($namespaces);

        $typeElement = new PiBX_AST_Type('StringAttribute');
        $typeElement->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $typeElement->setNamespaces($namespaces);
            $typeElementAttribute1 = new PiBX_AST_TypeAttribute('text', 'string', true);
            $typeElementAttribute1->setStyle('element');

            $typeElementAttribute2 = new PiBX_AST_TypeAttribute('string', 'string');
            $typeElementAttribute2->setStyle('attribute');

        $typeEchoElement = new PiBX_AST_Type('echoStringAttribute');
        $typeEchoElement->setAsRoot();
        $typeEchoElement->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $typeEchoElement->setNamespaces($namespaces);
            $typeEchoElementAttribute1 = new PiBX_AST_TypeAttribute('', 'stringAttribute');
            $typeEchoElementAttribute1->setStyle('element');
        

        $typeElement->add($typeElementAttribute1);
        $typeElement->add($typeElementAttribute2);

        $typeEchoElement->add($typeEchoElementAttribute1);
        
        return array($typeReferencedElement, $typeElement, $typeEchoElement);
    }

    public function getParseTree() {
        $tree = new PiBX_ParseTree_RootNode();
        $tree->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        
        $options = array(
            'name' => 'stringElement',
            'type' => ''
        );
        $namespaces = array(
            '' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'p' => 'http://www.w3.org/2002/ws/databinding/patterns/6/09/',
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );

        $element1 = new PiBX_ParseTree_ElementNode(array('name' => 'stringAttribute', 'type' => 'StringAttribute'), 0);
        $element1->setNamespaces($namespaces);
        $complexType1 = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'StringAttribute'), 0);
        $complexType1->setNamespaces($namespaces);
            $sequence1 = new PiBX_ParseTree_SequenceNode(array(), 1);
            $sequence1->setNamespaces($namespaces);
                $element2 = new PiBX_ParseTree_ElementNode(array('name' => 'text', 'type' => 'string', 'minOccurs' => '0'), 2);
                $element2->setNamespaces($namespaces);
                $attribute = new PiBX_ParseTree_AttributeNode(array('name' => 'string', 'type' => 'string'), 1);
                $attribute->setNamespaces($namespaces);
        $element3 = new PiBX_ParseTree_ElementNode(array('name' => 'echoStringAttribute'), 0);
        $element3->setNamespaces($namespaces);
            $complexType2 = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $complexType2->setNamespaces($namespaces);
                $sequence2 = new PiBX_ParseTree_SequenceNode(array(), 2);
                $sequence2->setNamespaces($namespaces);
                    $element4 = new PiBX_ParseTree_ElementNode(array('type' => 'stringAttribute'), 3);
                    $element4->setNamespaces($namespaces);

        $sequence1->add($element2);
        $complexType1->add($sequence1);
        $complexType1->add($attribute);

        $sequence2->add($element4);
        $complexType2->add($sequence2);
        $element3->add($complexType2);

        $tree->add($element1);
        $tree->add($complexType1);
        $tree->add($element3);
        
        return $tree;
    }
}