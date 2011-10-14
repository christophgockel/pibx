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
require_once 'PiBX/ParseTree/Tree.php';
/**
 * A factory for creating option-arrays for element attributes of the Parse Tree.
 *
 * @author Christoph Gockel
 */
class PiBX_ParseTree_AttributeHelper {
    protected $name;
    protected $type;
    protected $minOccurs;
    protected $maxOccurs;

    public function  __construct(SimpleXMLElement $xml, $level = 0) {
        parent::__construct($xml, $level);
        $attributes = $xml->attributes();

        $this->name = (string)$attributes['name'];
        $this->type = (string)$attributes['type'];
        if (strpos($this->type, ':') !== false) {
            // remove the namespace prefix
            $parts = explode(':', $this->type);
            $this->type = $parts[1];
        }
        $this->minOccurs = (string)$attributes['minOccurs'];
        $this->maxOccurs = (string)$attributes['maxOccurs'];
    }

    public static function getAttributesForXML(SimpleXMLElement $xml) {
        
    }

    private static function getValue($array, $key) {
        return key_exists($key, $array) ? $array[$key] : '';
    }

    public static function getElementOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();

            $options['name'] = (string)$attributes['name'];
            $options['type'] = (string)$attributes['type'];
            if (empty($options['type'])) {
                $options['type'] = (string)$attributes['ref'];
            }
            if (strpos($options['type'], ':') !== false) {
                // remove the namespace prefix
                $parts = explode(':', $options['type']);
                $options['type'] = $parts[1];
            }
            $options['minOccurs'] = ((string)$attributes['minOccurs'] != '') ? (string)$attributes['minOccurs'] : 1;
            $options['maxOccurs'] = ((string)$attributes['maxOccurs'] != '') ? (string)$attributes['maxOccurs'] : 1;
            $options['nillable']  = ((string)$attributes['nillable'] == 'true') ? true : false;
            $options['id']        = (string)$attributes['id'];
            $options['abstract']  = ((string)$attributes['abstract'] == 'true') ? true : false;
        } else {
            $options['name'] = self::getValue($objectOrArray, 'name');
            $options['type'] = self::getValue($objectOrArray, 'type');
            $options['minOccurs'] = array_key_exists('minOccurs', $objectOrArray) ? self::getValue($objectOrArray, 'minOccurs') : 1;
            $options['maxOccurs'] = array_key_exists('maxOccurs', $objectOrArray) ? self::getValue($objectOrArray, 'maxOccurs') : 1;
            $options['nillable']  = array_key_exists('nillable', $objectOrArray) ? ($objectOrArray['nillable'] == 'true') : false;
            $options['id']        = self::getValue($objectOrArray, 'id');
            $options['abstract']  = array_key_exists('abstract', $objectOrArray) ? ($objectOrArray['abstract'] == 'true') : false;
        }
        
        return $options;
    }

    public static function getSimpleTypeOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();

            $options['name'] = (string)$attributes['name'];
        } else {
            $options['name'] = self::getValue($objectOrArray, 'name');
        }

        return $options;
    }

    public static function getComplexTypeOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();

            $options['name'] = (string)$attributes['name'];
            $options['id']   = (string)$attributes['id'];
            $options['abstract'] = ((string)$attributes['abstract'] == 'true') ? true : false;
        } else {
            $options['name'] = self::getValue($objectOrArray, 'name');
            $options['id']   = self::getValue($objectOrArray, 'id');
            $options['abstract'] = array_key_exists('abstract', $objectOrArray) ? ($objectOrArray['abstract'] == 'true') : false;
        }

        return $options;
    }

    public static function getSequenceOptions($objectOrArray) {
        $options = array();
        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();

            $options['minOccurs'] = ((string)$attributes['minOccurs'] != '') ? (string)$attributes['minOccurs'] : 1;
            $options['id']        = (string)$attributes['id'];
        } else {
            $options['minOccurs'] = array_key_exists('minOccurs', $objectOrArray) ? self::getValue($objectOrArray, 'minOccurs') : 1;
            $options['id']        = self::getValue($objectOrArray, 'id');
        }

        return $options;
    }

    public static function getAttributeOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();

            $options['name'] = (string)$attributes['name'];
            $options['type'] = (string)$attributes['type'];

            if (empty($options['type'])) {
                $options['type'] = (string)$attributes['ref'];
            }

            if (strpos($options['type'], ':') !== false) {
                // remove the namespace prefix
                $parts = explode(':', $options['type']);
                $options['type'] = $parts[1];
            }

            $options['use'] = ((string)$attributes['use'] != '') ? (string)$attributes['use'] : 'optional';
            $options['form'] = (string)$attributes['form'];
        } else {
            $options['name'] = self::getValue($objectOrArray, 'name');
            $options['type'] = self::getValue($objectOrArray, 'type');
            $options['use'] = array_key_exists('use', $objectOrArray) ? self::getValue($objectOrArray, 'use') : 'optional';
            $options['form'] = self::getValue($objectOrArray, 'form');
        }

        return $options;
    }

    public static function getRestrictionOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();
            
            $options['base'] = (string)$attributes['base'];

            if (strpos($options['base'], ':') !== false) {
                // remove the namespace prefix
                $parts = explode(':', $options['base']);
                $options['base'] = $parts[1];
            }
        } else {
            $options['base'] = self::getValue($objectOrArray, 'base');
        }

        return $options;
    }

    public static function getEnumerationOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();
            
            $options['value'] = (string)$attributes['value'];
        } else {
            $options['value'] = self::getValue($objectOrArray, 'value');
        }

        return $options;
    }

    public static function getComplexContentOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();

            $options['mixed'] = (string)$attributes['mixed'];
            $options['id']    = (string)$attributes['id'];
        } else {
            $options['mixed'] = self::getValue($objectOrArray, 'mixed');
            $options['id']    = self::getValue($objectOrArray, 'id');
        }

        return $options;
    }

    public static function getExtensionOptions($objectOrArray) {
        $options = array();

        if ($objectOrArray instanceof SimpleXMLElement) {
            $attributes = $objectOrArray->attributes();

            $options['base'] = (string)$attributes['base'];

            if (strpos($options['base'], ':') !== false) {
                // remove the namespace prefix
                $parts = explode(':', $options['base']);
                $options['base'] = $parts[1];
            }

            $options['id']    = (string)$attributes['id'];
        } else {
            $options['base'] = self::getValue($objectOrArray, 'base');
            $options['id']    = self::getValue($objectOrArray, 'id');
        }

        return $options;
    }
}
