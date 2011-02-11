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

/**
 * BaseType is an Enum of available types.
 * 
 * @author Christoph Gockel
 */
class PiBX_ParseTree_BaseType {

    private static $string;
    private static $int;
    private static $integer;
    private static $float;
    private static $long;
    private static $boolean;
    private static $date;
    private static $dateTime;
    private static $time;
    private static $decimal;

    private $value;
    private static $alreadyInitialized = false;

    private function __construct($value) {

        $this->value = $value;
    }

    public static function init() {

        if (!self::$alreadyInitialized) {
            self::$string  = new PiBX_ParseTree_BaseType('string');
            self::$int     = new PiBX_ParseTree_BaseType('int');
            self::$integer = new PiBX_ParseTree_BaseType('integer');
            self::$float   = new PiBX_ParseTree_BaseType('float');
            self::$long    = new PiBX_ParseTree_BaseType('long');
            self::$boolean = new PiBX_ParseTree_BaseType('boolean');
            self::$date    = new PiBX_ParseTree_BaseType('date');
            self::$dateTime= new PiBX_ParseTree_BaseType('dateTime');
            self::$time    = new PiBX_ParseTree_BaseType('time');
            self::$decimal = new PiBX_ParseTree_BaseType('decimal');

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

    public static function STRING() {

        return self::$string;
    }

    public static function INTEGER() {

        return self::$integer;
    }

    public static function FLOAT() {

        return self::$float;
    }

    public static function BOOLEAN() {
        return self::$boolean;
    }

    public static function DATE() {
        return self::$date;
    }

    public static function TIME() {
        return self::$time;
    }

    public function __toString() {

        return $this->value;
    }
}

// simulate static initializer
PiBX_ParseTree_BaseType::init();