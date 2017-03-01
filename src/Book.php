<?php
    // require_once "../src/Patron.php";

    class Book {

        private $id;
        private $title;
        private $checkout_date;
        private $due_date;
        private $author;


        function __construct($title, $checkout_date, $due_date, $author, $id = null)
        {
            $this->title = $title;
            $this->checkout_date = $checkout_date;
            $this->due_date = $due_date;
            $this->author = $author;
            $this->id = $id;
        }

        function getId()
        {
            return $this->id;
        }

        function getTitle()
        {
            return $this->title;
        }

        function setTitle($title)
        {
            $this->title = $title;
        }

        function getCheckoutDate()
        {
            return $this->checkout_date;
        }

        function setCheckoutDate($checkout_date)
        {
            $this->checkout_date = $checkout_date;
        }
        function getDueDate()
        {
            return $this->due_date;
        }

        function setDueDate($due_date)
        {
          $this->due_date = $due_date;
        }

        function setAuthor($author)
        {
          $this->author = $author;
        }
        function getAuthor()
        {
            return $this->author;
        }


        function save() {
            $GLOBALS['DB']->exec("INSERT INTO books (title, checkout_date, due_date, author) VALUES ('{$this->getTitle()}','{$this->getCheckoutDate()}', '{$this->getDueDate()}', '{$this->getAuthor()}');");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll() {
            $returned_books = $GLOBALS['DB']->query("SELECT * FROM books;");
            $books = array();
            foreach ($returned_books as $book) {
                $id = $book['id'];
                $title = $book['title'];
                $due_date = $book['due_date'];
                $checkout_date = $book['checkout_date'];
                $author = $book['author'];
                $new_book = new Book($title, $checkout_date, $due_date, $author, $id);
                array_push($books, $new_book);
            }
            return $books;
        }

        static function deleteAll() {
            $GLOBALS['DB']->exec("DELETE FROM books;");
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM books WHERE id = {$this->getId()};");
        }

        function updateName($new_name)
        {
            $GLOBALS['DB']->exec("UPDATE books SET name = '{$new_name}' WHERE id = {$this->getId()};");
            $this->setName($new_name);
        }

        function update($due_date, $checkout_date)
        {
            $GLOBALS['DB']->exec("UPDATE books SET due_date = '{$due_date}', SET checkout_date = '{$checkout_date}' WHERE id = {$this->getId()};");
            $this->setDueDate($due_date);
            $this->setCheckoutDate($checkout_date);
        }

        static function find($search_id)
        {
            $found_book = $GLOBALS['DB']->query("SELECT * FROM books WHERE id = {$search_id};");
            $query = $found_book->fetchAll(PDO::FETCH_ASSOC);
            $id = $query[0]['id'];
            $title = $query[0]['title'];
            $due_date = $query[0]['due_date'];
            $checkout_date = $query[0]['checkout_date'];
            $author = $query[0]['author'];
            $found_book = new Book($title, $due_date, $checkout_date, $author, $id);

            return $found_book;
        }

        function addPatron($patron)
        {
            $patron_id = $patron->getId();
            $book_id = $this->getId();
            $GLOBALS['DB']->exec("INSERT INTO books_patrons (book_id, patron_id) VALUES ({$book_id}, {$patron_id});");
        }
        function getpatrons()
        {
            $returned_patrons = $GLOBALS['DB']->query(
                "SELECT patrons.*
                FROM books
                JOIN books_patrons ON (books_patrons.book_id = books.id)
                JOIN patrons ON (patrons.id = books_patrons.patron_id)
                WHERE books.id = {$this->getId()};"
            );
            // var_dump($returned_students);
            $patrons = array();
            if ($returned_patrons == null) {
                return null;
            }
            foreach ($returned_patrons as $patron) {
                $id = $patron['id'];
                $name = $patron['name'];
                $new_patron = new Patron($name, $id);
                array_push($patrons, $new_patron);
            }
            return $patrons;
        }

    }
?>
