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
require_once 'PiBX/ParseTree/ComplexContentNode.php';
require_once 'PiBX/ParseTree/ExtensionNode.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/ASTOptimizer.php';
require_once 'PiBX/CodeGen/ClassGenerator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
require_once 'PiBX/CodeGen/TypeUsage.php';
require_once 'PiBX/Binding/Creator.php';
/**
 * Implementation of the W3C basic example "ComplexTypeAttributeExtension".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_ComplexTypeAttributeExtensionTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/ComplexTypeAttributeExtension';
        $this->schemaFile = 'ComplexTypeAttributeExtension.xsd';
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

        $type1 = new PiBX_AST_Type('complexTypeAttributeExtension', 'ComplexTypeAttributeExtension');
        $type1->setAsRoot();
        $type1->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type1->setNamespaces($namespaces);


        $type2 = new PiBX_AST_Type('ComplexTypeAttributeBase');
        $type2->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type2->setNamespaces($namespaces);
            $type2_attribute = new PiBX_AST_TypeAttribute('name', 'string');
            $type2->add($type2_attribute);

//            $attribute = new PiBX_AST_TypeAttribute('id', 'string');
//            $attribute->setStyle('attribute');
//            $type2->add($attribute);
//
//            $attribute = new PiBX_AST_TypeAttribute('currency', 'string');
//            $attribute->setStyle('attribute');
//            $type2->add($attribute);

        $type3 = new PiBX_AST_Type('ComplexTypeAttributeExtension', null, 'ComplexTypeAttributeBase');
        $type3->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type3->setNamespaces($namespaces);
            $type3_attribute = new PiBX_AST_TypeAttribute('gender', 'string');
            $type3_attribute->setStyle('attribute');

        $type3->add($type3_attribute);


        $type4 = new PiBX_AST_Type('echoComplexTypeAttributeExtension');
        $type4->setAsRoot();
        $type4->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type4->setNamespaces($namespaces);
            $type4_attribute = new PiBX_AST_TypeAttribute('', 'complexTypeAttributeExtension');


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
           <xs:element xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                       xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                       name="complexTypeAttributeExtension"
                       type="ex:ComplexTypeAttributeExtension"/>
         */
        $node1_element = new PiBX_ParseTree_ElementNode(array('name' => 'complexTypeAttributeExtension', 'type' => 'ComplexTypeAttributeExtension'), 0);
        $node1_element->setNamespaces($namespaces);

        /*
           <xs:complexType xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                           xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                           name="ComplexTypeAttributeBase">
                <xs:sequence>
                  <xs:element name="name" type="xs:string"/>
                </xs:sequence>
              </xs:complexType>
         */
        $node2_complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'ComplexTypeAttributeBase'), 0);
        $node2_complexType->setNamespaces($namespaces);
            $node2_sequence = new PiBX_ParseTree_SequenceNode(array(), 1);
            $node2_sequence->setNamespaces($namespaces);
                $node2_element = new PiBX_ParseTree_ElementNode(array('name' => 'name', 'type' => 'string'), 2);
                $node2_element->setNamespaces($namespaces);

            $node2_sequence->add($node2_element);
        $node2_complexType->add($node2_sequence);

        /*
           <xs:complexType xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                           xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                           name="ComplexTypeAttributeExtension">

                <xs:complexContent>
                  <xs:extension base="ex:ComplexTypeAttributeBase">
                    <xs:attribute name="gender" type="xs:string"/>
                  </xs:extension>
                </xs:complexContent>
              </xs:complexType>
         */

        $node3_complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'ComplexTypeAttributeExtension'), 0);
        $node3_complexType->setNamespaces($namespaces);
            $node3_complexContent = new PiBX_ParseTree_ComplexContentNode(array(), 1);
            $node3_complexContent->setNamespaces($namespaces);
                $node3_extension = new PiBX_ParseTree_ExtensionNode(array('base' => 'ComplexTypeAttributeBase'), 2);
                $node3_extension->setNamespaces($namespaces);
                    $node3_attribute = new PiBX_ParseTree_AttributeNode(array('name' => 'gender', 'type' => 'string'), 3);
                    $node3_attribute->setNamespaces($namespaces);

                $node3_extension->add($node3_attribute);
            $node3_complexContent->add($node3_extension);
        $node3_complexType->add($node3_complexContent);

        /*
           <xs:element name="echoComplexTypeAttributeExtension">
              <xs:complexType>
                 <xs:sequence>

                    <xs:element ref="ex:complexTypeAttributeExtension"/>
                 </xs:sequence>
              </xs:complexType>
           </xs:element>
         */
        $node4_element = new PiBX_ParseTree_ElementNode(array('name' => 'echoComplexTypeAttributeExtension'), 0);
        $node4_element->setNamespaces($namespaces);
            $node4_complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node4_complexType->setNamespaces($namespaces);
                $node4_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node4_sequence->setNamespaces($namespaces);
                    $node4_element1 = new PiBX_ParseTree_ElementNode(array('type' => 'complexTypeAttributeExtension'), 3);
                    $node4_element1->setNamespaces($namespaces);

                $node4_sequence->add($node4_element1);
            $node4_complexType->add($node4_sequence);
        $node4_element->add($node4_complexType);
        
        $tree->add($node1_element);
        $tree->add($node2_complexType);
        $tree->add($node3_complexType);
        $tree->add($node4_element);
        
        return $tree;
    }
}