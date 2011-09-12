<?php
/**
 * Copyright (c) 2010, Christoph Gockel.
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
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
/**
 * Testing the SchemaParser.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_SchemaParserTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidSchemaFile() {
        $parser = new PiBX_CodeGen_SchemaParser('invalidfile.xsd');
    }

    public function testEmptySchemaFileParameter() {
        $parser = new PiBX_CodeGen_SchemaParser('');
        $this->assertTrue($parser instanceof PiBX_CodeGen_SchemaParser);
    }

    public function testComplexTypeWithUnboundedSequence() {
        $data = <<<XML
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:element name="Collection">
    <xs:complexType>
      <xs:sequence>
        <xs:element name ="books">
          <xs:complexType>
            <xs:sequence>
              <xs:element name="book" type="bookType" minOccurs="1" maxOccurs="unbounded"/>
            </xs:sequence>
          </xs:complexType>
        </xs:element>
      </xs:sequence>
    </xs:complexType>
  </xs:element>
</xs:schema>
XML;
        $xml = simplexml_load_string($data);
        
        // build the parse-tree
        $path = '/xs:schema/xs:element';
        list($element) = $xml->xpath($path);
        $collectionElement = new PiBX_ParseTree_ElementNode($element, 0);

        $path = '/xs:schema/xs:element/xs:complexType';
        list($element) = $xml->xpath($path);
        $collectionComplexType = new PiBX_ParseTree_ComplexTypeNode($element, 1);

        $path = '/xs:schema/xs:element/xs:complexType/xs:sequence';
        list($element) = $xml->xpath($path);
        $booksSequence = new PiBX_ParseTree_SequenceNode($element, 2);

        $path = '/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element';
        list($element) = $xml->xpath($path);
        $booksElement = new PiBX_ParseTree_ElementNode($element, 3);

        $path = '/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element/xs:complexType';
        list($element) = $xml->xpath($path);
        $booksComplexType = new PiBX_ParseTree_ComplexTypeNode($element, 4);

        $path = '/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element/xs:complexType/xs:sequence';
        list($element) = $xml->xpath($path);
        $sequence = new PiBX_ParseTree_SequenceNode($element, 5);
        
        $path = '/xs:schema/xs:element/xs:complexType/xs:sequence/xs:element/xs:complexType/xs:sequence/xs:element';
        list($element) = $xml->xpath($path);
        $sequenceElement = new PiBX_ParseTree_ElementNode($element, 6);
        
        // and add everything according to the hierarchy
        $collectionElement->add(
            $collectionComplexType->add(
                $booksSequence->add(
                    $booksElement->add(
                        $booksComplexType->add(
                            $sequence->add(
                                $sequenceElement
                            )
                        )
                    )
                )
            )
        );
        
        $expectedTree = new PiBX_ParseTree_RootNode();
        $expectedTree->add($collectionElement);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($xml);

        $parsedTree = $parser->parse();

        $this->assertTrue($parsedTree instanceof PiBX_ParseTree_Tree);
        $this->assertTrue($expectedTree instanceof PiBX_ParseTree_Tree);

        $this->assertEquals($expectedTree, $parsedTree);
    }

    public function testSimpleTypeWithEnum() {
        $data = <<<XML
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xs:simpleType name="bookCategoryType">
        <xs:restriction base="string">
            <xs:enumeration value="magazine"/>
            <xs:enumeration value="novel"/>
            <xs:enumeration value="fiction"/>
            <xs:enumeration value="other"/>
        </xs:restriction>
    </xs:simpleType>
</xs:schema>
XML;
        $schema = simplexml_load_string($data);

        list($simpleType)  = $schema->children('xs', true);
        list($restriction) = $simpleType->children('xs', true);

        $enum = array();
        foreach ($restriction->children('xs', true) as $child) {
            $enum[] = $child;
        }

        $simpleTypeTree = new PiBX_ParseTree_SimpleTypeNode($simpleType, 0);
        $restrictionTree = new PiBX_ParseTree_RestrictionNode($restriction, 1);
        $enum1 = new PiBX_ParseTree_EnumerationNode($enum['0'], 2);
        $restrictionTree->add($enum1);
        $enum2 = new PiBX_ParseTree_EnumerationNode($enum['1'], 2);
        $restrictionTree->add($enum2);
        $enum3 = new PiBX_ParseTree_EnumerationNode($enum['2'], 2);
        $restrictionTree->add($enum3);
        $enum4 = new PiBX_ParseTree_EnumerationNode($enum['3'], 2);
        $restrictionTree->add($enum4);

        $simpleTypeTree->add($restrictionTree);

        $expectedTree = new PiBX_ParseTree_RootNode();
        $expectedTree->add($simpleTypeTree);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);

        $parsedTree = $parser->parse();

        $this->assertTrue($parsedTree instanceof PiBX_ParseTree_Tree);
        $this->assertTrue($expectedTree instanceof PiBX_ParseTree_Tree);

        $this->assertEquals('magazine', $enum1->getValue());
        $this->assertEquals('novel', $enum2->getValue());
        $this->assertEquals('fiction', $enum3->getValue());
        $this->assertEquals('other', $enum4->getValue());
        $this->assertEquals('bookCategoryType', $simpleTypeTree->getName());

        $this->assertEquals($expectedTree, $parsedTree);
    }

    public function testElementWithChoice() {
        $data = <<<XML
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:element name="promotion">
    <xs:complexType>
      <xs:choice>
        <xs:element name="Discount" type="xs:string" />
        <xs:element name="None" type="xs:string"/>
      </xs:choice>
    </xs:complexType>
  </xs:element>
</xs:schema>
XML;
        $xml = simplexml_load_string($data);

        // build the parse-tree
        $path = '/xs:schema/xs:element';
        list($element) = $xml->xpath($path);
        $promotionElement = new PiBX_ParseTree_ElementNode($element, 0);

        $path = '/xs:schema/xs:element/xs:complexType';
        list($element) = $xml->xpath($path);
        $complexType = new PiBX_ParseTree_ComplexTypeNode($element, 1);

        $path = '/xs:schema/xs:element/xs:complexType/xs:choice';
        list($element) = $xml->xpath($path);
        $choice = new PiBX_ParseTree_ChoiceNode($element, 2);

        $path = '/xs:schema/xs:element/xs:complexType/xs:choice/xs:element[1]';
        list($element) = $xml->xpath($path);
        $element1 = new PiBX_ParseTree_ElementNode($element, 3);

        $path = '/xs:schema/xs:element/xs:complexType/xs:choice/xs:element[2]';
        list($element) = $xml->xpath($path);
        $element2 = new PiBX_ParseTree_ElementNode($element, 3);

        $choice->add($element1);
        $choice->add($element2);
        $complexType->add($choice);
        $promotionElement->add($complexType);
        
        $expectedTree = new PiBX_ParseTree_RootNode();
        $expectedTree->add($promotionElement);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($xml);

        $parsedTree = $parser->parse();

        $this->assertTrue($parsedTree instanceof PiBX_ParseTree_Tree);
        $this->assertTrue($expectedTree instanceof PiBX_ParseTree_Tree);

        $this->assertEquals($expectedTree, $parsedTree);
    }

    public function _testScenarioBooksSchema() {
        $filepath = dirname(__FILE__) . '/../_files/Books/';
        $xml = simplexml_load_file($filepath . '/books.xsd');

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($xml);

        $parsedTree = $parser->parse();
    }
}
