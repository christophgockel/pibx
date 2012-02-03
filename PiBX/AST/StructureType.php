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
require_once 'PiBX/AST/Tree.php';
/**
 * An enumeration of available structure types.
 *
 * There are three possible structure types: ordered, choice and a default or "none".
 *
 * It's a PHP adaption of the "Typesafe enum pattern".
 * (@see <link>http://java.sun.com/developer/Books/shiftintojava/page1.html#replaceenums</link>)
 * Since PHP doesn't have static initializers, this construct has to be simulated
 * via a static method call.
 * 
 * @author Christoph Gockel
 */
class PiBX_AST_StructureType {
    private static $ordered;
    private static $choice;
    private static $standard;

    private $value;
    private static $alreadyInitialized = false;

    private function __construct($value) {
        $this->value = $value;
    }

    public static function init() {

        if (!self::$alreadyInitialized) {
            self::$ordered  = new PiBX_AST_StructureType('ordered');
            self::$choice   = new PiBX_AST_StructureType('choice');
            self::$standard = new PiBX_AST_StructureType('');

            self::$alreadyInitialized = true;
        }
    }

    public static function ORDERED() {
        return self::$ordered;
    }

    public static function CHOICE() {
        return self::$choice;
    }

    public static function STANDARD() {
        return self::$standard;
    }
}

// simulate static initializers
PiBX_AST_StructureType::init();