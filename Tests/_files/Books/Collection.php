<?php
class Collection {
	private $bookList;

	public function setBooks(array $bookList) {
		$this->bookList = $bookList;
	}
	public function getBooks() {
		return $this->bookList;
	}
}