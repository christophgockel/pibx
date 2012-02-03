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
require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once 'PHPUnit/Autoload.php';
require_once 'PiBX/CodeGen/ASTCreator.php';
require_once 'PiBX/CodeGen/SchemaParser.php';
/**
 * Testing the ASTCreator in scenario "Books".
 *
 * @author Christoph Gockel
 */
class PiBX_Scenarios_Books_ASTCreatorTest extends PHPUnit_Framework_TestCase {
    public function testBooksXSD() {
        $filepath = dirname(__FILE__) . '/../../_files/Books';
        $schemaFile = 'books.xsd';

        $parser = new PiBX_CodeGen_SchemaParser();
        $parser->setSchemaFile($filepath . DIRECTORY_SEPARATOR . $schemaFile);

        $parsedTree = $parser->parse();

        $namespaces = array(
            'xs' => 'http://www.w3.org/2001/XMLSchema',
        );
        
        $expectedTypes = array();

        $type1 = new PiBX_AST_Type('Collection');
        $type1->setAsRoot();
        $type1->setNamespaces($namespaces);
            $type1_collection = new PiBX_AST_Collection('books');
                $type1_collectionItem = new PiBX_AST_CollectionItem('book', 'bookType');
            $type1_collection->add($type1_collectionItem);
        $type1->add($type1_collection);

        $expectedTypes[] = $type1;

        
        $type2 = new PiBX_AST_Type('bookType');
        $type2->setNamespaces($namespaces);
            $type2->add(new PiBX_AST_TypeAttribute('name', 'string'));
            $type2->add(new PiBX_AST_TypeAttribute('ISBN', 'long'));
            $type2->add(new PiBX_AST_TypeAttribute('price', 'string'));

            $type2_collection = new PiBX_AST_Collection('authors');
                $type2_collectionItem = new PiBX_AST_CollectionItem('authorName', 'string');
            $type2_collection->add($type2_collectionItem);
        $type2->add($type2_collection);

            $type2->add(new PiBX_AST_TypeAttribute('description', 'string', true));

            $type2_structure = new PiBX_AST_Structure('promotion');
                    $type2_structure->setStructureType(PiBX_AST_StructureType::CHOICE());
                    $type2_structure->add(new PiBX_AST_StructureElement('Discount', 'string'));
                    $type2_structure->add(new PiBX_AST_StructureElement('None', 'string'));
        $type2->add($type2_structure);

            $type2->add(new PiBX_AST_TypeAttribute('publicationDate', 'date'));

                $type2_enumeration = new PiBX_AST_Enumeration('bookCategory');
                    $type2_enumeration->add(new PiBX_AST_EnumerationValue('magazine', 'NCName'));
                    $type2_enumeration->add(new PiBX_AST_EnumerationValue('novel', 'NCName'));
                    $type2_enumeration->add(new PiBX_AST_EnumerationValue('fiction', 'NCName'));
                    $type2_enumeration->add(new PiBX_AST_EnumerationValue('other', 'NCName'));
        $type2->add($type2_enumeration);

            $type2_attribute = new PiBX_AST_TypeAttribute('itemId', 'string');
                $type2_attribute->setStyle('attribute');
        $type2->add($type2_attribute);

        $expectedTypes[] = $type2;


        $type3 = new PiBX_AST_Type('bookCategoryType');
        $type3->setAsRoot();
        $type3->setNamespaces($namespaces);
            $type3_enumeration = new PiBX_AST_Enumeration();
                $type3_enumeration->add(new PiBX_AST_EnumerationValue('magazine', 'string'));
                $type3_enumeration->add(new PiBX_AST_EnumerationValue('novel', 'string'));
                $type3_enumeration->add(new PiBX_AST_EnumerationValue('fiction', 'string'));
                $type3_enumeration->add(new PiBX_AST_EnumerationValue('other', 'string'));
        $type3->add($type3_enumeration);

        $expectedTypes[] = $type3;

        
        $parser = new PiBX_CodeGen_SchemaParser($filepath . DIRECTORY_SEPARATOR . $schemaFile);
        $tree = $parser->parse();

        $creator = new PiBX_CodeGen_ASTCreator(new PiBX_CodeGen_TypeUsage());
        $tree->accept($creator);

        $typeList = $creator->getTypeList();

        $this->assertEquals($expectedTypes, $typeList);
    }
}
