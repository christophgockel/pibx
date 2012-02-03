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
 * So, the actual file writing has to be done separately.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ClassGenerator implements PiBX_AST_Visitor_VisitorAbstract {
    private $xml;

    /**
     * @var string[] hash with generated class-code
     */
    private $classes;
    private $currentClass;
    private $currentClassName;
    private $currentClassAttributes;
    private $currentClassMethods;
    /**
     * @var string Used for additional class-code. Content will be added after the closing "}".
     */
    private $classAppendix;
    /**
     * @var PiBX_CodeGen_TypeCheckGenerator
     */
    private $typeChecks;
    /**
     * @var boolean Flag if type-check code should be included in the generated methods.
     */
    private $doTypeChecks;
    
    public function  __construct() {
        $this->classes = array();

        $this->currentClassName = '';
        $this->currentClass = '';
        $this->currentClassAttributes = '';
        $this->currentClassMethods = '';
        $this->classAppendix = '';

        $this->typeChecks = new PiBX_CodeGen_TypeCheckGenerator();
    }

    /**
     * 
     * @return string[] hash with class-name => class-code
     */
    public function getClasses() {        
        return $this->classes;
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

    public function visitCollectionEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        
        $this->currentClassAttributes .= "\tprivate \$" . $name . ";\n";
        
        $setter = PiBX_Binding_Names::createSetterNameFor($tree);
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);
        
        $this->currentClassMethods .= "\tpublic function " . $setter . "(array \$" . $name . ") {\n";
        if ($this->doTypeChecks) {
            $this->currentClassMethods .= $this->typeChecks->getListTypeCheckFor($tree, $name);
        }
        $this->currentClassMethods .= "\t\t\$this->" . $name . " = \$" . $name . ";\n"
                                    . "\t}\n"
                                    . "\tpublic function " . $getter . "() {\n"
                                    . "\t\treturn \$this->" . $name . ";\n"
                                    . "\t}\n";
        
        return true;
    }
    public function visitCollectionLeave(PiBX_AST_Tree $tree) {
        return true;
    }

    public function visitCollectionItem(PiBX_AST_Tree $tree) {
        if ($tree->getParent() instanceof PiBX_AST_Collection) {
            // the collection-node already did add everything necessary for the collection and its items
            return false;
        }
        
        $name = PiBX_Binding_Names::getAttributeName($tree->getName()) . 'list';

        $this->currentClassAttributes .= "\tprivate \$" . $name . ";\n";

        $setter = PiBX_Binding_Names::createSetterNameFor($tree);
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);

        $this->currentClassMethods .= "\tpublic function " . $setter . "(array \$" . $name . ") {\n";
        if ($this->doTypeChecks) {
            $this->currentClassMethods .= $this->typeChecks->getListTypeCheckFor($tree, $name);
        }
        $this->currentClassMethods .= "\t\t\$this->" . $name . " = \$" . $name . ";\n"
                                    . "\t}\n"
                                    . "\tpublic function " . $getter . "() {\n"
                                    . "\t\treturn \$this->" . $name . ";\n"
                                    . "\t}\n";
        return true;
    }
    
    public function visitEnumerationEnter(PiBX_AST_Tree $tree) {
        $enumName = $tree->getName();

        if ($tree->getParent() == null) {
            // at the moment separate enums are not supported, yet.
            //$this->classAppendix .= 'class b_' . $this->currentClassName . '_' . ucfirst($enumName) . " {\n";
            return false;
        }

        $attributeName = PiBX_Binding_Names::getAttributeName($tree->getName());
        $methodName = PiBX_Binding_Names::getCamelCasedName($tree->getName());

        $this->currentClassAttributes .= "\tprivate \$".$attributeName.";\n";
        $methods = "\tpublic function set".$methodName."(\$".$attributeName.") {\n";
        if ($this->doTypeChecks) {
            // to do a type check for enums
            // all possible values have to be fetched
            $valueCount = $tree->countChildren();
            $conditionValues = array();
            
            for ($i = 0; $i < $valueCount; $i++) {
                $valueAst = $tree->get($i);
                $conditionValues[] = $valueAst->getName();
            }
            
            $ifConditions = "(\$" . $attributeName . " != '".implode("') || (\$".$attributeName." != '", $conditionValues) . "')";
            $listOfValues = '"' . implode('", "', $conditionValues) . '"';
            
            $methods .= "\t\tif (";
            $methods .= $ifConditions . ") {\n"
                      . "\t\t\tthrow new InvalidArgumentException('Unexpected value \"' . \$" . $attributeName . " . '\". Expected is one of the following: " . $listOfValues . ".');\n"
                      . "\t\t}\n";
        }
        $methods .= "\t\t\$this->".  $attributeName . " = \$".$attributeName.";\n"
                  . "\t}\n"
                  . "\tpublic function get".$methodName."() {\n"
                  . "\t\treturn \$this->" . $attributeName . ";\n"
                  . "\t}\n";

        $this->currentClassMethods .= $methods;

        return true;
    }
    public function visitEnumerationLeave(PiBX_AST_Tree $tree) {
        if ($tree->getParent() == null) {
            //$this->classAppendix .= "}";
            return false;
        }
        return true;
    }
    public function visitEnumeration(PiBX_AST_Tree $tree) {
    }

    public function visitEnumerationValue(PiBX_AST_Tree $tree) {
    }
    
    public function visitStructureEnter(PiBX_AST_Tree $tree) {
        $structureType = $tree->getStructureType();
        
        if ($structureType === PiBX_AST_StructureType::CHOICE()) {
            $name = $tree->getName();
            $attributeName = $name . 'Select';
            $this->currentClassAttributes .= "\tprivate \$" . $attributeName . " = -1;\n";
            
            $constantNames = PiBX_Binding_Names::createChoiceConstantsFor($tree);
            $i = 0;
            foreach ($constantNames as $constant) {
                $this->currentClassAttributes .= "\tprivate \${$constant} = $i;\n";
                ++$i;
            }

            $methodName = ucfirst($attributeName);
            $methods = "\tprivate function set{$methodName}(\$choice) {\n"
                     . "\t\tif (\$this->{$attributeName} == -1) {\n"
                     . "\t\t\t\$this->{$attributeName} = \$choice;\n"
                     . "\t\t} elseif (\$this->{$attributeName} != \$choice) {\n"
                     . "\t\t\tthrow new RuntimeException('Need to call clear{$methodName}() before changing existing choice');\n"
                     . "\t\t}\n"
                     . "\t}\n";

            $methods .= "\tpublic function clear{$methodName}() {\n"
                      . "\t\t\$this->{$attributeName} = -1;\n"
                      . "\t}\n";

            $this->currentClassMethods .= $methods;
        }
        
        return true;
    }
    public function visitStructureLeave(PiBX_AST_Tree $tree) {
        if ($tree->getStructureType() == PiBX_AST_StructureType::CHOICE()) {
            $this->xml .= '</structure>';
        }
        $this->xml .= "</structure>";
        return true;
    }
    
    public function visitStructureElementEnter(PiBX_AST_Tree $tree) {
        $name = $tree->getName();
        $parentName = $tree->getParent()->getName();

        $attributeName = $parentName . $name;
        
        $selectName = ucfirst($parentName) . 'Select';
        
        $this->currentClassAttributes .= "\tprivate \$" . $attributeName . ";\n";
        
        $choiceConstant = $parentName . '_' . $name . '_CHOICE';
        $choiceConstant = strtoupper($choiceConstant);

        $setter = PiBX_Binding_Names::createSetterNameFor($tree);
        $getter = PiBX_Binding_Names::createGetterNameFor($tree);

        $methodName = ucfirst($attributeName);

        $methods = "\tpublic function if{$methodName}() {\n"
                . "\t\treturn \$this->{$parentName}Select == \$this->$choiceConstant;\n"
                . "\t}\n";
        $methods .= "\tpublic function {$setter}(\${$attributeName}) {\n"
                  . "\t\t\$this->set{$selectName}(\$this->{$choiceConstant});\n";
        
        if ($this->doTypeChecks) {
            $methods .= $this->typeChecks->getTypeCheckFor($tree->getType(), $attributeName);
        }
        
        $methods .= "\t\t\$this->{$attributeName} = \${$attributeName};\n"
                  . "\t}\n";
        $methods .= "\tpublic function {$getter}() {\n"
                  . "\t\treturn \$this->{$attributeName};\n"
                  . "\t}\n";

        $this->currentClassMethods .= $methods;
        
        return true;
    }
    public function visitStructureElementLeave(PiBX_AST_Tree $tree) {
        return true;
    }
    
    public function visitTypeEnter(PiBX_AST_Tree $tree) {
        $this->currentClassName = PiBX_Binding_Names::createClassnameFor($tree);
        $this->currentClass = 'class ' . $this->currentClassName . " {\n";
        
        return true;
    }
    public function visitTypeLeave(PiBX_AST_Tree $tree) {
        $this->currentClass .= $this->currentClassAttributes;
        $this->currentClass .= "\n";
        $this->currentClass .= $this->currentClassMethods;
        $this->currentClass .= '}';
        if (trim($this->classAppendix) != '') {
            $this->currentClass .= "\n";
            $this->currentClass .= $this->classAppendix;
        }
        $this->classes[$this->currentClassName] = $this->currentClass;

        $this->currentClassAttributes = '';
        $this->currentClassMethods = '';
        $this->currentClass = '';
        $this->classAppendix = '';
        return true;
    }

    public function visitTypeAttributeEnter(PiBX_AST_Tree $tree) {
        if ($tree->countChildren() == 0) {
            // base type attribute
            $attributeName = PiBX_Binding_Names::getAttributeName($tree->getName());
            $methodName = PiBX_Binding_Names::getCamelCasedName($tree->getName());
            
            $this->currentClassAttributes .= "\tprivate \$".$attributeName.";\n";
            $type = $tree->getType();

            $methods = "\tpublic function set".$methodName."(";
            if (!PiBX_Util_XsdType::isBaseType($type)) {
                // complexTypes (i.e. classes) have to be type-hinted
                // in the method signature.
                $expectedType = PiBX_Binding_Names::createClassnameFor($type);
                $methods .= $expectedType . ' ';
            }
            $methods .= "\$".$attributeName.") {\n";
            
            if ($this->doTypeChecks) {
                $methods .= $this->typeChecks->getTypeCheckFor($tree->getType(), $attributeName);
            }
            
            $methods .= "\t\t\$this->". $attributeName . " = \$".$attributeName.";\n"
                     . "\t}\n"
                     . "\tpublic function get".$methodName."() {\n"
                     . "\t\treturn \$this->". $attributeName . ";\n"
                     . "\t}\n";

            $this->currentClassMethods .= $methods;

            return false;
        } else {
            return true;
        }
    }
    public function visitTypeAttributeLeave(PiBX_AST_Tree $tree) {
        return true;
    }
}
