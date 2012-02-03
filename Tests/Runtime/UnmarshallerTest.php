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
require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'PiBX/Runtime/Binding.php';
require_once 'PiBX/Runtime/Unmarshaller.php';
require_once dirname(__FILE__) . '/../_files/Books/BookType.php';
require_once dirname(__FILE__) . '/../_files/Books/Collection.php';

class PiBX_Runtime_UnmarshallerTest extends PHPUnit_Framework_TestCase {

    public function testTwoBooks() {
        $booksXml = <<<XML
<?xml version="1.0"?>
<Collection>
  <books>
    <book itemId="0001">
      <name>Book #1 Name</name>
      <ISBN>123456789</ISBN>
      <price>$ 1.23</price>
      <authors>
        <authorName>Adam</authorName>
        <authorName>Bob</authorName>
        <authorName>Eve</authorName>
      </authors>
      <description>Book #1 Description</description>
      <promotion>
        <Discount>7%</Discount>
      </promotion>
      <publicationDate>2010-12-29</publicationDate>
      <bookCategory>fiction</bookCategory>
    </book>
    <book itemId="0002">
      <name>Book #2 Name</name>
      <ISBN>987654321</ISBN>
      <price>$ 4.56</price>
      <authors>
        <authorName>Mark</authorName>
        <authorName>Kate</authorName>
      </authors>
      <description>Book #2 Description</description>
      <promotion>
        <None>Regular price</None>
      </promotion>
      <publicationDate>2010-06-01</publicationDate>
      <bookCategory>novel</bookCategory>
    </book>
  </books>
</Collection>
XML;
        $filepath = dirname(__FILE__) . '/../_files/Books/';
        $binding = new PiBX_Runtime_Binding($filepath . '/binding.xml');
		$unmarshaller = new PiBX_Runtime_Unmarshaller($binding);

        $c = new Collection();

        $book1 = new BookType();
        $book1->setName('Book #1 Name');
        $book1->setIsbn(123456789);
        $book1->setPrice('$ 1.23');
        $book1->setAuthorNames(array('Adam', 'Bob', 'Eve'));
        $book1->setDescription('Book #1 Description');
        $book1->setPromotionDiscount('7%');
        $book1->setPublicationdate('2010-12-29');
        $book1->setBookcategory('fiction');
        $book1->setItemId('0001');

        $book2 = new BookType();
        $book2->setName('Book #2 Name');
        $book2->setIsbn(987654321);
        $book2->setPrice('$ 4.56');
        $book2->setAuthorNames(array('Mark', 'Kate'));
        $book2->setDescription('Book #2 Description');
        $book2->setPromotionNone('Regular price');
        $book2->setPublicationdate('2010-06-01');
        $book2->setBookcategory('novel');
        $book2->setItemId('0002');

        $list = array($book1, $book2);

        $c->setBooks($list);

        $object = $unmarshaller->unmarshal($booksXml);
        
        $this->assertEquals($c, $object);
        
    }
    public function testOneBook() {
        $bookXml = <<<XML
<?xml version="1.0"?>
<book itemId="0815">
    <name>Book #1 Name</name>
    <ISBN>123456789</ISBN>
    <price>$ 1.23</price>
    <authors>
        <authorName>Adam</authorName>
        <authorName>Bob</authorName>
        <authorName>Eve</authorName>
    </authors>
    <description>Book #1 Description</description>
    <promotion>
        <Discount>7%</Discount>
    </promotion>
    <publicationDate>2010-12-29</publicationDate>
    <bookCategory>fiction</bookCategory>
</book>
XML;
        $filepath = dirname(__FILE__) . '/../_files/Books/';
        $binding = new PiBX_Runtime_Binding($filepath . '/binding.xml');
		$unmarshaller = new PiBX_Runtime_Unmarshaller($binding);

        $book = new BookType();
        $book->setName('Book #1 Name');
        $book->setIsbn(123456789);
        $book->setPrice('$ 1.23');
        $book->setAuthorNames(array('Adam', 'Bob', 'Eve'));
        $book->setDescription('Book #1 Description');
        $book->setPromotionDiscount('7%');
        $book->setPublicationdate('2010-12-29');
        $book->setBookcategory('fiction');
        $book->setItemId('0815');

        $object = $unmarshaller->unmarshal($bookXml);
        
        $this->assertEquals($book, $object);
    }
}
