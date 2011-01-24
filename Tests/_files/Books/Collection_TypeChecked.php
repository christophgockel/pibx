<?php
class Collection {
	private $books;

	public function setBooks($books) {
		if (!is_array($books)) {
			throw new InvalidArgumentException('Not a valid list');
		}
		$this->books = $books;
	}
	public function getBooks() {
		return $this->books;
	}
}