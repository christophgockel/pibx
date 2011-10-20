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
 * Implementation of the W3C basic example "GlobalElementComplexType".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Basic_GlobalElementComplexTypeTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Basic/GlobalElementComplexType';
        $this->schemaFile = 'GlobalElementComplexType.xsd';
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

        $type1 = new PiBX_AST_Type('globalElementComplexType');
        $type1->setAsRoot();
        $type1->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type1->setNamespaces($namespaces);
            $type1_structure = new PiBX_AST_Structure('name');
                $type1_structure_element1 = new PiBX_AST_StructureElement('firstName', 'string');
                $type1_structure_element2 = new PiBX_AST_StructureElement('lastName', 'string');

            $type1_structure->add($type1_structure_element1);
            $type1_structure->add($type1_structure_element2);
        $type1->add($type1_structure);

        $type2 = new PiBX_AST_Type('echoGlobalElementComplexType');
        $type2->setAsRoot();
        $type2->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type2->setNamespaces($namespaces);
            $type2_attribute = new PiBX_AST_TypeAttribute('', 'globalElementComplexType');

        $type2->add($type2_attribute);
        
        return array($type1, $type2);
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
                   name="globalElementComplexType">
          <xs:complexType>
            <xs:sequence>
                <xs:element name="name">
                   <xs:complexType>
                      <xs:sequence>
                         <xs:element name="firstName" type="xs:string"/>

                         <xs:element name="lastName" type="xs:string"/>
                      </xs:sequence>
                   </xs:complexType>
                </xs:element>
            </xs:sequence>
          </xs:complexType>
        </xs:element>
         */
        $node1_element = new PiBX_ParseTree_ElementNode(array('name' => 'globalElementComplexType'), 0);
        $node1_element->setNamespaces($namespaces);
            $node1_complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node1_complexType->setNamespaces($namespaces);
                $node1_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node1_sequence->setNamespaces($namespaces);
                    $node1_element1 = new PiBX_ParseTree_ElementNode(array('name' => 'name'), 3);
                    $node1_element1->setNamespaces($namespaces);
                        $node1_complexType1 = new PiBX_ParseTree_ComplexTypeNode(array(), 4);
                        $node1_complexType1->setNamespaces($namespaces);
                            $node1_sequence1 = new PiBX_ParseTree_SequenceNode(array(), 5);
                            $node1_sequence1->setNamespaces($namespaces);
                                $node1_element2 = new PiBX_ParseTree_ElementNode(array('name' => 'firstName', 'type' => 'string'), 6);
                                $node1_element2->setNamespaces($namespaces);
                                $node1_element3 = new PiBX_ParseTree_ElementNode(array('name' => 'lastName', 'type' => 'string'), 6);
                                $node1_element3->setNamespaces($namespaces);

                            $node1_sequence1->add($node1_element2);
                            $node1_sequence1->add($node1_element3);
                        $node1_complexType1->add($node1_sequence1);
                    $node1_element1->add($node1_complexType1);
                $node1_sequence->add($node1_element1);
            $node1_complexType->add($node1_sequence);
        $node1_element->add($node1_complexType);

        /*
       <xs:element name="echoGlobalElementComplexType">
          <xs:complexType>

             <xs:sequence>
                <xs:element ref="ex:globalElementComplexType"/>
             </xs:sequence>
          </xs:complexType>
       </xs:element>
         */
        $node2_element = new PiBX_ParseTree_ElementNode(array('name' => 'echoGlobalElementComplexType'), 0);
        $node2_element->setNamespaces($namespaces);
            $node2_complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node2_complexType->setNamespaces($namespaces);
                $node2_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node2_sequence->setNamespaces($namespaces);
                    $node2_element1 = new PiBX_ParseTree_ElementNode(array('type' => 'globalElementComplexType'), 3);
                    $node2_element1->setNamespaces($namespaces);

                $node2_sequence->add($node2_element1);
            $node2_complexType->add($node2_sequence);
        $node2_element->add($node2_complexType);
        
        $tree->add($node1_element);
        $tree->add($node2_element);
        
        return $tree;
    }
}