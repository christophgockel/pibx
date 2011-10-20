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
require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once 'PiBX/Runtime/Binding.php';
require_once 'PiBX/Runtime/Unmarshaller.php';
require_once dirname(__FILE__) . '/../../_files/EasyPO/Customer.php';
require_once dirname(__FILE__) . '/../../_files/EasyPO/LineItem.php';
require_once dirname(__FILE__) . '/../../_files/EasyPO/PurchaseOrder.php';
require_once dirname(__FILE__) . '/../../_files/EasyPO/Shipper.php';

class PiBX_Scenarios_EasyPO_UnmarshallerTest extends PHPUnit_Framework_TestCase {

    public function testUnmarshalling() {
        $booksXml = <<<XML
<?xml version="1.0"?>
<purchase-order xmlns="http://openuri.org/easypo">
  <customer>
    <name>Gladys Kravitz</name>
    <address>Anytown, PA</address>
  </customer>
  <date>2003-01-07T14:16:00-05:00</date>
  <line-item>
    <description>Burnham's Celestial Handbook, Vol 1</description>
    <per-unit-ounces>5</per-unit-ounces>
    <price>21.79</price>
    <quantity>2</quantity>
  </line-item>
  <line-item>
    <description>Burnham's Celestial Handbook, Vol 2</description>
    <per-unit-ounces>5</per-unit-ounces>
    <price>19.89</price>
    <quantity>2</quantity>
  </line-item>
  <shipper>
    <name>ZipShip</name>
    <per-ounce-rate>0.74</per-ounce-rate>
  </shipper>
</purchase-order>
XML;
        $filepath = dirname(__FILE__) . '/../../_files/EasyPO/';
        $binding = new PiBX_Runtime_Binding($filepath . '/binding.xml');
		$unmarshaller = new PiBX_Runtime_Unmarshaller($binding);

        $po = new PurchaseOrder();
        $po->setDate('2003-01-07T14:16:00-05:00');

        $customer = new Customer();
        $customer->setName('Gladys Kravitz');
        $customer->setAddress('Anytown, PA');

        $lineItem1 = new LineItem();
        $lineItem1->setDescription('Burnham\'s Celestial Handbook, Vol 1');
        $lineItem1->setPerUnitOunces('5');
        $lineItem1->setPrice(21.79);
        $lineItem1->setQuantity(2);

        $lineItem2 = new LineItem();
        $lineItem2->setDescription('Burnham\'s Celestial Handbook, Vol 2');
        $lineItem2->setPerUnitOunces('5');
        $lineItem2->setPrice(19.89);
        $lineItem2->setQuantity(2);

        $shipper = new Shipper();
        $shipper->setName('ZipShip');
        $shipper->setPerOunceRate(0.74);

        $po->setCustomer($customer);
        $po->setLineItems(array($lineItem1, $lineItem2));
        $po->setShipper($shipper);

        $object = $unmarshaller->unmarshal($booksXml);
        
        $this->assertEquals($po, $object);
    }
}
