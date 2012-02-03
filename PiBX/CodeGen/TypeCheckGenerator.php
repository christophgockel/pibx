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
require_once 'PiBX/AST/Collection.php';
require_once 'PiBX/Binding/Names.php';
require_once 'PiBX/Util/XsdType.php';
/**
 * When generating class-code, it is possible to add extended type checks
 * into the methods.
 * This ensures only valid values can be passed to the methods.
 *
 * TypeCheckGenerator can be used for XSD base-types as well as generated classes.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_TypeCheckGenerator {
    public function __construct() {
        
    }
    
    /**
     * Returns the PHP code for a type check.
     * 
     * E.g. a XSD-date type has to be formatted like "yyyy-mm-dd". If a value
     * in a different format will be passed to a method, an InvalidArgumentException
     * will be thrown.
     * 
     * When no applicable check could be found, an empty string is returned.
     * 
     * @param string $type The type, the check should be generated for.
     * @param string $attributeName Name of the attribute-variable
     * @return string The PHP code used for the check.
     */
    public function getTypeCheckFor($type, $attributeName) {
        if ($type == 'date') {
            return $this->getDateCheck($attributeName);
        } elseif ($type == 'string') {
            return $this->getStringCheck($attributeName);
        } elseif ($type == 'int' || $type == 'integer') {
            return $this->getIntCheck($attributeName);
        } elseif ($type == 'long') {
            return $this->getLongCheck($attributeName);
        }
        
        return '';
    }

    /**
     * Returns the PHP code for a collection of arbitrary types.
     * 
     * @param PiBX_AST_Collection $ast
     * @return string 
     */
    public function getListTypeCheckFor(PiBX_AST_Tree $ast, $attributeName) {
        if (!($ast instanceof PiBX_AST_Collection) && !($ast instanceof PiBX_AST_CollectionItem)) {
            throw new RuntimeException('Not valid list AST given.');
        }

        $iterationVar = strtolower(substr($attributeName, 0, 1));
        $expectedType = $ast->getType();
        
        $code = "\t\tforeach (\$" . $attributeName . " as &\$" . $iterationVar . ") {\n";
        
        if (PiBX_Util_XsdType::isBaseType($expectedType) ) {
            $code .= "\t\t\tif (!is_" . $expectedType . "(\$" . $iterationVar . ")) {\n";
        } else {
            $expectedType = PiBX_Binding_Names::createClassnameFor($expectedType);
            $code .= "\t\t\tif (get_class(\$" . $iterationVar . ") !== '" . $expectedType . "') {\n";
        }
        
        $code .= "\t\t\t\tthrow new InvalidArgumentException('Invalid list. "
               . "All containing elements have to be of type \"" . $expectedType . "\".');\n"
               . "\t\t\t}\n"
               . "\t\t}\n";
        
        return $code;
    }

    /**
     * Returns the PHP code for a "date" check.
     * 
     * @param string $attributeName
     * @return string 
     */
    private function getDateCheck($attributeName) {
        $code = "\t\tif (!preg_match('/\d{4}-\d{2}-\d{2}/ism')) {\n"
              . "\t\t\tthrow new InvalidArgumentException('Unexpected date "
              . "format:' . \$" . $attributeName . " . '. Expected is: yyyy-mm-dd.');\n"
              . "\t\t}\n";
        
        return $code;
    }

    /**
     * Returns the PHP code for a "string" check.
     *
     * @param string $attributeName
     * @return string
     */
    private function getStringCheck($attributeName) {
        return $this->getSimpleTypeCheck('string', $attributeName);
    }

    /**
     * Returns the PHP code for a base type check.
     * Base types are the standard PHP ones like "string", "long", "int" and so on.
     *
     * @param string $type Type description (e.g. "string", "long", ...)
     * @param <type> $attributeName
     * @return string 
     */
    private function getSimpleTypeCheck($type, $attributeName) {
        $code = "\t\tif (!is_" . $type . "(\$" . $attributeName . ")) {\n"
              . "\t\t\tthrow new InvalidArgumentException('\"' . \$" . $attributeName . " . '\" is not a valid " . $type . ".');\n"
              . "\t\t}\n";
        
        return $code;
    }

    /**
     * Returns the PHP code for a "int" check.
     *
     * @param string $attributeName
     * @return string
     */
    private function getIntCheck($attributeName) {
        return $this->getSimpleTypeCheck('int', $attributeName);
    }

    /**
     * Returns the PHP code for a "long" check.
     *
     * @param string $attributeName
     * @return string
     */
    private function getLongCheck($attributeName) {
        return $this->getSimpleTypeCheck('long', $attributeName);
    }
}
