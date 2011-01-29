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
require_once 'PiBX/CodeGen/TypeUsage.php';
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
require_once 'PiBX/ParseTree/Visitor/VisitorAbstract.php';
/**
 * An ASTCreator is a Visitor of a ParseTree.
 * It traverses a ParseTree-structure to produce an abstract syntax tree.
 *
 * @author Christoph Gockel
 */
class PiBX_CodeGen_ASTCreator implements PiBX_ParseTree_Visitor_VisitorAbstract {
    /**
     * @var AST[] A stack of AST-objects of the current parsed type
     */
    private $stack = array();
    /**
     * @var AST[] A list of completed (already plowed) types of the ParseTree.
     */
    public  $typeList;
    private $lastLevel;
    private $typeUsage;

    public function  __construct(PiBX_CodeGen_TypeUsage $typeUsage) {
        $this->typeList = array();
        $this->lastLevel = 0;
        $this->typeUsage = $typeUsage;
    }

    public function getTypeList() {
        return $this->typeList;
    }

    /**
     * What Type is on top of the stack?
     *
     * @return AST or null
     */
    private function currentType() {
        $top = count($this->stack) - 1;

        if ($top < 0) {
            return null;
        }

        $type = $this->stack[ $top ];

        return $type->getAST();
    }

    /**
     *
     * @return int Returns the amount of already defined types.
     */
    private function countTypes() {
        return count($this->typeList);
    }

    /**
     * Checks if the current stack-frame is "plowable". If it is,
     * the current frame will be plowed.
     * "Plowed" means "packing" the last types of equal parse-tree hierarchy.
     * So a collection gets it children, an Attribute gets its sub-type and
     * so on.
     *
     * All identified AST-types are parsed with a stack and "plowing" is
     * nothing more than reducing the current stack.
     *
     * @var int $level The current parse level
     */
    public function plowTypesForLevel($level) {
        if ($level < 0 || $this->lastLevel == 0) {
            // these are non-plowable levels
            return;
        }

        /**
         * compare "<=" since objects on the same level need to be plowed
         * as well.
         * Think of two TypeAttribute-objects after a Type-object on the stack.
         * These two TypeAttributes need to be plowed into the Type.
         */
        if ($level <= $this->lastLevel) {
            $sf = array_pop($this->stack);
            $typeToPlow = $sf->getAST();

            // plow stack from top to bottom
            $frame = count($this->stack) - 1;

            do {
                if ($frame < 0)
                    break;

                $sf = $this->stack[$frame];
                $type = $sf->getAST();

                $typeToPlow->setParent($type);

                $type->add($typeToPlow);

                $sf = array_pop($this->stack);
                $typeToPlow = $sf->getAST();

                $frame--;

            } while ($sf->getParseTreeLevel() >= $level);

            if ($level == 0) {
                /*
                 * Plowed a level 0 Type means a global-type is complete.
                 */
                array_push($this->typeList, $typeToPlow);
                return;
            }

            /**
             * If the current plow wasn't for level 0, push the current part
             * back onto the stack - the current type isn't finished yet.
             */
            if ($level == $this->lastLevel) {
                // plowing in equal parse levels
                $stackLevel = $level - 1;
            } else {
                $stackLevel = 0;
            }

            array_push($this->stack, new PiBX_CodeGen_ASTStackFrame($stackLevel, $typeToPlow));
        }
    }

