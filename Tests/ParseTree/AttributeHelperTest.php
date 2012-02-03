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
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/ParseTree/AttributeHelper.php';

class PiBX_ParseTree_AttributeHelperTest extends PHPUnit_Framework_TestCase {
    public function testTypeAttributeWithNamespace() {
        $simpleXML = simplexml_load_string('<element name="elementName" abstract="false" minOccurs="1" type="ns1:anotherType"/>');

        $options = PiBX_ParseTree_AttributeHelper::getElementOptions($simpleXML);

        $this->assertEquals('anotherType', $options['type']);
    }

    public function testBaseAttributeWithNamespace() {
        $simpleXML = simplexml_load_string('<restriction id="res1" base="ns1:otherType"/>');

        $options = PiBX_ParseTree_AttributeHelper::getRestrictionOptions($simpleXML);

        $this->assertEquals('otherType', $options['base']);
    }

    public function testRefAttributeShouldBeTypeOption() {
        $simpleXML = simplexml_load_string('<element name="elementName" abstract="false" minOccurs="1" ref="ns1:anotherType"/>');

        $options = PiBX_ParseTree_AttributeHelper::getElementOptions($simpleXML);

        $this->assertEquals('anotherType', $options['type']);
    }

    public function testTypeConversions() {
        $simpleXML = simplexml_load_string('<element name="elementName" abstract="false" nillable="true" minOccurs="1" maxOccurs="unbounded"/>');

        $options = PiBX_ParseTree_AttributeHelper::getElementOptions($simpleXML);

        $this->assertEquals('elementName', $options['name']);
        $this->assertFalse($options['abstract']);
        $this->assertTrue($options['nillable']);
        $this->assertEquals(1, $options['minOccurs']);
        $this->assertEquals("unbounded", $options['maxOccurs']);
    }

    public function testElementOptionsWithSimpleXML() {
        $simpleXML = simplexml_load_string('<element name="elementName" abstract="false" minOccurs="1"/>');

        $options = PiBX_ParseTree_AttributeHelper::getElementOptions($simpleXML);

        $this->assertEquals('elementName', $options['name']);
        $this->assertFalse($options['abstract']);
        $this->assertFalse($options['nillable']);
        $this->assertEquals(1, $options['minOccurs']);
        $this->assertTrue(is_int($options['minOccurs']));
    }

    public function testElementOptionsWithArray() {
        $elementOptions = array('name' => 'elementName', 'abstract' => false, 'minOccurs' => 1);
        $options = PiBX_ParseTree_AttributeHelper::getElementOptions($elementOptions);

        $this->assertEquals('elementName', $options['name']);
        $this->assertFalse($options['abstract']);
        $this->assertFalse($options['nillable']);
        $this->assertEquals(1, $options['minOccurs']);
        $this->assertTrue(is_int($options['minOccurs']));
    }

    public function testSimpleTypeWithSimpleXML() {
        $simpleXML = simplexml_load_string('<simpleType name="simpleType" id="ST0001"/>');

        $options = PiBX_ParseTree_AttributeHelper::getSimpleTypeOptions($simpleXML);

        $this->assertEquals('simpleType', $options['name']);
        $this->assertEquals('ST0001', $options['id']);
        $this->assertTrue(!array_key_exists('invalidAttribute', $options));
    }

    public function testSimpleTypeWithArray() {
        $simpleTypeOptions = array('name' => 'simpleType');
        $options = PiBX_ParseTree_AttributeHelper::getSimpleTypeOptions($simpleTypeOptions);

        $this->assertEquals('simpleType', $options['name']);
        $this->assertEmpty($options['id']);
    }

    public function testComplexTypeWithSimpleXML() {
        $simpleXML = simplexml_load_string('<complexType name="complexType" mixed="false"/>');

        $options = PiBX_ParseTree_AttributeHelper::getComplexTypeOptions($simpleXML);

        $this->assertEquals('complexType', $options['name']);
        $this->assertFalse($options['mixed']);
        $this->assertFalse($options['abstract']);
    }

