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

/**
 * BaseType is an Enum of available types.
 * 
 * @author Christoph Gockel
 */
class PiBX_ParseTree_BaseType {

    // primitive datatypes
    // according to http://www.w3.org/TR/xmlschema-2/
    private static $string;
    private static $boolean;
    private static $decimal;
    private static $float;
    private static $double;
    private static $duration;
    private static $dateTime;
    private static $time;
    private static $date;
    private static $gYearMonth;
    private static $gYear;
    private static $gMonthDay;
    private static $gDay;
    private static $gMonth;
    private static $hexBinary;
    private static $base64Binary;
    private static $anyURI;
    private static $QName;
    private static $NOTATION;

    // derived datatypes
    private static $normalizedString;
    private static $token;
    private static $language;
    private static $NMTOKEN;
    private static $NMTOKENS;
    private static $Name;
    private static $NCName;
    private static $ID;
    private static $IDREF;
    private static $IDREFS;
    private static $ENTITY;
    private static $ENTITIES;
    private static $integer;
    private static $nonPositiveInteger;
    private static $negativeInteger;
    private static $long;
    private static $int;
    private static $short;
    private static $byte;
    private static $nonNegativeInteger;
    private static $unsignedLong;
    private static $unsignedInt;
    private static $unsignedShort;
    private static $unsignedByte;
    private static $positiveInteger;

    private $value;
    private static $alreadyInitialized = false;

    private function __construct($value) {

        $this->value = $value;
    }

    public static function init() {

        if (!self::$alreadyInitialized) {
            self::$string       = new PiBX_ParseTree_BaseType('string');
            self::$boolean      = new PiBX_ParseTree_BaseType('boolean');
            self::$decimal      = new PiBX_ParseTree_BaseType('decimal');
            self::$float        = new PiBX_ParseTree_BaseType('float');
            self::$double       = new PiBX_ParseTree_BaseType('double');
            self::$duration     = new PiBX_ParseTree_BaseType('duration');
            self::$dateTime     = new PiBX_ParseTree_BaseType('dateTime');
            self::$time         = new PiBX_ParseTree_BaseType('time');
            self::$date         = new PiBX_ParseTree_BaseType('date');
            self::$gYearMonth   = new PiBX_ParseTree_BaseType('gYearMonth');
            self::$gYear        = new PiBX_ParseTree_BaseType('gYear');
            self::$gMonthDay    = new PiBX_ParseTree_BaseType('gMonthDay');
            self::$gDay         = new PiBX_ParseTree_BaseType('gDay');
            self::$gMonth       = new PiBX_ParseTree_BaseType('gMonth');
            self::$hexBinary    = new PiBX_ParseTree_BaseType('hexBinary');
            self::$base64Binary = new PiBX_ParseTree_BaseType('base64Binary');
            self::$anyURI       = new PiBX_ParseTree_BaseType('anyURI');
            self::$QName        = new PiBX_ParseTree_BaseType('QName');
            self::$NOTATION     = new PiBX_ParseTree_BaseType('NOTATION');

            self::$normalizedString   = new PiBX_ParseTree_BaseType('normalizedString');
            self::$token              = new PiBX_ParseTree_BaseType('token');
            self::$language           = new PiBX_ParseTree_BaseType('language');
            self::$NMTOKEN            = new PiBX_ParseTree_BaseType('NMTOKEN');
            self::$NMTOKENS           = new PiBX_ParseTree_BaseType('NMTOKENS');
            self::$Name               = new PiBX_ParseTree_BaseType('Name');
            self::$NCName             = new PiBX_ParseTree_BaseType('NCName');
            self::$ID                 = new PiBX_ParseTree_BaseType('ID');
            self::$IDREF              = new PiBX_ParseTree_BaseType('IDREF');
            self::$IDREFS             = new PiBX_ParseTree_BaseType('IDREFS');
            self::$ENTITY             = new PiBX_ParseTree_BaseType('ENTITY');
            self::$ENTITIES           = new PiBX_ParseTree_BaseType('ENTITIES');
            self::$integer            = new PiBX_ParseTree_BaseType('integer');
            self::$nonPositiveInteger = new PiBX_ParseTree_BaseType('nonPositiveInteger');
            self::$negativeInteger    = new PiBX_ParseTree_BaseType('negativeInteger');
            self::$long               = new PiBX_ParseTree_BaseType('long');
            self::$int                = new PiBX_ParseTree_BaseType('int');
            self::$short              = new PiBX_ParseTree_BaseType('short');
            self::$byte               = new PiBX_ParseTree_BaseType('byte');
            self::$nonNegativeInteger = new PiBX_ParseTree_BaseType('nonNegativeInteger');
            self::$unsignedLong       = new PiBX_ParseTree_BaseType('unsignedLong');
            self::$unsignedInt        = new PiBX_ParseTree_BaseType('unsignedInt');
            self::$unsignedShort      = new PiBX_ParseTree_BaseType('unsignedShort');
            self::$unsignedByte       = new PiBX_ParseTree_BaseType('unsignedByte');
            self::$positiveInteger    = new PiBX_ParseTree_BaseType('positiveInteger');

            self::$alreadyInitialized = true;
        }
    }

