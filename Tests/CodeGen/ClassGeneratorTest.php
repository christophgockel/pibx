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
require_once 'PiBX/AST/Type.php';
require_once 'PiBX/CodeGen/ClassGenerator.php';

class PiBX_CodeGen_ClassGeneratorTest extends PHPUnit_Framework_TestCase {
    public function testIndentationWithSpaces() {
        $dummyAST = new PiBX_AST_Type('generatedWithSpaces', 'string');
        $dummyAST->setAsRoot();

        $generator = new PiBX_CodeGen_ClassGenerator('    ');
        $dummyAST->accept($generator);

        $classes = $generator->getClasses();

        $expectedClassCode = "class GeneratedWithSpaces {\n"
                           . "    private \$generatedWithSpaces;\n"
                           . "\n"
                           . "    public function setGeneratedWithSpaces(\$generatedWithSpaces) {\n"
                           . "        \$this->generatedWithSpaces = \$generatedWithSpaces;\n"
                           . "    }\n"
                           . "    public function getGeneratedWithSpaces() {\n"
                           . "        return \$this->generatedWithSpaces;\n"
                           . "    }\n"
                           . "}";
        $this->assertEquals($expectedClassCode, $classes['GeneratedWithSpaces']);
    }
    
    public function testIndentationWithTabs() {
        $dummyAST = new PiBX_AST_Type('generatedWithTabs', 'string');
        $dummyAST->setAsRoot();

        $generator = new PiBX_CodeGen_ClassGenerator("\t");
        $dummyAST->accept($generator);

        $classes = $generator->getClasses();

        $expectedClassCode = "class GeneratedWithTabs {\n"
                           . "\tprivate \$generatedWithTabs;\n"
                           . "\n"
                           . "\tpublic function setGeneratedWithTabs(\$generatedWithTabs) {\n"
                           . "\t\t\$this->generatedWithTabs = \$generatedWithTabs;\n"
                           . "\t}\n"
                           . "\tpublic function getGeneratedWithTabs() {\n"
                           . "\t\treturn \$this->generatedWithTabs;\n"
                           . "\t}\n"
                           . "}";
        $this->assertEquals($expectedClassCode, $classes['GeneratedWithTabs']);
    }

    public function testNoIndentation() {
        $dummyAST = new PiBX_AST_Type('generatedWithoutIndentation', 'string');
        $dummyAST->setAsRoot();

        $generator = new PiBX_CodeGen_ClassGenerator('');
        $dummyAST->accept($generator);

        $classes = $generator->getClasses();

        $expectedClassCode = "class GeneratedWithoutIndentation {\n"
                           . "private \$generatedWithoutIndentation;\n"
                           . "\n"
                           . "public function setGeneratedWithoutIndentation(\$generatedWithoutIndentation) {\n"
                           . "\$this->generatedWithoutIndentation = \$generatedWithoutIndentation;\n"
                           . "}\n"
                           . "public function getGeneratedWithoutIndentation() {\n"
                           . "return \$this->generatedWithoutIndentation;\n"
                           . "}\n"
                           . "}";
        $this->assertEquals($expectedClassCode, $classes['GeneratedWithoutIndentation']);
    }

    public function testGlobalComplexTypeChoice() {
        $type = new PiBX_AST_Type('Fruit');
            $type_structure = new PiBX_AST_Structure();
            $type_structure->setStructureType(PiBX_AST_StructureType::CHOICE());
            $type_structure->add(new PiBX_AST_StructureElement('apple', 'string'));
            $type_structure->add(new PiBX_AST_StructureElement('orange', 'string'));
        $type->add($type_structure);

        $generator = new PiBX_CodeGen_ClassGenerator('  ');
        $type->accept($generator);

        $classes = $generator->getClasses();

        $expectedClassCode  = "class Fruit {\n"
                            . "  private \$choiceSelect = -1;\n"
                            . "  private \$APPLE_CHOICE = 0;\n"
                            . "  private \$ORANGE_CHOICE = 1;\n"
                            . "  private \$apple;\n"
                            . "  private \$orange;\n"
                            . "\n"
                            . "  public function clearChoiceSelect() {\n"
                            . "    \$this->choiceSelect = -1;\n"
                            . "  }\n"
                            . "  public function ifApple() {\n"
                            . "    return \$this->choiceSelect == \$this->APPLE_CHOICE;\n"
                            . "  }\n"
                            . "  public function setApple(\$apple) {\n"
                            . "    \$this->setChoiceSelect(\$this->APPLE_CHOICE);\n"
                            . "    \$this->apple = \$apple;\n"
                            . "  }\n"
                            . "  public function getApple() {\n"
                            . "    return \$this->apple;\n"
                            . "  }\n"
                            . "  public function ifOrange() {\n"
                            . "    return \$this->choiceSelect == \$this->ORANGE_CHOICE;\n"
                            . "  }\n"
                            . "  public function setOrange(\$orange) {\n"
                            . "    \$this->setChoiceSelect(\$this->ORANGE_CHOICE);\n"
                            . "    \$this->orange = \$orange;\n"
                            . "  }\n"
                            . "  public function getOrange() {\n"
                            . "    return \$this->orange;\n"
                            . "  }\n"
                            . "  private function setChoiceSelect(\$choice) {\n"
                            . "    if (\$this->choiceSelect == -1) {\n"
                            . "      \$this->choiceSelect = \$choice;\n"
                            . "    } elseif (\$this->choiceSelect != \$choice) {\n"
                            . "      throw new RuntimeException('Need to call clearChoiceSelect() before changing existing choice');\n"
                            . "    }\n"
                            . "  }\n"
                            ."}";
        $this->assertEquals($expectedClassCode, $classes['Fruit']);
    }
}
