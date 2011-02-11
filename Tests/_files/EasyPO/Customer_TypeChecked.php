<?php
class Customer {
	private $name;
	private $address;
	private $age;
	private $moo;
	private $poo;

	public function setName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException('"' . $name . '" is not a valid string.');
		}
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setAddress($address) {
		if (!is_string($address)) {
			throw new InvalidArgumentException('"' . $address . '" is not a valid string.');
		}
		$this->address = $address;
	}
	public function getAddress() {
		return $this->address;
	}
	public function setAge($age) {
		if (!is_int($age)) {
			throw new InvalidArgumentException('"' . $age . '" is not a valid int.');
		}
		$this->age = $age;
	}
	public function getAge() {
		return $this->age;
	}
	public function setMoo($moo) {
		if (!is_int($moo)) {
			throw new InvalidArgumentException('"' . $moo . '" is not a valid int.');
		}
		$this->moo = $moo;
	}
	public function getMoo() {
		return $this->moo;
	}
	public function setPoo($poo) {
		if (!is_int($poo)) {
			throw new InvalidArgumentException('"' . $poo . '" is not a valid int.');
		}
		$this->poo = $poo;
	}
	public function getPoo() {
		return $this->poo;
	}
}