<?php
    // require_once "../src/Book.php";

    class Patron
    {
        private $id;
        private $name;

        function __construct($name, $id= null)
        {
            $this->name = $name;
            $this->id = $id;
        }

        function setName($name)
        {
            $this->name = $name;
        }

    	function getName()
        {
    		return $this->name;
    	}

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO patrons (name) VALUES ('{$this->getName()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_patrons = $GLOBALS['DB']->query("SELECT * FROM patrons;");
            $patrons = array();
            foreach ($returned_patrons as $patron) {
                $name = $patron['name'];
                $id = $patron['id'];
                $new_patron = new Patron($name, $id);
                array_push($patrons, $new_patron);
            }
            return $patrons;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons;");
        }

        static function find($search_id)
        {
            $found_patron = $GLOBALS['DB']->query("SELECT * FROM patrons WHERE id = {$search_id};");
            $query = $found_patron->fetchAll(PDO::FETCH_ASSOC);
            $name = $query[0]['name'];
            $id = $query[0]['id'];
            $found_patron = new Patron($name, $id);

            return $found_patron;
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons WHERE id = {$this->getId()};");
        }

        function updateName($new_name)
        {
            $GLOBALS['DB']->exec("UPDATE students SET first_name = '{$new_name}' WHERE id = {$this->getId()};");
            $this->setName($new_name);
        }

        function addBook($book)
        {
            $book_id = $book->getId();
            $patron_id = $this->getId();
            $GLOBALS['DB']->exec("INSERT INTO books_patrons (book_id, patron_id) VALUES ({$book_id}, {$patron_id});");
        }

        function getBooks()
        {
            $returned_books = $GLOBALS['DB']->query(
                "SELECT books.*
                FROM patrons
                JOIN books_patrons ON (books_patrons.patron_id = patrons.id)
                JOIN books ON (books.id = books_patrons.book_id)
                WHERE patrons.id = {$this->getId()};"
            );

            $books = array();
            foreach ($returned_books as $book) {
                $id = $book['id'];
                $title = $book['title'];
                $due_date = $book['due_date'];
                $checkout_date = $book['checkout_date'];
                $author = $book['author'];
                $new_book = new book($title, $checkout_date, $due_date, $author, $id);
                array_push($books, $new_book);
            }
            return $books;
        }
}

?>
