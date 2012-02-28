<?php
class BookType {
	private $name;
	private $ISBN;
	private $price;
	private $authorNameList;
	private $description;
	private $promotionSelect = -1;
	private $PROMOTION_DISCOUNT_CHOICE = 0;
	private $PROMOTION_NONE_CHOICE = 1;
	private $promotionDiscount;
	private $promotionNone;
	private $publicationDate;
	private $bookCategory;
	private $itemId;

	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setISBN($ISBN) {
		$this->ISBN = $ISBN;
	}
	public function getISBN() {
		return $this->ISBN;
	}
	public function setPrice($price) {
		$this->price = $price;
	}
	public function getPrice() {
		return $this->price;
	}
	public function setAuthorNames(array $authorNameList) {
		$this->authorNameList = $authorNameList;
	}
	public function getAuthorNames() {
		return $this->authorNameList;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getDescription() {
		return $this->description;
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
	public function setPublicationDate($publicationDate) {
		$this->publicationDate = $publicationDate;
	}
	public function getPublicationDate() {
		return $this->publicationDate;
	}
	public function setBookCategory($bookCategory) {
		$this->bookCategory = $bookCategory;
	}
	public function getBookCategory() {
		return $this->bookCategory;
	}
	public function setItemId($itemId) {
		$this->itemId = $itemId;
	}
	public function getItemId() {
		return $this->itemId;
	}
	private function setPromotionSelect($choice) {
		if ($this->promotionSelect == -1) {
			$this->promotionSelect = $choice;
		} elseif ($this->promotionSelect != $choice) {
			throw new RuntimeException('Need to call clearPromotionSelect() before changing existing choice');
		}
	}
}