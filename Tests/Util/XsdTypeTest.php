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
require_once 'PiBX/Util/XsdType.php';
/**
 * @author Christoph Gockel
 */
class PiBX_Util_XsdTypeTest extends PHPUnit_Framework_TestCase {
    public function testAllEnumsShouldHaveTheirProperValues() {
        $this->assertEquals(PiBX_Util_XsdType::STRING(), 'string');
        $this->assertEquals(PiBX_Util_XsdType::BOOLEAN(), 'boolean');
        $this->assertEquals(PiBX_Util_XsdType::DECIMAL(), 'decimal');
        $this->assertEquals(PiBX_Util_XsdType::FLOAT(), 'float');
        $this->assertEquals(PiBX_Util_XsdType::DOUBLE(), 'double');
        $this->assertEquals(PiBX_Util_XsdType::DURATION(), 'duration');
        $this->assertEquals(PiBX_Util_XsdType::DATETIME(), 'dateTime');
        $this->assertEquals(PiBX_Util_XsdType::TIME(), 'time');
        $this->assertEquals(PiBX_Util_XsdType::DATE(), 'date');
        $this->assertEquals(PiBX_Util_XsdType::GYEARMONTH(), 'gYearMonth');
        $this->assertEquals(PiBX_Util_XsdType::GYEAR(), 'gYear');
        $this->assertEquals(PiBX_Util_XsdType::GMONTHDAY(), 'gMonthDay');
        $this->assertEquals(PiBX_Util_XsdType::GDAY(), 'gDay');
        $this->assertEquals(PiBX_Util_XsdType::GMONTH(), 'gMonth');
        $this->assertEquals(PiBX_Util_XsdType::HEXBINARY(), 'hexBinary');
        $this->assertEquals(PiBX_Util_XsdType::BASE64BINARY(), 'base64Binary');
        $this->assertEquals(PiBX_Util_XsdType::ANYURI(), 'anyURI');
        $this->assertEquals(PiBX_Util_XsdType::QNAME(), 'QName');
        $this->assertEquals(PiBX_Util_XsdType::NOTATION(), 'NOTATION');
        $this->assertEquals(PiBX_Util_XsdType::NORMALIZEDSTRING(), 'normalizedString');
        $this->assertEquals(PiBX_Util_XsdType::TOKEN(), 'token');
        $this->assertEquals(PiBX_Util_XsdType::LANGUAGE(), 'language');
        $this->assertEquals(PiBX_Util_XsdType::NMTOKEN(), 'NMTOKEN');
        $this->assertEquals(PiBX_Util_XsdType::NMTOKENS(), 'NMTOKENS');
        $this->assertEquals(PiBX_Util_XsdType::NAME(), 'Name');
        $this->assertEquals(PiBX_Util_XsdType::NCNAME(), 'NCName');
        $this->assertEquals(PiBX_Util_XsdType::ID(), 'ID');
        $this->assertEquals(PiBX_Util_XsdType::IDREF(), 'IDREF');
        $this->assertEquals(PiBX_Util_XsdType::IDREFS(), 'IDREFS');
        $this->assertEquals(PiBX_Util_XsdType::ENTITY(), 'ENTITY');
        $this->assertEquals(PiBX_Util_XsdType::ENTITIES(), 'ENTITIES');
        $this->assertEquals(PiBX_Util_XsdType::INTEGER(), 'integer');
        $this->assertEquals(PiBX_Util_XsdType::NONPOSITIVEINTEGER(), 'nonPositiveInteger');
        $this->assertEquals(PiBX_Util_XsdType::NEGATIVEINTEGER(), 'negativeInteger');
        $this->assertEquals(PiBX_Util_XsdType::LONG(), 'long');
        $this->assertEquals(PiBX_Util_XsdType::INT(), 'int');
        $this->assertEquals(PiBX_Util_XsdType::SHORT(), 'short');
        $this->assertEquals(PiBX_Util_XsdType::BYTE(), 'byte');
        $this->assertEquals(PiBX_Util_XsdType::NONNEGATIVEINTEGER(), 'nonNegativeInteger');
        $this->assertEquals(PiBX_Util_XsdType::UNSIGNEDLONG(), 'unsignedLong');
        $this->assertEquals(PiBX_Util_XsdType::UNSIGNEDINT(), 'unsignedInt');
        $this->assertEquals(PiBX_Util_XsdType::UNSIGNEDSHORT(), 'unsignedShort');
        $this->assertEquals(PiBX_Util_XsdType::UNSIGNEDBYTE(), 'unsignedByte');
        $this->assertEquals(PiBX_Util_XsdType::POSITIVEINTEGER(), 'positiveInteger');
    }
}