    public static function isBaseType($type) {
        $r = new ReflectionClass('PiBX_ParseTree_BaseType');
        $props = $r->getProperties(ReflectionProperty::IS_STATIC);
        
        foreach ($props as &$prop) {
            $name = $prop->getName();
            
            if ($name == 'alreadyInitialized') {
                continue;
            }
            
            if (self::$$name == $type) {
                return true;
            }
        }
        
        return false;
    }

    public function __toString() {
        return $this->value;
    }

    public static function STRING() { return (string)self::$string; }
    public static function BOOLEAN() { return (string)self::$boolean; }
    public static function DECIMAL() { return (string)self::$decimal; }
    public static function FLOAT() { return (string)self::$float; }
    public static function DOUBLE() { return (string)self::$double; }
    public static function DURATION() { return (string)self::$duration; }
    public static function DATETIME() { return (string)self::$dateTime; }
    public static function TIME() { return (string)self::$time; }
    public static function DATE() { return (string)self::$date; }
    public static function GYEARMONTH() { return (string)self::$gYearMonth; }
    public static function GYEAR() { return (string)self::$gYear; }
    public static function GMONTHDAY() { return (string)self::$gMonthDay; }
    public static function GDAY() { return (string)self::$gDay; }
    public static function GMONTH() { return (string)self::$gMonth; }
    public static function HEXBINARY() { return (string)self::$hexBinary; }
    public static function BASE64BINARY() { return (string)self::$base64Binary; }
    public static function ANYURI() { return (string)self::$anyURI; }
    public static function QNAME() { return (string)self::$QName; }
    public static function NOTATION() { return (string)self::$NOTATION; }

    public static function NORMALIZEDSTRING() { return (string)self::$normalizedString; }
    public static function TOKEN() { return (string)self::$token; }
    public static function LANGUAGE() { return (string)self::$language; }
    public static function NMTOKEN() { return (string)self::$NMTOKEN; }
    public static function NMTOKENS() { return (string)self::$NMTOKENS; }
    public static function NAME() { return (string)self::$Name; }
    public static function NCNAME() { return (string)self::$NCName; }
    public static function ID() { return (string)self::$ID; }
    public static function IDREF() { return (string)self::$IDREF; }
    public static function IDREFS() { return (string)self::$IDREFS; }
    public static function ENTITY() { return (string)self::$ENTITY; }
    public static function ENTITIES() { return (string)self::$ENTITIES; }
    public static function INTEGER() { return (string)self::$integer; }
    public static function NONPOSITIVEINTEGER() { return (string)self::$nonPositiveInteger; }
    public static function NEGATIVEINTEGER() { return (string)self::$negativeInteger; }
    public static function LONG() { return (string)self::$long; }
    public static function INT() { return (string)self::$int; }
    public static function SHORT() { return (string)self::$short; }
    public static function BYTE() { return (string)self::$byte; }
    public static function NONNEGATIVEINTEGER() { return (string)self::$nonNegativeInteger; }
    public static function UNSIGNEDLONG() { return (string)self::$unsignedLong; }
    public static function UNSIGNEDINT() { return (string)self::$unsignedInt; }
    public static function UNSIGNEDSHORT() { return (string)self::$unsignedShort; }
    public static function UNSIGNEDBYTE() { return (string)self::$unsignedByte; }
    public static function POSITIVEINTEGER() { return (string)self::$positiveInteger; }

}

// simulate static initializer
PiBX_ParseTree_BaseType::init();