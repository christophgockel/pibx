<?php
class Shipper {
	private $name;
	private $perouncerate;

	public function setName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException('"' . $name . '" is not a valid string.');
		}
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