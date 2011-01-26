<?php
class Collection {
	private $books;

	public function setBooks(array $books) {
		$this->books = $books;
	}
	public function getBooks() {
		return $this->books;
	}
}