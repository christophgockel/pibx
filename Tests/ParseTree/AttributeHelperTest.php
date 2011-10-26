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
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/ParseTree/AttributeHelper.php';

class PiBX_ParseTree_AttributeHelperTest extends PHPUnit_Framework_TestCase {
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
}
