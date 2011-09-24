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
 * Implementation of the W3C basic example "QualifiedLocalElements".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_NonIdentifierNameTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/NonIdentifierName';
        $this->schemaFile = 'NonIdentifierName.xsd';
    }

    public function getASTs() {
        $namespaces = array(
            '' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'p' => 'http://www.w3.org/2002/ws/databinding/patterns/6/09/',
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
        );
        $elementNamespaces = array(
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );

        $typeReferencedElement = new PiBX_AST_Type('non-Identifier-Name', 'string');
        $typeReferencedElement->setAsRoot();
        $typeReferencedElement->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $typeReferencedElement->setNamespaces($namespaces);

        $typeElement = new PiBX_AST_Type('echoNonIdentifierName');
        $typeElement->setAsRoot();
        $typeElement->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $typeElement->setNamespaces($namespaces);
        
        $attribute = new PiBX_AST_TypeAttribute('', 'non-Identifier-Name');

        $typeElement->add($attribute);
        
        return array($typeReferencedElement, $typeElement);
    }

    public function getParseTree() {
        $tree = new PiBX_ParseTree_RootNode();
        $tree->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $options = array(
            'name' => 'non-Identifier-Name',
            'type' => ''
        );
        $namespaces = array(
            '' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'p' => 'http://www.w3.org/2002/ws/databinding/patterns/6/09/',
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
//            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
//            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );

        $element1 = new PiBX_ParseTree_ElementNode(array('name' => 'non-Identifier-Name', 'type' => 'string'), 0);
        $element1->setNamespaces($namespaces);
        $element2 = new PiBX_ParseTree_ElementNode(array('name' => 'echoNonIdentifierName', 'type' => ''), 0);
        $element2->setNamespaces($namespaces);
        $complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
        $complexType->setNamespaces($namespaces);
        $sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
        $sequence->setNamespaces($namespaces);
        $element3 = new PiBX_ParseTree_ElementNode(array('type' => 'non-Identifier-Name'), 3);
        $element3->setNamespaces($namespaces);

        $sequence->add($element3);
        $complexType->add($sequence);
        $element2->add($complexType);

        $tree->add($element1);
        $tree->add($element2);
        
        return $tree;
    }
}