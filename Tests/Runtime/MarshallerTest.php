<?php

require_once 'PHPUnit/Framework.php';
require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'PiBX/Runtime/Binding.php';
require_once 'PiBX/Runtime/Marshaller.php';
require_once dirname(__FILE__) . '/_files/MarshallerTest/BookType.php';
require_once dirname(__FILE__) . '/_files/MarshallerTest/Collection.php';

class PiBX_MarshallerTest extends PHPUnit_Framework_TestCase {

    public function testTwoBooks() {
        $expectedXml = <<<XML
<?xml version="1.0"?>
<Collection>
  <books>
    <book>
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
    <book>
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
        $filepath = dirname(__FILE__) . '/_files/MarshallerTest';
        $binding = new PiBX_Runtime_Binding($filepath . '/binding.xml');
		$marshaller = new PiBX_Runtime_Marshaller($binding);

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

        $book2 = new BookType();
        $book2->setName('Book #2 Name');
        $book2->setIsbn(987654321);
        $book2->setPrice('$ 4.56');
        $book2->setAuthorNames(array('Mark', 'Kate'));
        $book2->setDescription('Book #2 Description');
        $book2->setPromotionNone('Regular price');
        $book2->setPublicationdate('2010-06-01');
        $book2->setBookcategory('novel');

        $list = array($book1, $book2);

        $c->setBooks($list);

        $xml = $marshaller->marshal($c);

        $this->assertEquals($expectedXml, $xml);

        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $this->assertTrue($dom->schemaValidate($filepath . '/books.xsd'));
    }
}

?>
