<?php
class BookType {
	private $name;
	private $isbn;
	private $price;
	private $authors;
	private $description;
	private $promotionSelect = -1;
	private $PROMOTION_DISCOUNT_CHOICE = 0;
	private $PROMOTION_NONE_CHOICE = 1;
	private $promotionDiscount;
	private $promotionNone;
	private $publicationdate;
	private $bookcategory;
	private $itemid;

	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setISBN($isbn) {
		$this->isbn = $isbn;
	}
	public function getISBN() {
		return $this->isbn;
	}
	public function setPrice($price) {
		$this->price = $price;
	}
	public function getPrice() {
		return $this->price;
	}
	public function setAuthorNames(array $authors) {
		$this->authors = $authors;
	}
	public function getAuthorNames() {
		return $this->authors;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getDescription() {
		return $this->description;
	}
	private function setPromotionSelect($choice) {
		if ($this->promotionSelect == -1) {
			$this->promotionSelect = $choice;
		} elseif ($this->promotionSelect != $choice) {
			throw new RuntimeException('Need to call clearPromotionSelect() before changing existing choice');
		}
	}
	public function clearPromotionSelect() {
		$this->promotionSelect = -1;
	}
	public function ifPromotionDiscount() {
		return $this->promotionSelect == $this->PROMOTION_DISCOUNT_CHOICE;
	}
	public function setPromotionDiscount($promotionDiscount) {
		$this->setPromotionSelect($this->PROMOTION_DISCOUNT_CHOICE);
		$this->promotionDiscount = $promotionDiscount;
	}
	public function getPromotionDiscount() {
		return $this->promotionDiscount;
	}
	public function ifPromotionNone() {
		return $this->promotionSelect == $this->PROMOTION_NONE_CHOICE;
	}
	public function setPromotionNone($promotionNone) {
		$this->setPromotionSelect($this->PROMOTION_NONE_CHOICE);
		$this->promotionNone = $promotionNone;
	}
	public function getPromotionNone() {
		return $this->promotionNone;
	}
	public function setPublicationDate($publicationdate) {
		$this->publicationdate = $publicationdate;
	}
	public function getPublicationDate() {
		return $this->publicationdate;
	}
	public function setBookCategory($bookcategory) {
		$this->bookcategory = $bookcategory;
	}
	public function getBookCategory() {
		return $this->bookcategory;
	}
	public function setItemId($itemid) {
		$this->itemid = $itemid;
	}
	public function getItemId() {
		return $this->itemid;
	}
}