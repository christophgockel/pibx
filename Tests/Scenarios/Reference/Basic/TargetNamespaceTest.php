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
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'Tests/Scenarios/Reference/TestCase.php';
require_once 'PiBX/ParseTree/Tree.php';
require_once 'PiBX/ParseTree/ElementNode.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/ASTOptimizer.php';
require_once 'PiBX/CodeGen/ClassGenerator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
require_once 'PiBX/CodeGen/TypeUsage.php';
/**
 * Test-data definition for the W3C basic example "TargetNamespace".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_TargetNamespaceTest extends PiBX_Scenarios_Reference_TestCase {

    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/TargetNamespace';
        $this->schemaFile = 'targetNamespace.xsd';
    }

    public function _testExampleOutput() {
        return <<<OUTPUT
<?xml version="1.0" encoding="UTF-8"?>
<ex:targetNamespace xmlns:ex="http://www.w3.org/2002/ws/databinding/examples/6/09/"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                    xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/">foo</ex:targetNamespace>
OUTPUT;
    }

    public function getASTs() {
        $expectedType = new PiBX_AST_Type('targetNamespace', 'string');
        $expectedType->setAsRoot();
        $expectedType->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $namespaces = array(
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );
        $expectedType->setNamespaces($namespaces);

        return array($expectedType);
    }

    public function getParseTree() {
        $namespaces = array(
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );

        $tree = new PiBX_ParseTree_RootNode();
        $tree->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        
        $element = new PiBX_ParseTree_ElementNode(array('name' => 'targetNamespace','type' => 'string'), 0);
        $element->setNamespaces($namespaces);

        $tree->add($element);
        
        return $tree;
    }
}
