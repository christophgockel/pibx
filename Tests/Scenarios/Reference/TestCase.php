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
require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/ParseTree/Tree.php';
require_once 'PiBX/ParseTree/ElementNode.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
require_once 'PiBX/CodeGen/TypeUsage.php';
/**
 * This is an abstract base class for all W3C reference tests.
 * It ensures a single definition of how to test the different schemas.
 * Letting the particular sub-classes define what to test.
 *
 * @author Christoph Gockel
 */
abstract class PiBX_Scenarios_Reference_TestCase extends PHPUnit_Framework_TestCase {
    protected $pathToTestFiles;
    protected $schemaFile;

    abstract function getAST();
    abstract function getParseTree();

    public function testAST() {
        $expectedType = $this->getAST();
        $schema = simplexml_load_file($this->pathToTestFiles . '/' . $this->schemaFile);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        list($type) = $typeList;

        $this->assertEquals($expectedType, $type);
    }

    public function testParseTree() {
        $expectedTree = $this->getParseTree();
        $xml = simplexml_load_file($this->pathToTestFiles . '/' . $this->schemaFile);

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($xml);

        $parsedTree = $parser->parse();

        $this->assertTrue($parsedTree instanceof PiBX_ParseTree_Tree);
        $this->assertTrue($expectedTree instanceof PiBX_ParseTree_Tree);

        $this->assertEquals($expectedTree, $parsedTree);
    }
}