<?php
class LineItem {
	private $description;
	private $perUnitOunces;
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
	public function setPerUnitOunces($perUnitOunces) {
		$this->perUnitOunces = $perUnitOunces;
	}
	public function getPerUnitOunces() {
		return $this->perUnitOunces;
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