    public function testComplexTypeWithArray() {
        $complexTypeOptions = array('name' => 'complexType', 'mixed' => false);

        $options = PiBX_ParseTree_AttributeHelper::getComplexTypeOptions($complexTypeOptions);

        $this->assertEquals('complexType', $options['name']);
        $this->assertFalse($options['mixed']);
        $this->assertFalse($options['abstract']);
    }

    public function testSequenceWithSimpleXML() {
        $simpleXML = simplexml_load_string('<sequence id="SEQ001"/>');

        $options = PiBX_ParseTree_AttributeHelper::getSequenceOptions($simpleXML);

        $this->assertEquals('SEQ001', $options['id']);
        $this->assertEquals(1, $options['maxOccurs']);
        $this->assertEquals(1, $options['minOccurs']);
    }

    public function testSequenceWithArray() {
        $complexTypeOptions = array('id' => 'SEQ001', 'minOccurs' => '1');

        $options = PiBX_ParseTree_AttributeHelper::getSequenceOptions($complexTypeOptions);

        $this->assertEquals('SEQ001', $options['id']);
        $this->assertEquals(1, $options['maxOccurs']);
        $this->assertEquals(1, $options['minOccurs']);
    }

    public function testAttributeWithSimpleXML() {
        $simpleXML = simplexml_load_string('<attribute name="attribute"/>');

        $options = PiBX_ParseTree_AttributeHelper::getAttributeOptions($simpleXML);

        $this->assertEquals('attribute', $options['name']);
    }

    public function testAttributeWithArray() {
        $attributeOptions = array('name' => 'attribute');

        $options = PiBX_ParseTree_AttributeHelper::getAttributeOptions($attributeOptions);

        $this->assertEquals('attribute', $options['name']);
    }

    public function testRestrictionWithSimpleXML() {
        $simpleXML = simplexml_load_string('<restriction id="res1" base="otherType"/>');

        $options = PiBX_ParseTree_AttributeHelper::getRestrictionOptions($simpleXML);

        $this->assertEquals('res1', $options['id']);
        $this->assertEquals('otherType', $options['base']);
    }

    public function testRestrictionWithArray() {
        $restrictionOptions = array('base' => 'otherType');

        $options = PiBX_ParseTree_AttributeHelper::getRestrictionOptions($restrictionOptions);

        $this->assertEquals('otherType', $options['base']);
    }

    public function testEnumerationWithSimpleXML() {
        $simpleXML = simplexml_load_string('<enumeration value="value"/>');

        $options = PiBX_ParseTree_AttributeHelper::getEnumerationOptions($simpleXML);

        $this->assertEquals('value', $options['value']);
    }

    public function testEnumerationWithArray() {
        $enumerationOptions = array('value' => 'value');

        $options = PiBX_ParseTree_AttributeHelper::getEnumerationOptions($enumerationOptions);

        $this->assertEquals('value', $options['value']);
    }

    public function testComplexContentWithSimpleXML() {
        $simpleXML = simplexml_load_string('<complexContent id="cc1" mixed="true"/>');

        $options = PiBX_ParseTree_AttributeHelper::getComplexContentOptions($simpleXML);

        $this->assertEquals('cc1', $options['id']);
        $this->assertTrue($options['mixed']);
    }

    public function testComplexContentWithArray() {
        $complexContentOptions = array('id' => 'cc1');

        $options = PiBX_ParseTree_AttributeHelper::getComplexContentOptions($complexContentOptions);

        $this->assertEquals('cc1', $options['id']);
        $this->assertFalse($options['mixed']);
    }

    public function testExtensionWithSimpleXML() {
        $simpleXML = simplexml_load_string('<extension id="cc1" base="baseType"/>');

        $options = PiBX_ParseTree_AttributeHelper::getExtensionOptions($simpleXML);

        $this->assertEquals('cc1', $options['id']);
        $this->assertEquals('baseType', $options['base']);
    }

    public function testExtensionWithArray() {
        $extensionOptions = array('base' => 'baseType');

        $options = PiBX_ParseTree_AttributeHelper::getExtensionOptions($extensionOptions);

        $this->assertEquals('baseType', $options['base']);
    }
}
