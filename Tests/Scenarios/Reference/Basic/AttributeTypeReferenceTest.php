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
 * Implementation of the W3C basic example "AttributeTypeReference".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_AttributeTypeReferenceTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/AttributeTypeReference';
        $this->schemaFile = 'AttributeTypeReference.xsd';
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

        $type1 = new PiBX_AST_Type('attributeTypeReference', 'AttributeTypeReference');
        $type1->setAsRoot();
        $type1->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type1->setNamespaces($namespaces);


        $type2 = new PiBX_AST_Type('SimpleType', '');
        $type2->setAsRoot();
        $type2->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type2->setNamespaces($namespaces);
            $type2_enumeration = new PiBX_AST_Enumeration();
                $type2_enumeration_value1 = new PiBX_AST_EnumerationValue('value1', 'string');
                $type2_enumeration_value2 = new PiBX_AST_EnumerationValue('value2', 'string');

            $type2_enumeration->add($type2_enumeration_value1);
            $type2_enumeration->add($type2_enumeration_value2);
        $type2->add($type2_enumeration);


        $type3 = new PiBX_AST_Type('AttributeTypeReference');
        $type3->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type3->setNamespaces($namespaces);
            $type3_attribute1 = new PiBX_AST_TypeAttribute('element', 'string');
            $type3_attribute2 = new PiBX_AST_TypeAttribute('attr', 'SimpleType');
            $type3_attribute2->setStyle('attribute');

        $type3->add($type3_attribute1);
        $type3->add($type3_attribute2);


        $type4 = new PiBX_AST_Type('echoAttributeTypeReference');
        $type4->setAsRoot();
        $type4->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type4->setNamespaces($namespaces);
            $type4_attribute = new PiBX_AST_TypeAttribute('', 'attributeTypeReference');

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
                   name="attributeTypeReference"
                   type="ex:AttributeTypeReference"/>

        */
        $node1_element = new PiBX_ParseTree_ElementNode(array('name' => 'attributeTypeReference', 'type' => 'AttributeTypeReference'), 0);
        $node1_element->setNamespaces($namespaces);

        /*
       <xs:simpleType xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                      xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                      name="SimpleType">
            <xs:restriction base="xs:string">
              <xs:enumeration value="value1"/>
              <xs:enumeration value="value2"/>
            </xs:restriction>
          </xs:simpleType>
        */
        $node2_simpleType = new PiBX_ParseTree_SimpleTypeNode(array('name' => 'SimpleType'), 0);
        $node2_simpleType->setNamespaces($namespaces);
            $node2_restriction = new PiBX_ParseTree_RestrictionNode(array('base' => 'string'), 1);
            $node2_restriction->setNamespaces($namespaces);
                $node2_enumeraton1 = new PiBX_ParseTree_EnumerationNode(array('value' => 'value1'), 2);
                $node2_enumeraton1->setNamespaces($namespaces);
                $node2_enumeraton2 = new PiBX_ParseTree_EnumerationNode(array('value' => 'value2'), 2);
                $node2_enumeraton2->setNamespaces($namespaces);

            $node2_restriction->add($node2_enumeraton1);
            $node2_restriction->add($node2_enumeraton2);
        $node2_simpleType->add($node2_restriction);

        /*
       <xs:complexType xmlns:wsdl11="http://schemas.xmlsoap.org/wsdl/"
                       xmlns:soap11enc="http://schemas.xmlsoap.org/soap/encoding/"
                       name="AttributeTypeReference">
            <xs:sequence>
              <xs:element name="element" type="xs:string"/>
            </xs:sequence>
            <xs:attribute name="attr" type="ex:SimpleType"/>
          </xs:complexType>
        */
        $node3_complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'AttributeTypeReference'),0);
        $node3_complexType->setNamespaces($namespaces);
            $node3_sequence = new PiBX_ParseTree_SequenceNode(array(), 1);
            $node3_sequence->setNamespaces($namespaces);
                $node3_element1 = new PiBX_ParseTree_ElementNode(array('name' => 'element', 'type' => 'string'), 2);
                $node3_element1->setNamespaces($namespaces);
            $node3_attribute = new PiBX_ParseTree_AttributeNode(array('name' => 'attr', 'type' => 'SimpleType'), 1);
            $node3_attribute->setNamespaces($namespaces);

            $node3_sequence->add($node3_element1);
        $node3_complexType->add($node3_sequence);
        $node3_complexType->add($node3_attribute);
        /*
       <xs:element name="echoAttributeTypeReference">
          <xs:complexType>
             <xs:sequence>

                <xs:element ref="ex:attributeTypeReference"/>
             </xs:sequence>
          </xs:complexType>
       </xs:element>
        */
        $node4_element = new PiBX_ParseTree_ElementNode(array('name' => 'echoAttributeTypeReference'), 0);
        $node4_element->setNamespaces($namespaces);
            $node4_complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node4_complexType->setNamespaces($namespaces);
                $node4_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node4_sequence->setNamespaces($namespaces);
                    $node4_element1 = new PiBX_ParseTree_ElementNode(array('type' => 'attributeTypeReference'), 3);
                    $node4_element1->setNamespaces($namespaces);

                $node4_sequence->add($node4_element1);
            $node4_complexType->add($node4_sequence);
        $node4_element->add($node4_complexType);

        
        $tree->add($node1_element);
        $tree->add($node2_simpleType);
        $tree->add($node3_complexType);
        $tree->add($node4_element);
        
        return $tree;
    }
}