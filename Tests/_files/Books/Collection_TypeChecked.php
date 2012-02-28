<?php
class Collection {
	private $bookList;

	public function setBooks(array $bookList) {
		foreach ($bookList as &$b) {
			if (get_class($b) !== 'BookType') {
				throw new InvalidArgumentException('Invalid list. All containing elements have to be of type "BookType".');
			}
		}
		$this->bookList = $bookList;
	}
	public function getBooks() {
		return $this->bookList;
	}
}