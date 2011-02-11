<?php
class Shipper {
	private $name;
	private $perouncerate;

	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setPerOunceRate($perouncerate) {
		$this->perouncerate = $perouncerate;
	}
	public function getPerOunceRate() {
		return $this->perouncerate;
	}
}