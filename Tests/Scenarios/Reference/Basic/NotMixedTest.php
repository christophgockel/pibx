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
 * Implementation of the W3C basic example "NotMixed".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_NotMixedTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/NotMixed';
        $this->schemaFile = 'NotMixed.xsd';
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

        $type1 = new PiBX_AST_Type('notMixed', 'NotMixed');
        $type1->setAsRoot();
        $type1->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type1->setNamespaces($namespaces);


        $type2 = new PiBX_AST_Type('NotMixed');
        $type2->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type2->setNamespaces($namespaces);
            $type2_attribute1 = new PiBX_AST_TypeAttribute('notMixedElement', 'string');

        $type2->add($type2_attribute1);


        $type3 = new PiBX_AST_Type('echoNotMixed');
        $type3->setAsRoot();
        $type3->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type3->setNamespaces($namespaces);
            $type3_attribute = new PiBX_AST_TypeAttribute('', 'notMixed');

        $type3->add($type3_attribute);
        
        return array($type1, $type2, $type3);
    }

    public function getParseTree() {
        $tree = new PiBX_ParseTree_RootNode();
        $tree->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        
        $namespaces = array(
            '' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'p' => 'http://www.w3.org/2002/ws/databinding/patterns/6/09/',
            'ex' => 'http://www.w3.org/2002/ws/databinding/examples/6/09/',
            'wsdl11' => 'http://schemas.xmlsoap.org/wsdl/',
            'soap11enc' => 'http://schemas.xmlsoap.org/soap/encoding/'
        );
        /*
           <xs:element xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                       xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                       name="notMixed"
                       type="ex:NotMixed"/>
         */
        $node1_element = new PiBX_ParseTree_ElementNode(array('name' => 'notMixed', 'type' => 'NotMixed'), 0);
        $node1_element->setNamespaces($namespaces);
        /*
           <xs:complexType xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                           xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                           name="NotMixed"
                           mixed="false">
                <xs:sequence>
                  <xs:element name="notMixedElement" type="xs:string"/>
                </xs:sequence>
              </xs:complexType>
         */
        $node2_complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'NotMixed', 'mixed' => 'false'), 0);
        $node2_complexType->setNamespaces($namespaces);
            $node2_sequence = new PiBX_ParseTree_SequenceNode(array(), 1);
            $node2_sequence->setNamespaces($namespaces);
                $node2_element = new PiBX_ParseTree_ElementNode(array('name' => 'notMixedElement', 'type' => 'string'), 2);
                $node2_element->setNamespaces($namespaces);

            $node2_sequence->add($node2_element);
        $node2_complexType->add($node2_sequence);

        /*
           <xs:element name="echoNotMixed">

              <xs:complexType>
                 <xs:sequence>
                    <xs:element ref="ex:notMixed"/>
                 </xs:sequence>
              </xs:complexType>
           </xs:element>
         */
        $node3_element = new PiBX_ParseTree_ElementNode(array('name' => 'echoNotMixed'), 0);
        $node3_element->setNamespaces($namespaces);
            $node3_complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node3_complexType->setNamespaces($namespaces);
                $node3_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node3_sequence->setNamespaces($namespaces);
                    $node3_element1 = new PiBX_ParseTree_ElementNode(array('type' => 'notMixed'), 3);
                    $node3_element1->setNamespaces($namespaces);

                $node3_sequence->add($node3_element1);
        $node3_complexType->add($node3_sequence);
        $node3_element->add($node3_complexType);
        
        $tree->add($node1_element);
        $tree->add($node2_complexType);
        $tree->add($node3_element);
        
        return $tree;
    }
}