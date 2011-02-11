<?php
class LineItem {
	private $description;
	private $perunitounces;
	private $price;
	private $quantity;

	public function setDescription($description) {
		if (!is_string($description)) {
			throw new InvalidArgumentException('"' . $description . '" is not a valid string.');
		}
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
		if (!is_int($quantity)) {
			throw new InvalidArgumentException('"' . $quantity . '" is not a valid int.');
		}
		$this->quantity = $quantity;
	}
	public function getQuantity() {
		return $this->quantity;
	}
}