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
require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
require_once 'PiBX/ParseTree/AttributeNode.php';
require_once 'PiBX/ParseTree/ChoiceNode.php';
require_once 'PiBX/ParseTree/ComplexContentNode.php';
require_once 'PiBX/ParseTree/ComplexTypeNode.php';
require_once 'PiBX/ParseTree/ElementNode.php';
require_once 'PiBX/ParseTree/EnumerationNode.php';
require_once 'PiBX/ParseTree/ExtensionNode.php';
require_once 'PiBX/ParseTree/RestrictionNode.php';
require_once 'PiBX/ParseTree/RootNode.php';
require_once 'PiBX/ParseTree/SequenceNode.php';
require_once 'PiBX/ParseTree/SimpleTypeNode.php';
/**
 * Testing the SchemaParser in scenario "Books".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Books_SchemaParserTest extends PHPUnit_Framework_TestCase {

    public function testScenarioBooksSchema() {
        $filepath = dirname(__FILE__) . '/../../_files/Books/';
        $xml = simplexml_load_file($filepath . '/books.xsd');

        $namespaces = array('xs' => 'http://www.w3.org/2001/XMLSchema');

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($xml);

        $parsedTree = $parser->parse();

        $expectedTree = new PiBX_ParseTree_RootNode();

        $node1_element = new PiBX_ParseTree_ElementNode(array('name' => 'Collection'), 0);
        $node1_element->setNamespaces($namespaces);
            $node1_complexType_1 = new PiBX_ParseTree_ComplexTypeNode(array(), 1);
            $node1_complexType_1->setNamespaces($namespaces);
                $node1_sequence_1 = new PiBX_ParseTree_SequenceNode(array(), 2);
                $node1_sequence_1->setNamespaces($namespaces);
                    $node1_element_1 = new PiBX_ParseTree_ElementNode(array('name' => 'books'), 3);
                    $node1_element_1->setNamespaces($namespaces);
                        $node1_complexType_2 = new PiBX_ParseTree_ComplexTypeNode(array(), 4);
                        $node1_complexType_2->setNamespaces($namespaces);
                            $node1_sequence_2 = new PiBX_ParseTree_SequenceNode(array(), 5);
                            $node1_sequence_2->setNamespaces($namespaces);
                                $node1_element_2 = new PiBX_ParseTree_ElementNode(array('name' => 'book', 'type' => 'bookType', 'maxOccurs' => 'unbounded'), 6);
                                $node1_element_2->setNamespaces($namespaces);

                            $node1_sequence_2->add($node1_element_2);
                        $node1_complexType_2->add($node1_sequence_2);
                    $node1_element_1->add($node1_complexType_2);
                $node1_sequence_1->add($node1_element_1);
            $node1_complexType_1->add($node1_sequence_1);
        $node1_element->add($node1_complexType_1);

        
        $node2_complexType = new PiBX_ParseTree_ComplexTypeNode(array('name' => 'bookType'), 0);
        $node2_complexType->setNamespaces($namespaces);
            $node2_sequence = new PiBX_ParseTree_SequenceNode(array(), 1);
            $node2_sequence->setNamespaces($namespaces);
                $node2_element_1 = new PiBX_ParseTree_ElementNode(array('name' => 'name', 'type' => 'string'), 2);
                $node2_element_1->setNamespaces($namespaces);
                $node2_element_2 = new PiBX_ParseTree_ElementNode(array('name' => 'ISBN', 'type' => 'long'), 2);
                $node2_element_2->setNamespaces($namespaces);
                $node2_element_3 = new PiBX_ParseTree_ElementNode(array('name' => 'price', 'type' => 'string'), 2);
                $node2_element_3->setNamespaces($namespaces);
                $node2_element_4 = new PiBX_ParseTree_ElementNode(array('name' => 'authors'), 2);
                $node2_element_4->setNamespaces($namespaces);
                    $node2_complexType_1 = new PiBX_ParseTree_ComplexTypeNode(array(), 3);
                    $node2_complexType_1->setNamespaces($namespaces);
                        $node2_sequence_1 = new PiBX_ParseTree_SequenceNode(array(), 4);
                        $node2_sequence_1->setNamespaces($namespaces);
                            $node2_element_5 = new PiBX_ParseTree_ElementNode(array('name' => 'authorName', 'type' => 'string', 'maxOccurs' => 'unbounded'), 5);
                            $node2_element_5->setNamespaces($namespaces);
                $node2_element_6 = new PiBX_ParseTree_ElementNode(array('name' => 'description', 'type' => 'string', 'minOccurs' => '0'), 2);
                $node2_element_6->setNamespaces($namespaces);
                $node2_element_7 = new PiBX_ParseTree_ElementNode(array('name' => 'promotion'), 2);
                $node2_element_7->setNamespaces($namespaces);
                    $node2_complexType_2 = new PiBX_ParseTree_ComplexTypeNode(array(), 3);
                    $node2_complexType_2->setNamespaces($namespaces);
                        $node2_choice = new PiBX_ParseTree_ChoiceNode(array(), 4);
                        $node2_choice->setNamespaces($namespaces);
                            $node2_element_8 = new PiBX_ParseTree_ElementNode(array('name' => 'Discount', 'type' => 'string'), 5);
                            $node2_element_8->setNamespaces($namespaces);
                            $node2_element_9 = new PiBX_ParseTree_ElementNode(array('name' => 'None', 'type' => 'string'), 5);
                            $node2_element_9->setNamespaces($namespaces);
                $node2_element_10 = new PiBX_ParseTree_ElementNode(array('name' => 'publicationDate', 'type' => 'date'), 2);
                $node2_element_10->setNamespaces($namespaces);
                $node2_element_11 = new PiBX_ParseTree_ElementNode(array('name' => 'bookCategory'), 2);
                $node2_element_11->setNamespaces($namespaces);
                    $node2_simpleType = new PiBX_ParseTree_SimpleTypeNode(array(), 3);
                    $node2_simpleType->setNamespaces($namespaces);
                        $node2_restriction = new PiBX_ParseTree_RestrictionNode(array('base' => 'NCName'), 4);
                        $node2_restriction->setNamespaces($namespaces);
                            $node2_enumeration_1 = new PiBX_ParseTree_EnumerationNode(array('value' => 'magazine'), 5);
                            $node2_enumeration_1->setNamespaces($namespaces);
                            $node2_enumeration_2 = new PiBX_ParseTree_EnumerationNode(array('value' => 'novel'), 5);
                            $node2_enumeration_2->setNamespaces($namespaces);
                            $node2_enumeration_3 = new PiBX_ParseTree_EnumerationNode(array('value' => 'fiction'), 5);
                            $node2_enumeration_3->setNamespaces($namespaces);
                            $node2_enumeration_4 = new PiBX_ParseTree_EnumerationNode(array('value' => 'other'), 5);
                            $node2_enumeration_4->setNamespaces($namespaces);
            $node2_attribute = new PiBX_ParseTree_AttributeNode(array('name' => 'itemId', 'type' => 'string'), 1);
            $node2_attribute->setNamespaces($namespaces);

        $node2_complexType->add($node2_sequence);
            $node2_sequence->add($node2_element_1);
            $node2_sequence->add($node2_element_2);
            $node2_sequence->add($node2_element_3);
            $node2_sequence->add($node2_element_4);
                $node2_element_4->add($node2_complexType_1);
                    $node2_complexType_1->add($node2_sequence_1);
                        $node2_sequence_1->add($node2_element_5);
            $node2_sequence->add($node2_element_6);
            $node2_sequence->add($node2_element_7);
                $node2_element_7->add($node2_complexType_2);
                    $node2_complexType_2->add($node2_choice);
                        $node2_choice->add($node2_element_8);
                        $node2_choice->add($node2_element_9);
            $node2_sequence->add($node2_element_10);
            $node2_sequence->add($node2_element_11);
                $node2_element_11->add($node2_simpleType);
                    $node2_simpleType->add($node2_restriction);
                        $node2_restriction->add($node2_enumeration_1);
                        $node2_restriction->add($node2_enumeration_2);
                        $node2_restriction->add($node2_enumeration_3);
                        $node2_restriction->add($node2_enumeration_4);
        $node2_complexType->add($node2_attribute);


        $node3_simpleType = new PiBX_ParseTree_SimpleTypeNode(array('name' => 'bookCategoryType'), 0);
        $node3_simpleType->setNamespaces($namespaces);
            $node3_restriction = new PiBX_ParseTree_RestrictionNode(array('base' => 'string'), 1);
            $node3_restriction->setNamespaces($namespaces);
                $node3_enumeration_1 = new PiBX_ParseTree_EnumerationNode(array('value' => 'magazine'), 2);
                $node3_enumeration_1->setNamespaces($namespaces);
                $node3_enumeration_2 = new PiBX_ParseTree_EnumerationNode(array('value' => 'novel'), 2);
                $node3_enumeration_2->setNamespaces($namespaces);
                $node3_enumeration_3 = new PiBX_ParseTree_EnumerationNode(array('value' => 'fiction'), 2);
                $node3_enumeration_3->setNamespaces($namespaces);
                $node3_enumeration_4 = new PiBX_ParseTree_EnumerationNode(array('value' => 'other'), 2);
                $node3_enumeration_4->setNamespaces($namespaces);

            $node3_restriction->add($node3_enumeration_1);
            $node3_restriction->add($node3_enumeration_2);
            $node3_restriction->add($node3_enumeration_3);
            $node3_restriction->add($node3_enumeration_4);
        $node3_simpleType->add($node3_restriction);


        $expectedTree->add($node1_element);
        $expectedTree->add($node2_complexType);
        $expectedTree->add($node3_simpleType);


        $this->assertEquals($expectedTree, $parsedTree);
    }
}
