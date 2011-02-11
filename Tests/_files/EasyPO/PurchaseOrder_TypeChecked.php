<?php
class PurchaseOrder {
	private $customer;
	private $date;
	private $lineitemlist;
	private $shipper;

	public function setCustomer(Customer $customer) {
		$this->customer = $customer;
	}
	public function getCustomer() {
		return $this->customer;
	}
	public function setDate($date) {
		$this->date = $date;
	}
	public function getDate() {
		return $this->date;
	}
	public function setLineItems(array $lineitemlist) {
		foreach ($lineitemlist as &$l) {
			if (get_class($l) !== 'LineItem') {
				throw new InvalidArgumentException('Invalid list. All containing elements have to be of type "LineItem".');
			}
		}
		$this->lineitemlist = $lineitemlist;
	}
	public function getLineItems() {
		return $this->lineitemlist;
	}
	public function setShipper(Shipper $shipper) {
		$this->shipper = $shipper;
	}
	public function getShipper() {
		return $this->shipper;
	}
}