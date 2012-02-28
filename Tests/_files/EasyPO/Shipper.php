<?php
class Shipper {
	private $name;
	private $perOunceRate;

	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setPerOunceRate($perOunceRate) {
		$this->perOunceRate = $perOunceRate;
	}
	public function getPerOunceRate() {
		return $this->perOunceRate;
	}
}