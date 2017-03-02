<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Book.php";
    require_once "src/Patron.php";
    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);
    class BookTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Book::deleteAll();
            Patron::deleteAll();
        }

        function test_save()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $result = Book::getAll();

            $this->assertEquals($new_book, $result[0]);
        }

        function test_getAll()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $title2 = 'Eumeswil';
            $due_date2 = '0000-00-00';
            $checkout_date2 = '0000-00-00';
            $author2 = 'Ernst Junger';
            $new_book2 = new Book($title2, $checkout_date2, $due_date2, $author2);
            $new_book2->save();

            $result = Book::getAll();

            $this->assertEquals([$new_book, $new_book2], $result);
        }

        function test_deleteAll()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $title2 = 'Eumeswil';
            $due_date2 = '0000-00-00';
            $checkout_date2 = '0000-00-00';
            $author2 = 'Ernst Junger';
            $new_book2 = new Book($title2, $checkout_date2, $due_date2, $author2);
            $new_book2->save();

            Book::deleteAll();
            $result = Book::getAll();
            $this->assertEquals([], $result);
        }

        function test_delete()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $title2 = 'Eumeswil';
            $due_date2 = '0000-00-00';
            $checkout_date2 = '0000-00-00';
            $author2 = 'Ernst Junger';
            $new_book2 = new Book($title2, $checkout_date2, $due_date2, $author2);
            $new_book2->save();

            $new_book2->delete();

            $result = Book::getAll();

            $this->assertEquals([$new_book], $result);
        }

        function test_updateDueDate()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $new_due_date = '0000-00-01';
            $new_checkout_date = '0000-00-01';
            $new_book->update($new_due_date, $new_checkout_date);
            $result = $new_book->getDueDate();

            $this->assertEquals($new_due_date, $result);
        }

        function test_updateCheckout()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $new_due_date = '0000-00-01';
            $new_checkout_date = '0000-00-01';
            $new_book->update($new_due_date, $new_checkout_date);
            $result = $new_book->getCheckoutDate();

            $this->assertEquals($new_checkout_date, $result);
        }

        function test_find()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $title2 = 'Eumeswil';
            $due_date2 = '0000-00-00';
            $checkout_date2 = '0000-00-00';
            $author2 = 'Ernst Junger';
            $new_book2 = new Book($title2, $checkout_date2, $due_date2, $author2);
            $new_book2->save();

            $result = Book::find($new_book->getId());

            $this->assertEquals($new_book, $result);
        }
        function test_addPatron()
        {
            $name = 'Foo';
            $new_patron = new Patron($name);
            $new_patron->save();

            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $new_patron->addBook($new_book);


            $result = $new_book->getPatrons();

            $this->assertEquals([$new_patron], $result);
        }
        function test_checkIn()
        {
            $name = 'Foo';
            $new_patron = new Patron($name);
            $new_patron->save();

            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $new_patron->addBook($new_book);
            $new_book->checkIn();

            $result = $new_book->getpatrons();
            var_dump($result);

            $this->assertEquals([], $result);
        }
    }
?>
