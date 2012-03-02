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
require_once 'PiBX/AST/Collection.php';
require_once 'PiBX/AST/CollectionItem.php';
require_once 'PiBX/AST/Enumeration.php';
require_once 'PiBX/AST/EnumerationValue.php';
require_once 'PiBX/AST/Structure.php';
require_once 'PiBX/AST/StructureElement.php';
require_once 'PiBX/AST/StructureType.php';
require_once 'PiBX/AST/Type.php';
require_once 'PiBX/AST/TypeAttribute.php';
require_once 'PiBX/AST/Visitor/VisitorAbstract.php';
require_once 'PiBX/Binding/Names.php';
require_once 'PiBX/CodeGen/TypeCheckGenerator.php';
require_once 'PiBX/Util/XsdType.php';
/**
 * Generating the PHP-code of the classes is done here, with a Hierarchical Visitor
 * of the AST.
 * 
 * After visiting, the code can be retrieved with <code>getClasses()</code>.
 * The actual file writing has to be done separately.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ClassGenerator implements PiBX_AST_Visitor_VisitorAbstract {
    /**
     * @var PiBX_CodeGen_TypeCheckGenerator
     */
    private $typeChecks;
    /**
     * @var boolean Flag if type-check code should be included in the generated methods.
     */
    private $doTypeChecks;

    /**
     * List of class attributes. Separated by class-name => public/protected/private.
     * @var array
     */
    private $attributes;
    private $methods;
    private $TYPEHINT_ARRAY = '#array#';
    private $localClasses;
    private $indentationString;
    
    public function  __construct($indentationString = "\t") {
        $this->typeChecks = new PiBX_CodeGen_TypeCheckGenerator();
        $this->attributes = array();
        $this->methods = array();
        $this->localClasses = array();
        $this->indentationString = $indentationString;
    }

    /**
     * @return string[] hash with class-name => class-code
     */
    public function getClasses() {
        return $this->buildClasses();
        return $this->classes;
    }

    private function buildClasses() {
        $classNames = array_keys($this->attributes);
        $classes = array();
        
        foreach ($classNames as $className) {
            $code = 'class ' . $className . ' {';
            $code .= $this->buildPrivateAttributesForClass($className);
            $code .= $this->buildPublicMethodsForClass($className);
            $code .= '}';
            
            $code = $this->prettyPrint($code);
            
            $classes[$className] = $code;
        }

        return $classes;
    }

    private function buildPrivateAttributesForClass($className) {
        $code = '';

        foreach ($this->attributes[$className]['private'] as $attribute) {
            list($attributeName, $attributeValue) = $attribute;
            $code .= 'private $' . $attributeName;
            if ($attributeValue !== '') {
                $code .= ' = ' . $attributeValue;
            }
            $code .= ';';
        }

        return $code;
    }

    private function buildPublicMethodsForClass($className) {
        $code = '';

        foreach ($this->methods[$className] as $scope => $methods) {
            foreach ($methods as $method) {
                $code .= $scope . ' function ';
                $code .= $method['name'];
                $code .= '(';
                    $code .= $this->getParameterListString($method);
                $code .= ')';
                $code .= ' {';
                    $code .= $method['body'];
                $code .= '}';
            }
        }

        return $code;
    }

    private function getParameterListString(array $method) {
        $parameterString = '';
        
        foreach ($method['parameter'] as $parameter) {
            $parameterName = key($parameter);
            $parameterType = $parameter[$parameterName];

            if ( !PiBX_Util_XsdType::isBaseType($parameterType) ) { // enable type hinting
                if ($parameterType == $this->TYPEHINT_ARRAY) {
                    $parameterString .= 'array ';
                } else {
                    $parameterString .= PiBX_Binding_Names::createClassnameFor($parameterType) . ' ';
                }
            }
            $parameterString .= '$' . key($parameter);
        }

        return $parameterString;
    }

    private function prettyPrint($code) {
        $indentation = 0;
        $prettyCode = '';
        $length = strlen($code);
        $currentChar = $previousChar = '';
        $quoted = false;

        for ($i = 0; $i < $length; $i++) {
            $currentChar = mb_substr($code, $i, 1);
            
            if ($currentChar == "'" || $currentChar == '"') {
                $quoted = !$quoted;
            } elseif ($currentChar == '}') {
                $indentation--;
            } elseif ($previousChar == '{') {
                $indentation++;
            }
            
            if ($indentation < 0) {
                $indentation = 0;
            }
            
            if (!$quoted && ($previousChar == '{' || $previousChar == ';' || ($previousChar == '}' && $currentChar != ' '))) {
                $prettyCode .= "\n";
                $prettyCode .= str_repeat($this->indentationString, $indentation);
            }
            
            $prettyCode .= $currentChar;
            $previousChar = $currentChar;
        }
        
        // for cosmetic reasons, add a separating newline between class-attributes and its methods
        $prettyCode = preg_replace('/^(\s*(?:private|protected|public) function)/im', "\n$1", $prettyCode, 1);
        
        return $prettyCode;
    }

    /**
     * Enables type-checks in generated set-methods.
     * This enforces valid formats/values as parameters.
     */
    public function enableTypeChecks() {
        $this->doTypeChecks = true;
    }

    /**
     * disables type-checks in generated set-methods.
     * The generated set-methods are plain setters.
     */
    public function disableTypeChecks() {
        $this->doTypeChecks = false;
    }

    public function visitCollectionEnter(PiBX_AST_Collection $collection) {
        // Collection informations are used when CollectionItem-nodes are traversed
        return true;
    }
    public function visitCollectionLeave(PiBX_AST_Collection $collection) {
        return true;
    }

    public function visitCollectionItem(PiBX_AST_CollectionItem $collectionItem) {        
        $name = PiBX_Binding_Names::getListAttributeName($collectionItem->getName());

        $this->addPrivateMember($name);
        $this->addSetterFor($collectionItem);//TODO: add parameter $name, to pass pre-defined name?
        $this->addGetterFor($collectionItem);
        
        return;
    }
    
    public function visitEnumerationEnter(PiBX_AST_Enumeration $enumeration) {
        $name = $enumeration->getName();
        $parent = $enumeration->getParent();

        $this->addPrivateMember($name);

        $this->addSetterFor($enumeration);
        $this->addGetterFor($enumeration);
        
        return true;
    }

    public function visitEnumerationLeave(PiBX_AST_Enumeration $enumeration) {
        if ($enumeration->getParent() == null) {
            return false;
        }
        
        return true;
    }
    
    public function visitEnumerationValue(PiBX_AST_EnumerationValue $enumerationValue) {
    }
    
    public function visitStructureEnter(PiBX_AST_Structure $structure) {
        $name = $structure->getName();
        $structureType = $structure->getStructureType();

        if ($structureType === PiBX_AST_StructureType::CHOICE()) {
            if ($name != '') {
                $selectionAttribute = $name . 'Select';
            } else {
                $selectionAttribute = 'choiceSelect';
            }
            $this->addPrivateMember($selectionAttribute, '-1');
            $childrenCount = $structure->countChildren();
            for ($i = 0; $i < $childrenCount; $i++) {
                $child = $structure->get($i);

                $childName = $child->getName();
                $attributeName = '';
                if ($name != '') {
                    $attributeName = $name . '_';
                }
                $attributeName = $attributeName . $childName . '_CHOICE';
                $attributeName = strtoupper($attributeName);

                $this->addPrivateMember($attributeName, $i);
            }

            $methodName = ucfirst($selectionAttribute);
            $parameter = array(array('choice' => 'string'));
            $body = 'if ($this->' . $selectionAttribute . ' == -1) {';
                $body .= '$this->' . $selectionAttribute . ' = $choice;';
            $body .= '} elseif ($this->' . $selectionAttribute . ' != $choice) {';
                $body .= 'throw new RuntimeException(\'Need to call clear' . $methodName . '() before changing existing choice\');';
            $body .= '}';
            $this->addPrivateMethod('set'.$methodName, $parameter, $body);
            $body = '$this->' . $selectionAttribute . ' = -1;';
            $this->addPublicMethod('clear'.$methodName, array(), $body);
        }
        
        return true;
    }
    public function visitStructureLeave(PiBX_AST_Structure $structure) {
        return true;
    }
    
    public function visitStructureElementEnter(PiBX_AST_StructureElement $structureElement) {
        $name = $structureElement->getName();
        $parent = $structureElement->getParent();
        $structureType = $parent->getStructureType();

        if ($structureType === PiBX_AST_StructureType::CHOICE()) {
            $parentName = $parent->getName();
            
            if ($parentName == '') {
                $attributeName  = $name;
                $choiceConstant = $name . '_CHOICE';
                $choiceMember   = 'choiceSelect';
            } else {
                $attributeName  = $parentName . ucfirst($name);
                $choiceConstant = $parentName . '_' . $name . '_CHOICE';
                $choiceMember   = $parentName . 'Select';
            }
            
            $this->addPrivateMember($attributeName);

            $choiceConstant = strtoupper($choiceConstant);
            $methodName = 'if' . ucfirst($attributeName);
            $parameter = array();            
            $body = 'return $this->'.$choiceMember . ' == $this->' . $choiceConstant . ';';
            $this->addPublicMethod($methodName, $parameter, $body);
            
            $methodName = 'set' . ucfirst($attributeName);
            $parameter = array(array($attributeName => $structureElement->getType()));
            $body = '$this->set' . ucfirst($choiceMember) . '($this->' . $choiceConstant . ');';
            if ($this->doTypeChecks) {
                $typeCheckCode = $this->typeChecks->getTypeCheckFor($structureElement->getType(), $attributeName);
                $body .= $typeCheckCode;
            }
            $body .= '$this->' . $attributeName . ' = $' . $attributeName . ';';
            $this->addPublicMethod($methodName, $parameter, $body);
            
            $methodName = 'get' . ucfirst($attributeName);
            $parameter = array();
            $body = 'return $this->' . $attributeName . ';';
            $this->addPublicMethod($methodName, $parameter, $body);
        }

        return true;
    }
    public function visitStructureElementLeave(PiBX_AST_StructureElement $structureElement) {
        return true;
    }
    
    public function visitTypeEnter(PiBX_AST_Type $type) {
        $name = $type->getName();
        $typesType = $type->getType();

        $this->currentClassName = PiBX_Binding_Names::createClassnameFor($type);
        $this->addClass($this->currentClassName);

        if ( !$type->hasChildren() ) {
            
            if (!PiBX_Util_XsdType::isBaseType($typesType)) {
                // complexTypes (i.e. classes) have to be type-hinted
                // in the method signature.
                $expectedType = PiBX_Binding_Names::createClassnameFor($type);
            }

            $this->addPrivateMember($name);
            $this->addSetterFor($type);
            $this->addGetterFor($type);
        }
        
        return true;
    }

    private function addClass($name) {
        $initialScopeElements = array(
            'public' => array(),
            'protected' => array(),
            'private' => array()
        );
        
        $this->attributes[$name] = $initialScopeElements;
        $this->methods[$name] = $initialScopeElements;
        $this->localClasses[$name] = $initialScopeElements;
    }

    private function addPrivateMember($name, $initialValue = '') {
        $this->attributes[$this->currentClassName]['private'][] = array($name, $initialValue);
    }

    private function addPublicMethod($name, array $parameter, $body) {
        $this->methods[$this->currentClassName]['public'][] = array(
            'name' => $name,
            'parameter' => $parameter,
            'body' => $body
        );
    }

    private function addPrivateMethod($name, array $parameter, $body) {
        $this->methods[$this->currentClassName]['private'][] = array(
            'name' => $name,
            'parameter' => $parameter,
            'body' => $body
        );
    }

    public function visitTypeLeave(PiBX_AST_Type $type) {
        return true;
    }

    public function visitTypeAttributeEnter(PiBX_AST_TypeAttribute $typeAttribute) {
        if ($typeAttribute->countChildren() == 0) {
            $name = $typeAttribute->getName();
            $type = $typeAttribute->getType();
            // base type attribute
            $attributeName = PiBX_Binding_Names::getAttributeName($name);
            
            $this->addPrivateMember($attributeName);
            $this->addSetterFor($typeAttribute);
            $this->addGetterFor($typeAttribute);

            return false;
        } else {
            return true;
        }
    }

    protected function addSetterFor(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $type = $tree->getType();
        $attributeName = PiBX_Binding_Names::getAttributeName($name);
        $methodName    = PiBX_Binding_Names::getCamelCasedName($name);
        $typeCheckCode = '';
        
        if ($tree instanceof PiBX_AST_CollectionItem) {
            if (!PiBX_Binding_Names::nameAlreadyEndsWithWordList($methodName)) {
                $methodName = $this->buildPlural($methodName);
                $attributeName = PiBX_Binding_Names::getListAttributeName($name);
            }
            $type = $this->TYPEHINT_ARRAY;
            if ($this->doTypeChecks) {
                $typeCheckCode = $this->typeChecks->getListTypeCheckFor($tree, $attributeName);
            }
        } elseif ($tree instanceof PiBX_AST_Enumeration) {
            $firstEnumerationValue = $tree->get(0);
            // the type is stored in the actual EnumerationValue nodes, not in the Enumeration itself
            $type = $firstEnumerationValue->getType();
            if ($this->doTypeChecks) {
                $typeCheckCode = $this->typeChecks->getEnumerationTypeCheckFor($tree, $attributeName);
            }
        } else {
            if ($this->doTypeChecks) {
                $typeCheckCode = $this->typeChecks->getTypeCheckFor($type, $attributeName);
            }
        }
        
        $parameter = array(array($attributeName => $type));
        $body = $typeCheckCode . '$this->' . $attributeName . ' = $' . $attributeName . ';';
        
        $this->addPublicMethod('set' . $methodName, $parameter, $body);
    }

    protected function addGetterFor(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $methodName    = PiBX_Binding_Names::getCamelCasedName($name);

        if ($tree instanceof PiBX_AST_CollectionItem && !PiBX_Binding_Names::nameAlreadyEndsWithWordList($name)) {
            $methodName = $this->buildPlural($methodName);
            $attributeName = PiBX_Binding_Names::getListAttributeName($name);
        }
        else {
            $attributeName = PiBX_Binding_Names::getAttributeName($name);
        }

        $body = 'return $this->' . $attributeName . ';';
        
        $this->addPublicMethod('get' . $methodName, array(), $body);
    }
    
    public function visitTypeAttributeLeave(PiBX_AST_TypeAttribute $typeAttribute) {
        return true;
    }

    private function buildPlural($name) {
        $lastCharacter = substr($name, -1);
        if (strtolower($lastCharacter) == 's') {
            return $name;
        }

        return $name . 's';
    }
}
