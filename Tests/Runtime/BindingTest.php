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
require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'PiBX/Runtime/Binding.php';

class PiBX_Runtime_BindingTest extends PHPUnit_Framework_TestCase {

    public function testBooksBinding() {
        $filepath = dirname(__FILE__) . '/../_files/Books';
        $bindingFile = $filepath . '/binding.xml';
        $expectedXml = file_get_contents($bindingFile);
        
        $binding = new PiBX_Runtime_Binding($bindingFile);

        $asts = $binding->parse();
        $this->assertEquals(2, count($asts));

        // first tree/type
        $expectedAst1 = new PiBX_AST_Type('Collection', 'Collection');
        //$expectedAst1->setAsRoot();
        $expectedAst1->setClassName('Collection');
        $collection = new PiBX_AST_Collection('books');
        $collection->setGetMethod('getBooks');
        $collection->setSetMethod('setBooks');
        $structure = new PiBX_AST_Structure('book');
        $structure->setXsdType('bookType');

        $expectedAst1->add(
            $collection->add(
                $structure
            )
        );

        $this->assertEquals($expectedAst1, $asts[0]);

        // second tree/type
        $expectedAst2 = new PiBX_AST_Type('BookType', 'BookType');
        $expectedAst2->setClassName('BookType');

        $value1 = new PiBX_AST_TypeAttribute('name');
        $value1->setStyle('element');
        $value1->setGetMethod('getName');
        $value1->setSetMethod('setName');

        $expectedAst2->add($value1);

        $value2 = new PiBX_AST_TypeAttribute('ISBN');
        $value2->setStyle('element');
        $value2->setGetMethod('getISBN');
        $value2->setSetMethod('setISBN');

        $expectedAst2->add($value2);

        $value3 = new PiBX_AST_TypeAttribute('price');
        $value3->setStyle('element');
        $value3->setGetMethod('getPrice');
        $value3->setSetMethod('setPrice');

        $expectedAst2->add($value3);

        $value4 = new PiBX_AST_Collection('authors');
        $value4->setGetMethod('getAuthorNames');
        $value4->setSetMethod('setAuthorNames');
        $value4item = new PiBX_AST_CollectionItem('authorName');
        $value4->add($value4item);

        $expectedAst2->add($value4);

        $value5 = new PiBX_AST_TypeAttribute('description');
        $value5->setStyle('element');
        $value5->setGetMethod('getDescription');
        $value5->setSetMethod('setDescription');

        $expectedAst2->add($value5);

        $value6 = new PiBX_AST_Structure('promotion');
        $value6->setType(PiBX_AST_StructureType::CHOICE());
        $value6Item1 = new PiBX_AST_StructureElement('');
        $value6Item1->setTestMethod('ifPromotionDiscount');
        $value6Item1->setGetMethod('getPromotionDiscount');
        $value6Item1->setSetMethod('setPromotionDiscount');
        $value6Item1Value = new PiBX_AST_TypeAttribute('Discount');
        $value6Item1Value->setStyle('element');
        $value6Item1Value->setGetMethod('getPromotionDiscount');
        $value6Item1Value->setSetMethod('setPromotionDiscount');
        $value6Item1->add($value6Item1Value);

        $value6Item2 = new PiBX_AST_StructureElement('');
        $value6Item2->setTestMethod('ifPromotionNone');
        $value6Item2->setGetMethod('getPromotionNone');
        $value6Item2->setSetMethod('setPromotionNone');
        $value6Item2Value = new PiBX_AST_TypeAttribute('None');
        $value6Item2Value->setStyle('element');
        $value6Item2Value->setGetMethod('getPromotionNone');
        $value6Item2Value->setSetMethod('setPromotionNone');
        $value6Item2->add($value6Item2Value);

        $value6->add($value6Item1);
        $value6->add($value6Item2);

        $expectedAst2->add($value6);

        $value7 = new PiBX_AST_TypeAttribute('publicationDate');
        $value7->setStyle('element');
        $value7->setGetMethod('getPublicationDate');
        $value7->setSetMethod('setPublicationDate');

        $expectedAst2->add($value7);

        $value8 = new PiBX_AST_TypeAttribute('bookCategory');
        $value8->setStyle('element');
        $value8->setGetMethod('getBookCategory');
        $value8->setSetMethod('setBookCategory');

        $expectedAst2->add($value8);

        $this->assertEquals($expectedAst2, $asts[1]);
    }
}
