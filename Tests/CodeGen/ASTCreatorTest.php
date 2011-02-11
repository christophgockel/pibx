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
require_once 'PHPUnit/Framework.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
/**
 * Description of ASTCreatorTest
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ASTCreatorTest extends PHPUnit_Framework_TestCase {
    public function testSimpleTypeWithEnumeration() {
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

        $expectedType = new PiBX_AST_Type('bookCategoryType');
        $expectedType->setAsRoot();
        $expectedType->setNamespaces(array('xs' => 'http://www.w3.org/2001/XMLSchema'));
        $enumeration = new PiBX_AST_Enumeration();
        $enum = new PiBX_AST_EnumerationValue('magazine', 'string');
        $enumeration->add($enum);
        $enum = new PiBX_AST_EnumerationValue('novel', 'string');
        $enumeration->add($enum);
        $enum = new PiBX_AST_EnumerationValue('fiction', 'string');
        $enumeration->add($enum);
        $enum = new PiBX_AST_EnumerationValue('other', 'string');
        $enumeration->add($enum);
        $expectedType->add($enumeration);

        $schema = simplexml_load_string($data);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        list($type) = $typeList;

        $this->assertEquals($expectedType, $type);
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

        $expectedType = new PiBX_AST_Type('Collection');
        $expectedType->setAsRoot();
        $expectedType->setAttributeCount(1);
        $expectedType->setNamespaces(array('xs' => 'http://www.w3.org/2001/XMLSchema'));
        $ta = new PiBX_AST_TypeAttribute('books');
        $c = new PiBX_AST_Collection();
        $ci = new PiBX_AST_CollectionItem('book');
        $ci->setType('bookType');
        $expectedType->add($ta);
        $ta->add($c);
        $c->add($ci);

        $schema = simplexml_load_string($data);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        list($type) = $typeList;

        $this->assertEquals($expectedType, $type);
    }

    public function testComplexTypeWithElements() {
        $data = <<<XML
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xs:complexType name="complexTypeWithElements">
        <xs:sequence>
            <xs:element name="element1" type="xs:string"/>
            <xs:element name="element2" type="xs:long"/>
            <xs:element name="element3" type="xs:string"/>
            <xs:element name="element4" type="xs:string"  minOccurs="0"/>
            <xs:element name="element5" type="xs:date"/>
        </xs:sequence>
    </xs:complexType>
</xs:schema>
XML;

        $expectedType = new PiBX_AST_Type('complexTypeWithElements');
        $expectedType->setAsRoot();
        $expectedType->setAttributeCount(5);
        $expectedType->setNamespaces(array('xs' => 'http://www.w3.org/2001/XMLSchema'));
        $attr = new PiBX_AST_TypeAttribute('element1');
        $attr->setType('string');
        $expectedType->add($attr);
        $attr = new PiBX_AST_TypeAttribute('element2');
        $attr->setType('long');
        $expectedType->add($attr);
        $attr = new PiBX_AST_TypeAttribute('element3');
        $attr->setType('string');
        $expectedType->add($attr);
        $attr = new PiBX_AST_TypeAttribute('element4');
        $attr->setType('string');
        $expectedType->add($attr);
        $attr = new PiBX_AST_TypeAttribute('element5');
        $attr->setType('date');
        $expectedType->add($attr);

        $schema = simplexml_load_string($data);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        list($type) = $typeList;

        $this->assertEquals($expectedType, $type);
    }

    public function testComplexTypeWithElementsAndSequence() {
        $data = <<<XML
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xs:complexType name="complexTypeWithElements">
        <xs:sequence>
            <xs:element name="element1" type="xs:string"/>
            <xs:element name="element2" type="xs:long"/>
            <xs:element name="element3" type="xs:string"/>
            <xs:element name="element4" type="xs:string"  minOccurs="0"/>
            <xs:element name="elements" >
                <xs:complexType>
                    <xs:sequence>
                        <xs:element name="element" type="xs:string" minOccurs="1" maxOccurs="unbounded"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
            <xs:element name="element6" type="xs:date"/>
        </xs:sequence>
    </xs:complexType>
</xs:schema>
XML;

        $expectedType = new PiBX_AST_Type('complexTypeWithElements');
        $expectedType->setAsRoot();
        $expectedType->setAttributeCount(6);
        $expectedType->setNamespaces(array('xs' => 'http://www.w3.org/2001/XMLSchema'));
        $attr = new PiBX_AST_TypeAttribute('element1');
        $attr->setType('string');
        $expectedType->add($attr);
        $attr = new PiBX_AST_TypeAttribute('element2');
        $attr->setType('long');
        $expectedType->add($attr);
        $attr = new PiBX_AST_TypeAttribute('element3');
        $attr->setType('string');
        $expectedType->add($attr);
        $attr = new PiBX_AST_TypeAttribute('element4');
        $attr->setType('string');
        $expectedType->add($attr);

        $attr = new PiBX_AST_TypeAttribute('elements');
        $collection = new PiBX_AST_Collection('');
        $collectionItem = new PiBX_AST_CollectionItem('element');
        $collectionItem->setType('string');
        $collection->add($collectionItem);
        $attr->add($collection);
        $expectedType->add($attr);

        $attr = new PiBX_AST_TypeAttribute('element6');
        $attr->setType('date');
        $expectedType->add($attr);

        $schema = simplexml_load_string($data);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        list($type) = $typeList;

        $this->assertEquals($expectedType, $type);
    }

    public function testBooksExampleXSD() {
        $data = file_get_contents(dirname(__FILE__) . '/../_files/Books/books.xsd');
        $expectedTypeList = array();

        $expectedType1 = new PiBX_AST_Type('Collection');
        $expectedType1->setAsRoot();
        $expectedType1->setAttributeCount(1);
        $expectedType1->setNamespaces(array('xs' => 'http://www.w3.org/2001/XMLSchema'));
        $ta = new PiBX_AST_TypeAttribute('books');
        $c = new PiBX_AST_Collection();
        $ci = new PiBX_AST_CollectionItem('book');
        $ci->setType('bookType');
        $expectedType1->add($ta);
        $ta->add($c);
        $c->add($ci);

        $expectedTypeList[] = $expectedType1;

        $expectedType2 = new PiBX_AST_Type('bookType');
        $expectedType2->setAttributeCount(8);
        
        $expectedType2->add(new PiBX_AST_TypeAttribute('name', 'string'));
        $expectedType2->add(new PiBX_AST_TypeAttribute('ISBN', 'long'));
        $expectedType2->add(new PiBX_AST_TypeAttribute('price', 'string'));

        $ta = new PiBX_AST_TypeAttribute('authors');
            $c = new PiBX_AST_Collection();
            $ci = new PiBX_AST_CollectionItem('authorName', 'string');
            $c->add($ci);
            $ta->add($c);
        $expectedType2->add($ta);
        
        $expectedType2->add(new PiBX_AST_TypeAttribute('description', 'string'));

        $ta = new PiBX_AST_TypeAttribute('promotion');
            $s = new PiBX_AST_Structure();
            $s->setStructureType(PiBX_AST_StructureType::CHOICE());
            $s->add(new PiBX_AST_StructureElement('Discount', 'string'));
            $s->add(new PiBX_AST_StructureElement('None', 'string'));
            $ta->add($s);
        $expectedType2->add($ta);
        
        $expectedType2->add(new PiBX_AST_TypeAttribute('publicationDate', 'date'));
        
        $ta = new PiBX_AST_TypeAttribute('bookCategory');
            $e = new PiBX_AST_Enumeration();
            $e->add(new PiBX_AST_EnumerationValue('magazine', 'string'));
            $e->add($ev = new PiBX_AST_EnumerationValue('novel', 'string'));
            $e->add($ev = new PiBX_AST_EnumerationValue('fiction', 'string'));
            $e->add($ev = new PiBX_AST_EnumerationValue('other', 'string'));
            $ta->add($e);
        $expectedType2->add($ta);

        $ta = new PiBX_AST_TypeAttribute('itemId', 'string');
            $ta->setStyle('attribute');
        $expectedType2->add($ta);
        
        $expectedTypeList[] = $expectedType2;

        $enumeration = new PiBX_AST_Enumeration();
        $enumeration->add(new PiBX_AST_EnumerationValue('magazine', 'string'));
        $enumeration->add(new PiBX_AST_EnumerationValue('novel', 'string'));
        $enumeration->add(new PiBX_AST_EnumerationValue('fiction', 'string'));
        $enumeration->add(new PiBX_AST_EnumerationValue('other', 'string'));
        $expectedType3 = new PiBX_AST_Type('bookCategoryType');
        $expectedType3->add($enumeration);

        $expectedTypeList[] = $expectedType3;

        $schema = simplexml_load_string($data);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        
        $this->assertEquals(3, count($typeList));

        $this->assertEquals($expectedType1, $typeList[0]);
        $this->assertEquals($expectedType2, $typeList[1]);
        $this->assertEquals($expectedType3, $typeList[2]);
    }
}
