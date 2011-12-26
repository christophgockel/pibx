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
require_once 'PiBX/AST/StructureElement.php';
require_once 'PiBX/AST/StructureType.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/ASTOptimizer.php';
require_once 'PiBX/CodeGen/ClassGenerator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
require_once 'PiBX/CodeGen/TypeUsage.php';
require_once 'PiBX/Binding/Creator.php';
/**
 * Implementation of the W3C advanced example "LocalElementComplexTypeEmptyExtension".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Reference_Advanced_LocalElementComplexTypeEmptyExtensionTest extends PiBX_Scenarios_Reference_TestCase {
    public function setUp() {
        $this->pathToTestFiles = dirname(__FILE__) . '/../../../_files/Reference/Advanced/LocalElementComplexTypeEmptyExtension';
        $this->schemaFile = 'LocalElementComplexTypeEmptyExtension.xsd';
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

        $type1 = new PiBX_AST_Type('localElementComplexTypeEmptyExtension');
        $type1->setAsRoot();
        $type1->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type1->setNamespaces($namespaces);
            $type1_attribute = new PiBX_AST_Structure('extensionElement', 'ExtensionType');

        $type1->add($type1_attribute);


        $type2 = new PiBX_AST_Type('ExtensionType');
        $type2->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type2->setNamespaces($namespaces);
            $type2_attribute_1 = new PiBX_AST_TypeAttribute('element1', 'string');
            $type2_attribute_2 = new PiBX_AST_TypeAttribute('element2', 'string');

        $type2->add($type2_attribute_1);
        $type2->add($type2_attribute_2);


        $type3 = new PiBX_AST_Type('echoLocalElementComplexTypeEmptyExtension');
        $type3->setAsRoot();
        $type3->setTargetNamespace('http://www.w3.org/2002/ws/databinding/examples/6/09/');
        $type3->setNamespaces($namespaces);
            $type3_attribute = new PiBX_AST_TypeAttribute('', 'localElementComplexTypeEmptyExtension');

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

        $node1_element = new PiBX_ParseTree_ElementNode(array('name' => 'localElementComplexTypeEmptyExtension'), 0);
        $node1_element->setNamespaces($namespaces);
            $node1_complexType_1 = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node1_complexType_1->setNamespaces($namespaces);
                $node1_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node1_sequence->setNamespaces($namespaces);
                    $node1_element_1 = new PiBX_ParseTree_ElementNode(array('name' => 'extensionElement'), 3);
                    $node1_element_1->setNamespaces($namespaces);
                        $node1_complexType_2 = new PiBX_ParseTree_ComplexTypeNode(array(), 4);
                        $node1_complexType_2->setNamespaces($namespaces);
                            $node1_complexContent = new PiBX_ParseTree_ComplexContentNode(array(), 5);
                            $node1_complexContent->setNamespaces($namespaces);
                                $node1_extension = new PiBX_ParseTree_ExtensionNode(array('base' => 'ExtensionType'), 6);
                                $node1_extension->setNamespaces($namespaces);

                            $node1_complexContent->add($node1_extension);
                        $node1_complexType_2->add($node1_complexContent);
                    $node1_element_1->add($node1_complexType_2);
                $node1_sequence->add($node1_element_1);
            $node1_complexType_1->add($node1_sequence);
        $node1_element->add($node1_complexType_1);


        $node2_complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'ExtensionType'), 0);
        $node2_complexType->setNamespaces($namespaces);
            $node2_sequence = new PiBX_ParseTree_SequenceNode(array(), 1);
            $node2_sequence->setNamespaces($namespaces);
                $node2_element_1 = new PiBX_ParseTree_ElementNode(array('name' => 'element1', 'type' => 'string'), 2);
                $node2_element_1->setNamespaces($namespaces);
                $node2_element_2 = new PiBX_ParseTree_ElementNode(array('name' => 'element2', 'type' => 'string'), 2);
                $node2_element_2->setNamespaces($namespaces);

            $node2_sequence->add($node2_element_1);
            $node2_sequence->add($node2_element_2);
        $node2_complexType->add($node2_sequence);


        $node3_element = new PiBX_ParseTree_ElementNode(array('name' => 'echoLocalElementComplexTypeEmptyExtension'), 0);
        $node3_element->setNamespaces($namespaces);
            $node3_complexType = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node3_complexType->setNamespaces($namespaces);
                $node3_sequence = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node3_sequence->setNamespaces($namespaces);
                    $node3_element1 = new PiBX_ParseTree_ElementNode(array('type' => 'localElementComplexTypeEmptyExtension'), 3);
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