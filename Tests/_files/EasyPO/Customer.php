<?php
class Customer {
	private $name;
	private $address;
	private $age;
	private $moo;
	private $poo;

	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setAddress($address) {
		$this->address = $address;
	}
	public function getAddress() {
		return $this->address;
	}
	public function setAge($age) {
		$this->age = $age;
	}
	public function getAge() {
		return $this->age;
	}
	public function setMoo($moo) {
		$this->moo = $moo;
	}
	public function getMoo() {
		return $this->moo;
	}
	public function setPoo($poo) {
		$this->poo = $poo;
	}
	public function getPoo() {
		return $this->poo;
	}
}