<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */
    require_once "src/Patron.php";
    require_once "src/Book.php";

    $server = 'mysql:host=localhost:8889;dbname=library_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);
    class patronTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Book::deleteAll();
            Patron::deleteAll();

        }

        function test_save()
        {

            $name = 'Foo';
            $test_patron = new Patron($name);
            $test_patron->save();

            $result = Patron::getAll();

            $this->assertEquals($test_patron, $result[0]);
        }

        function test_getAll()
        {

            $name = 'Tom';
            $test_patron = new Patron($name);
            $test_patron->save();

            $name2 = 'Lilly';
            $test_patron2 = new Patron($name2);
            $test_patron2->save();

            $result = Patron::getAll();

            $this->assertEquals([$test_patron, $test_patron2], $result);
        }

        function test_deleteAll()
        {
            $name = 'Tom';
            $test_patron = new Patron($name);
            $test_patron->save();

            $name2 = 'Lilly';
            $test_patron2 = new Patron($name2);
            $test_patron2->save();

            Patron::deleteAll();
            $result = Patron::getAll();

            $this->assertEquals([], $result);
        }
        function test_delete()
        {
            $name = 'Tom';
            $test_patron = new Patron($name);
            $test_patron->save();

            $name2 = 'Lilly';
            $test_patron2 = new Patron($name2);
            $test_patron2->save();
            $test_patron2->delete();

            $result = patron::getAll();

            $this->assertEquals([$test_patron], $result);
        }

        function test_updateName()
        {
            $name = 'Tom';
            $test_patron = new Patron($name);
            $test_patron->save();

            $new_name = 'Bob';
            $test_patron->updateName($new_name);
            $result = $test_patron->getName();

            $this->assertEquals($new_name, $result);
        }


        function test_find()
        {
            $name = 'Tom';
            $test_patron = new Patron($name);
            $test_patron->save();

            $result = Patron::find($test_patron->getId());

            $this->assertEquals($test_patron, $result);
        }

        function test_addBook()
        {
            $title = 'Eumeswil';
            $due_date = '0000-00-00';
            $checkout_date = '0000-00-00';
            $author = 'Ernst Junger';
            $new_book = new Book($title, $checkout_date, $due_date, $author);
            $new_book->save();

            $name = 'Tom';
            $test_patron = new Patron($name);
            $test_patron->save();

            $test_patron->addBook($new_book);

            $result = $test_patron->getBooks();

            $this->assertEquals([$new_book], $result);
        }

    }
?>
