<?php
class Collection {
	private $books;

	public function setBooks(array $books) {
		foreach ($books as &$b) {
			if (get_class($b) !== 'BookType') {
				throw new InvalidArgumentException('Invalid list. All containing elements have to be of type "BookType".');
			}
		}
		$this->books = $books;
	}
	public function getBooks() {
		return $this->books;
	}
}