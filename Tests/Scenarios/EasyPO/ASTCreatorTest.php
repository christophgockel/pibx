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
require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
/**
 * Testing the ASTCreator in scenario "EasyPO".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_EasyPO_ASTCreatorTest extends PHPUnit_Framework_TestCase {
    public function testEasyPoXSD() {
        $filepath = dirname(__FILE__) . '/../../_files/EasyPO/';
        $schema = simplexml_load_file($filepath . '/easypo.xsd');

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);

        $parsedTree = $parser->parse();

        $namespaces = array(
            'xs' => 'http://www.w3.org/2001/XMLSchema',
            'po' => 'http://openuri.org/easypo'
        );
        
        $expectedTypeList = array();

        $expectedType1 = new PiBX_AST_Type('purchase-order');
        $expectedType1->setAsRoot();
        $expectedType1->setTargetNamespace('http://openuri.org/easypo');
        $expectedType1->setNamespaces($namespaces);
        $expectedType1->add(new PiBX_AST_TypeAttribute('customer', 'customer'));
        $expectedType1->add(new PiBX_AST_TypeAttribute('date', 'dateTime'));
        $ta = new PiBX_AST_TypeAttribute('line-item', 'line-item');
        $ta->add(new PiBX_AST_CollectionItem('line-item', 'line-item'));
        $expectedType1->add($ta);
        $expectedType1->add(new PiBX_AST_TypeAttribute('shipper', 'shipper'));

        $expectedType2 = new PiBX_AST_Type('customer');
        $expectedType2->add(new PiBX_AST_TypeAttribute('name', 'string'));
        $expectedType2->add(new PiBX_AST_TypeAttribute('address', 'string'));
        $ta = new PiBX_AST_TypeAttribute('age', 'int');
        $ta->setStyle('attribute');
        $expectedType2->add($ta);
        $ta = new PiBX_AST_TypeAttribute('moo', 'int');
        $ta->setStyle('attribute');
        $expectedType2->add($ta);
        $ta = new PiBX_AST_TypeAttribute('poo', 'int');
        $ta->setStyle('attribute');
        $expectedType2->add($ta);

        $expectedType3 = new PiBX_AST_Type('line-item');
        $expectedType3->add(new PiBX_AST_TypeAttribute('description', 'string'));
        $expectedType3->add(new PiBX_AST_TypeAttribute('per-unit-ounces', 'decimal'));
        $expectedType3->add(new PiBX_AST_TypeAttribute('price', 'decimal'));
        $expectedType3->add(new PiBX_AST_TypeAttribute('quantity', 'integer'));

        $expectedType4 = new PiBX_AST_Type('shipper');
        $expectedType4->add(new PiBX_AST_TypeAttribute('name', 'string'));
        $expectedType4->add(new PiBX_AST_TypeAttribute('per-ounce-rate', 'decimal'));

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchema($schema);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();
        $this->assertEquals(4, count($typeList));

        $this->assertEquals($expectedType1, $typeList[0]);
        $this->assertEquals($expectedType2, $typeList[1]);
        $this->assertEquals($expectedType3, $typeList[2]);
        $this->assertEquals($expectedType4, $typeList[3]);
    }
}
