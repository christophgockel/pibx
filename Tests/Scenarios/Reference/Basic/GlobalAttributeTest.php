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
 * Implementation of the W3C basic example "GlobalAttribute".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_GlobalAttributeTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/GlobalAttribute';
        $this->schemaFile = 'GlobalAttribute.xsd';
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

        $type1 = new PiBX_AST_Type('globalAttributeAttr', 'string');
        $type1->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type1->setNamespaces($namespaces);
        $type1->setValueStyle('attribute');


        $type2 = new PiBX_AST_Type('GlobalAttribute');
        $type2->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type2->setNamespaces($namespaces);
            $type2_attribute1 = new PiBX_AST_TypeAttribute('globalAttributeElement', 'string');
            $type2_attribute2 = new PiBX_AST_TypeAttribute('', 'globalAttributeAttr');
            $type2_attribute2->setStyle('attribute');

        $type2->add($type2_attribute1);
        $type2->add($type2_attribute2);


        $type3 = new PiBX_AST_Type('globalAttribute', 'GlobalAttribute');
        $type3->setAsRoot();
        $type3->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type3->setNamespaces($namespaces);


        $type4 = new PiBX_AST_Type('echoGlobalAttribute');
        $type4->setAsRoot();
        $type4->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type4->setNamespaces($namespaces);
            $type4_attribute = new PiBX_AST_TypeAttribute('', 'globalAttribute');

        $type4->add($type4_attribute);


        return array($type1, $type2, $type3, $type4);
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
       <xs:attribute xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                     xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                     name="globalAttributeAttr"
                     type="xs:string"/>
        */
        $node1_attribute = new PiBX_ParseTree_AttributeNode(array('name' => 'globalAttributeAttr', 'type' => 'string'), 0);
        $node1_attribute->setNamespaces($namespaces);

        /*
       <xs:complexType xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                       xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                       name="GlobalAttribute">
            <xs:sequence>
              <xs:element name="globalAttributeElement" type="xs:string"/>
            </xs:sequence>
            <xs:attribute ref="ex:globalAttributeAttr"/>
          </xs:complexType>
        */
        $node2_complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'GlobalAttribute'),0);
        $node2_complexType->setNamespaces($namespaces);
            $node2_sequence = new PiBX_ParseTree_SequenceNode(array(), 1);
            $node2_sequence->setNamespaces($namespaces);
                $node2_element = new PiBX_ParseTree_ElementNode(array('name' => 'globalAttributeElement', 'type' => 'string'), 2);
                $node2_element->setNamespaces($namespaces);
            $node2_attribute = new PiBX_ParseTree_AttributeNode(array('type' => 'globalAttributeAttr'), 1);
            $node2_attribute->setNamespaces($namespaces);

            $node2_sequence->add($node2_element);
        $node2_complexType->add($node2_sequence);
        $node2_complexType->add($node2_attribute);

        /*
       <xs:element xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                   xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                   name="globalAttribute"
                   type="ex:GlobalAttribute"/>
        */
        $node3_element = new PiBX_ParseTree_ElementNode(array('name' => 'globalAttribute', 'type' => 'GlobalAttribute'), 0);
        $node3_element->setNamespaces($namespaces);

        /*
       <xs:element name="echoGlobalAttribute">
          <xs:complexType>
             <xs:sequence>
                <xs:element ref="ex:globalAttribute"/>
             </xs:sequence>
          </xs:complexType>
       </xs:element>
        */
        $node4_element = new PiBX_ParseTree_ElementNode(array('name' => 'echoGlobalAttribute'), 0);
        $node4_element->setNamespaces($namespaces);
            $node4_complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node4_complexType->setNamespaces($namespaces);
                $node4_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node4_sequence->setNamespaces($namespaces);
                    $node4_element1 = new PiBX_ParseTree_ElementNode(array('type' => 'globalAttribute'), 3);
                    $node4_element1->setNamespaces($namespaces);

                $node4_sequence->add($node4_element1);
            $node4_complexType->add($node4_sequence);
        $node4_element->add($node4_complexType);

        
        $tree->add($node1_attribute);
        $tree->add($node2_complexType);
        $tree->add($node3_element);
        $tree->add($node4_element);
        
        return $tree;
    }
}