<?php
class PurchaseOrder {
	private $customer;
	private $date;
	private $lineItemList;
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
	public function setLineItems(array $lineItemList) {
		$this->lineItemList = $lineItemList;
	}
	public function getLineItems() {
		return $this->lineItemList;
	}
	public function setShipper(Shipper $shipper) {
		$this->shipper = $shipper;
	}
	public function getShipper() {
		return $this->shipper;
	}
}