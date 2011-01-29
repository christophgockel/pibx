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
		if (!is_string($name)) {
			throw new InvalidArgumentException('"' . $name . '" is not a valid string.');
		}
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function setISBN($isbn) {
		if (!is_long($isbn)) {
			throw new InvalidArgumentException('"' . $isbn . '" is not a valid long.');
		}
		$this->isbn = $isbn;
	}
	public function getISBN() {
		return $this->isbn;
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
	public function setAuthorNames(array $authors) {
		foreach ($authors as &$a) {
			if (!is_string($a)) {
				throw new InvalidArgumentException('Invalid list. All containing elements have to be of type "string".');
			}
		}
		$this->authors = $authors;
	}
	public function getAuthorNames() {
		return $this->authors;
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
	public function setPublicationDate($publicationdate) {
		if (!preg_match('/\d{4}-\d{2}-\d{2}/ism')) {
			throw new InvalidArgumentException('Unexpected date format:' . $publicationdate . '. Expected is: yyyy-mm-dd.');
		}
		$this->publicationdate = $publicationdate;
	}
	public function getPublicationDate() {
		return $this->publicationdate;
	}
	public function setBookcategory($bookcategory) {
		$this->bookcategory = $bookcategory;
	}
	public function getBookcategory() {
		return $this->bookcategory;
	}
	public function setItemId($itemid) {
		if (!is_string($itemid)) {
			throw new InvalidArgumentException('"' . $itemid . '" is not a valid string.');
		}
		$this->itemid = $itemid;
	}
	public function getItemId() {
		return $this->itemid;
	}
}