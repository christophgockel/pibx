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
		if (!is_string($name)) {
			throw new InvalidArgumentException('"' . $name . '" is not a valid string.');
		}
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setISBN($ISBN) {
		if (!is_long($ISBN)) {
			throw new InvalidArgumentException('"' . $ISBN . '" is not a valid long.');
		}
		$this->ISBN = $ISBN;
	}
	public function getISBN() {
		return $this->ISBN;
	}
	public function setPrice($price) {
		if (!is_string($price)) {
			throw new InvalidArgumentException('"' . $price . '" is not a valid string.');
		}
		$this->price = $price;
	}
	public function getPrice() {
		return $this->price;
	}
	public function setAuthorNames(array $authorNameList) {
		foreach ($authorNameList as &$a) {
			if (!is_string($a)) {
				throw new InvalidArgumentException('Invalid list. All containing elements have to be of type "string".');
			}
		}
		$this->authorNameList = $authorNameList;
	}
	public function getAuthorNames() {
		return $this->authorNameList;
	}
	public function setDescription($description) {
		if (!is_string($description)) {
			throw new InvalidArgumentException('"' . $description . '" is not a valid string.');
		}
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
		if (!is_string($promotionDiscount)) {
			throw new InvalidArgumentException('"' . $promotionDiscount . '" is not a valid string.');
		}
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
		if (!is_string($promotionNone)) {
			throw new InvalidArgumentException('"' . $promotionNone . '" is not a valid string.');
		}
		$this->promotionNone = $promotionNone;
	}
	public function getPromotionNone() {
		return $this->promotionNone;
	}
	public function setPublicationDate($publicationDate) {
		if (!preg_match('/\d{4}-\d{2}-\d{2}/ism')) {
			throw new InvalidArgumentException('Unexpected date format:' . $publicationDate . '. Expected is: yyyy-mm-dd.');
		}
		$this->publicationDate = $publicationDate;
	}
	public function getPublicationDate() {
		return $this->publicationDate;
	}
	public function setBookCategory($bookCategory) {
		if (($bookCategory != 'magazine') || ($bookCategory != 'novel') || ($bookCategory != 'fiction') || ($bookCategory != 'other')) {
			throw new InvalidArgumentException('Unexpected value "' . $bookCategory . '". Expected is one of the following: "magazine", "novel", "fiction", "other".');
		}
		$this->bookCategory = $bookCategory;
	}
	public function getBookCategory() {
		return $this->bookCategory;
	}
	public function setItemId($itemId) {
		if (!is_string($itemId)) {
			throw new InvalidArgumentException('"' . $itemId . '" is not a valid string.');
		}
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