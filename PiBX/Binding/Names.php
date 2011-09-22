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
require_once 'PiBX/AST/Tree.php';
require_once 'PiBX/AST/Collection.php';
require_once 'PiBX/AST/CollectionItem.php';
require_once 'PiBX/AST/Enumeration.php';
require_once 'PiBX/AST/EnumerationValue.php';
require_once 'PiBX/AST/Structure.php';
require_once 'PiBX/AST/StructureElement.php';
require_once 'PiBX/AST/StructureType.php';
require_once 'PiBX/AST/Type.php';
require_once 'PiBX/AST/TypeAttribute.php';
/**
 * Names is a facility to create names for getter/setter methods or variable-names
 * for class attributes needed when generating code.
 * There are two instances (at least), that needs to get names out of the AST.
 * The Binding_Creator and the ClassGenerator. Both of them needs to retrieve
 * names for methods for specific AST-nodes.
 * Binding_Names encapsulates the logic for defining those names.
 * 
 * @author Christoph Gockel
 */
class PiBX_Binding_Names {
    public static function createGetterNameFor(PiBX_AST_Tree $tree) {
        if ($tree instanceof PiBX_AST_Type) {
                return 'get' . self::getCamelCasedName( $tree->getName() );
        } elseif ($tree instanceof PiBX_AST_Collection) {
            if ($tree->countChildren() == 1) {
                $child = $tree->get(0);
                $name = self::getCamelCasedName( $child->getName() );
                
                return 'get' . self::getCollectionName($name);
            }
        } elseif ($tree instanceof PiBX_AST_CollectionItem) {
            // a CollectionItem in a TypeAttribute is a list of the CollectionItems
            // without a parenting Collection-node
            if ($tree->getParent() instanceof PiBX_AST_TypeAttribute) {
                $name = self::getCamelCasedName( $tree->getName() );
                
                return 'get' . self::getCollectionName($name);
            }
        } elseif ($tree instanceof PiBX_AST_TypeAttribute) {
            $name = self::getCamelCasedName( $tree->getName() );
            return 'get' . $name;
        } elseif ($tree instanceof PiBX_AST_StructureElement) {
            $structureAst = $tree->getParent();
            $structureName = self::getCamelCasedName($structureAst->getName());
            $elementName = self::getCamelCasedName($tree->getName());

            return 'get' . $structureName . $elementName;
        } elseif ($tree instanceof PiBX_AST_Enumeration) {
            $name = self::getCamelCasedName($tree->getName());
            return 'get' . $name;
        }
        return '';
    }

    public static function createSetterNameFor(PiBX_AST_Tree $tree) {
        if ($tree instanceof PiBX_AST_Type) {
                return 'set' . self::getCamelCasedName( $tree->getName() );
        } elseif ($tree instanceof PiBX_AST_Collection) {
            if ($tree->countChildren() == 1) {
                $child = $tree->get(0);
                $name = self::getCamelCasedName( $child->getName() );
                // append a plural "s" for collection items if applicable
                if (strtolower(substr($name, -1)) != 's') {
                    $name .= 's';
                }
                return 'set' . $name;
            }
        } elseif ($tree instanceof PiBX_AST_CollectionItem) {
            // a CollectionItem in a TypeAttribute is a list of the CollectionItems
            // without a parenting Collection-node
            if ($tree->getParent() instanceof PiBX_AST_TypeAttribute) {
                $name = self::getCamelCasedName( $tree->getName() );

                return 'set' . self::getCollectionName($name);
            }
        } elseif ($tree instanceof PiBX_AST_TypeAttribute) {
            $name = self::getCamelCasedName( $tree->getName() );
            return 'set' . $name;
        } elseif ($tree instanceof PiBX_AST_StructureElement) {
            $structureAst = $tree->getParent();
            $structureName = self::getCamelCasedName($structureAst->getName());
            $elementName = self::getCamelCasedName($tree->getName());

            return 'set' . $structureName . $elementName;
        } elseif ($tree instanceof PiBX_AST_Enumeration) {
            $name = self::getCamelCasedName($tree->getName());
            return 'set' . $name;
        }
        return '';
    }

    public static function createTestFunctionFor(PiBX_AST_Tree $tree) {
        if ($tree instanceof PiBX_AST_StructureElement) {
            $structureAst = $tree->getParent();
            $structureName = self::getCamelCasedName($structureAst->getName());
            $elementName = self::getCamelCasedName($tree->getName());

            return 'if' . $structureName . $elementName;
        }
        return '';
    }

    /**
     * Returns a valid classname for a given string *or* instance of PiBX_AST_Tree.
     * 
     * @param mixed $treeOrString string or PiBX_AST_Tree
     * @return string
     */
    public static function createClassnameFor($treeOrString) {
        $name = '';
        
        if (is_string($treeOrString)) {
            $name = $treeOrString;
        } elseif (is_object($treeOrString) && ($treeOrString instanceof PiBX_AST_Tree)) {
            $name = $treeOrString->getName();
        } else {
            throw new RuntimeException("Cannot create classname for " . print_r($treeOrString, true));
        }
        
        return self::getCamelCasedName($name);
    }
    
    /**
     * XSD-Choice elements are handled via private variables/constants
     * 
     * @param PiBX_AST_Tree $tree
     * @return array The choice constants as strings
     */
    public static function createChoiceConstantsFor(PiBX_AST_Tree $tree) {
        $childCount = $tree->countChildren();
        $name = strtoupper( $tree->getName() );
        
        $names = array();
        
        for ($i = 0; $i < $childCount; ++$i) {
            $child = $tree->get($i);
            $childName = strtoupper($child->getName());
            $names[] = $name . '_' . $childName . '_CHOICE';
        }

        return $names;
    }

    /**
     * Returns a camel cased version of the given string.
     * 
     * @param string $name
     * @return string
     */
    public static function getCamelCasedName($name) {
        $name = str_replace('_', '-', $name);
        $parts = explode('-', $name);

        foreach ($parts as &$part) {
            $part = ucfirst($part);
        }
        
        return implode('', $parts);
    }

    /**
     * Strips off "-" and "_" characters in a name to create a valid attribute
     * name.
     * 
     * @param string $name
     * @return string
     */
    public static function getAttributeName($name) {
        $name = str_replace('-', '', $name);
        $name = str_replace('_', '', $name);

        return strtolower($name);
    }

    /**
     * Concats a plural "s" on a name if applicable, i.e. "item" gets "items" and so on.
     * 
     * @param string $name
     * @return string
     */
    private static function getCollectionName($name) {
        if (strtolower(substr($name, -1)) != 's') {
            $name .= 's';
        }
        
        return $name;
    }
}