    public function visitAttributeNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());
        $logMessage = $tree->getLevel() . str_pad("  ", $tree->getLevel()) . " attribute (" . $tree->getName() . ")";
        $this->log($logMessage);
        
        if ( !($this->currentType() instanceof PiBX_AST_Type) ) {
            throw new RuntimeException("Attributes can only be added to types.");
        }
        
        $name = $tree->getName();
        $type = $tree->getType();
        
        $attribute = new PiBX_AST_TypeAttribute($name, $type);
        $attribute->setStyle('attribute');
        
        $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $attribute);
        array_push($this->stack, $sf);

        $this->lastLevel = $tree->getLevel();
    }

    function visitElementNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());
        $logMessage = $tree->getLevel() . str_pad("  ", $tree->getLevel()) . " element";

        if ($tree->isAnonym()) {
            $logMessage .= " <anonym>";
        } else {
            $logMessage .= " (".$tree->getName().")";
        }

        if ($tree->getLevel() > 0) {

            if ($this->currentType() instanceof PiBX_AST_Type) {
                $ta = new PiBX_AST_TypeAttribute($tree->getName());
                $ta->setType($tree->getType());
                $schemaType = $tree->getType();
                $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $ta);
                array_push($this->stack, $sf);
            } elseif ($this->currentType() instanceof PiBX_AST_Collection) {
                $ci = new PiBX_AST_CollectionItem($tree->getName());
                $ci->setType($tree->getType());
                $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $ci);
                array_push($this->stack, $sf);
            } elseif ($this->currentType() instanceof PiBX_AST_Structure) {
                $se = new PiBX_AST_StructureElement($tree->getName(), $tree->getType());
                $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $se);
                array_push($this->stack, $sf);
            }

        } elseif ($tree->getLevel() == 0) {
            $logMessage .= " - global";
            $t = new PiBX_AST_Type($tree->getName());
            if ($this->countTypes() == 0) {
                // the first type is the XSD-root.
                $t->setAsRoot();
            }
            $sf = new PiBX_CodeGen_ASTStackFrame(-1, $t);
            array_push($this->stack, $sf);

            /*
             * Yes, add it two times.
             * It's a limitation that exist in the current implementation
             * of the TypeUsage-class.
             * The concrete class (which is the root element/type of a schema)
             * will not be referenced more than one time (at the definition).
             * But it must not be removed in an AST optimization afterwards,
             * so we just increase the usage-counter by one.
             */
            $this->typeUsage->addType($tree->getName());
            $this->typeUsage->addType($tree->getName());
        } else {
            throw new RuntimeException('invalid element state');
        }

        $this->lastLevel = $tree->getLevel();
        $this->log($logMessage);
    }

    function visitSimpleTypeNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());
        $logMessage = $tree->getLevel() . " ". str_pad("  ", $tree->getLevel()) . " ";
        $logMessage .= "simple";
        
        if ($tree->getLevel() == 0) {
            $logMessage .= " - global";
            $t = new PiBX_AST_Type($tree->getName());
            
            if ($this->countTypes() == 0) {
                // the first type is the XSD-root.
                $t->setAsRoot();
            }
            
            $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $t);
            array_push($this->stack, $sf);
            $this->typeUsage->addType($tree->getName());
        }

        $this->lastLevel = $tree->getLevel();
        $this->log($logMessage);
    }

    function visitComplexTypeNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());
        $logMessage = $tree->getLevel() . str_pad("  ", $tree->getLevel()) . " ";
        $logMessage .= "complex";

        if ($tree->getLevel() == 0) {
            $logMessage .= " - global";

            $t = new PiBX_AST_Type($tree->getName());

            if ($this->countTypes() == 0) {
                // the first type is the XSD-root.
                $t->setAsRoot();
            }

            $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $t);
            array_push($this->stack, $sf);

            $this->typeUsage->addType($tree->getName());
        }

        $this->log($logMessage);
    }

    function visitSequenceNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());
        $logMessage = $tree->getLevel() . " ". str_pad("  ", $tree->getLevel()) . " ";
        $logMessage .= "sequence (".$tree->getElementCount().")";
        $this->log($logMessage);

        if ($this->currentType() instanceof PiBX_AST_Type) {
            /**
             * A sequence witin a Type means a list of
             * attributes are incoming.
             */
            $this->currentType()->setAttributeCount($tree->getElementCount());
        } elseif ($this->currentType() instanceof PiBX_AST_TypeAttribute) {
            $newType = new PiBX_AST_Collection();
            $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $newType);
            array_push($this->stack, $sf);
        } elseif ($tree->getElementCount() < 0) {
            $c = new PiBX_AST_Collection();
            $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $c);
            array_push($this->stack, $sf);
        } else {
            throw new RuntimeException('invalid sequence state');
        }

        $this->lastLevel = $tree->getLevel();
    }

    function visitGroupNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());

        $this->lastLevel = $tree->getLevel();
    }

    function visitAllNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());

        $this->lastLevel = $tree->getLevel();
    }

    function visitChoiceNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());
        $this->log($tree->getLevel() . " ". str_pad("  ", $tree->getLevel()) . " choice");
        
        $s = new PiBX_AST_Structure();
        $s->setStructureType(PiBX_AST_StructureType::CHOICE());
        $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $s);
        array_push($this->stack, $sf);

        $this->lastLevel = $tree->getLevel();
    }

    function visitRestrictionNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());

        $this->lastLevel = $tree->getLevel();
    }

    function visitEnumerationNode(PiBX_ParseTree_Tree $tree) {
        $this->plowTypesForLevel($tree->getLevel());
        $this->log($tree->getLevel() . " ". str_pad("  ", $tree->getLevel()) . " enumeration (".$tree->getValue().")");

        if ( !($this->currentType() instanceof PiBX_AST_Enumeration) ) {
            /*
             * On the first enumeration-element a new AST-node for
             * enumerations has to be created.
             * All enumeration-nodes of the Parse-Tree are added as
             * attributes (EnumerationValue-objects) to the
             * AST-node "Enumeration".
             */
            $e = new PiBX_AST_Enumeration();
            $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel() - 1, $e);
            array_push($this->stack, $sf);
        }

        if ($this->currentType() instanceof PiBX_AST_Enumeration) {
            $enumType = $tree->getParent()->getBase();
            $enumType = $this->getCorrespondingType($enumType);
            $enum = new PiBX_AST_EnumerationValue($tree->getValue(), $enumType);
            $sf = new PiBX_CodeGen_ASTStackFrame($tree->getLevel(), $enum);
            array_push($this->stack, $sf);
        }

        $this->lastLevel = $tree->getLevel();
    }

    /**
     * XSD Types cannot be put 1:1 into the AST. The XSD definition has different
     * identifiers for the same semantical type, e.g. "NCName" is a string.
     * 
     * @param string $xsdType
     * @return string
     */
    private function getCorrespondingType($xsdType) {
        if ($xsdType == 'NCName') {
            //TODO maybe it will get necessary to do a pattern check afterwards
            //     in the ClassGenerator. But at the moment "string" is sufficient.
            return 'string';
        }

        return 'string';
    }

    private function log($message) {
        //TODO add log4php support
        //echo $message . "\n";
    }
}

/**
 * An ASTStackFrame is a structure for the internal ASTCreator usage.
 * It consists of a parse-tree level and the corresponding AST-object.
 * 
 * @private
 */
class PiBX_CodeGen_ASTStackFrame {
    /**
     * @var int The level of the ParseTree-element
     */
    private $parseTreeLevel;
    /**
     * @var AST The current built AST
     */
    private $ast;

    public function __construct($parseTreeLevel, PiBX_AST_Tree $ast) {
        $this->parseTreeLevel = $parseTreeLevel;
        $this->ast = $ast;
    }

    public function getParseTreeLevel() {
        return $this->parseTreeLevel;
    }

    public function getAST() {
        return $this->ast;
    }
}
