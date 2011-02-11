<?php
class LineItem {
	private $description;
	private $perunitounces;
	private $price;
	private $quantity;

	public function setDescription($description) {
		$this->description = $description;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setPerUnitOunces($perunitounces) {
		$this->perunitounces = $perunitounces;
	}
	public function getPerUnitOunces() {
		return $this->perunitounces;
	}
	public function setPrice($price) {
		$this->price = $price;
	}
	public function getPrice() {
		return $this->price;
	}
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
	}
	public function getQuantity() {
		return $this->quantity;
	}
}