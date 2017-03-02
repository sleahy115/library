<?php
date_default_timezone_set('America/Los_Angeles');

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/Patron.php";
require_once __DIR__ . "/../src/Book.php";

$app = new Silex\Application();

$app['debug'] = true;

$server = 'mysql:host=localhost:8889;dbname=library';
$username = 'root';
$password = 'root';
$DB = new PDO($server, $username, $password);

use Symfony\Component\HttpFoundation\Request;
Request::enableHttpMethodParameterOverride();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
));

$app->get("/", function() use ($app) {
    return $app['twig']->render(
    'index.html.twig'
    );
});

$app->get("/book_list", function() use ($app) {
    // if (Book::getAll() == false){
    //     $books= array();
    // }else {
    //     $books = Book::getAll();
    // }
    return $app['twig']->render(
    '/book_list.html.twig',
    array(
        'books'=>Book::getAll())
    );
});

$app->post("/book_list", function() use ($app) {
    $new_book = new Book($_POST['title'],$_POST['due_date'], $_POST['checkout_date'], $_POST['author']);
    $new_book->save();
    return $app['twig']->render('book_list.html.twig',
    array(
        'books'=>Book::getAll())
    );
});

$app->get("/delete", function() use ($app) {
    Book::deleteAll();
    return $app['twig']->render(
    'index.html.twig',
    array(
        'books'=>Book::getAll())
    );
});

$app->get("/book/{id}", function($id) use ($app) {
    $book = Book::find($id);
    if ($book->getPatrons() != array()){
        $patron = $book->getPatrons();
        $patron_name = $patron[0];
    }
    return $app['twig']->render('book_details.html.twig',
    array(
        'book'=>$book,
        'patrons' => Patron::getAll(),
        'patron' => $patron,
        'patron_name' =>$patron_name->getName())
    );
});

$app->post("/add_patron_to_book/{id}", function($id) use ($app) {
    $book = Book::find($id);
    $patron_id = $_POST['patron_id'];
    $patron = Patron::find($patron_id);
    $book->addPatron($patron);
    return $app['twig']->render(
    'book_list.html.twig',
    array(
        'book' => $book,
        'patrons_by_book' => $book->getPatrons(), 'books'=>Book::getAll())
    );

});

$app->get("/patron_list", function() use ($app) {
    return $app['twig']->render('patron_list.html.twig',
    array(
        'patrons'=>Patron::getAll())
    );
});
$app->delete("/delete_patron/{id}", function($id) use ($app) {
    $patron = Patron::find($id);
    $book = Book::find($id);
    $patron->delete();
    return $app->redirect("/book_list");
});

$app->post("/patron_list", function() use ($app) {
    $new_patron = new Patron($_POST['name']);
    $new_patron->save();
    return $app['twig']->render('patron_list.html.twig',
    array(
        'patrons'=>Patron::getAll())
    );
});

// $app->get("/delete/patron", function() use ($app) {
//     Patron::deleteAll();
//     return $app['twig']->render('index.html.twig',
//     array(
//         'patrons'=>Patron::getAll())
//     );
// });

$app->delete("/check_in/{id}", function($id) use ($app) {
    $book = Book::find($id);
    // $patron = Patron::find($id);
    $book->checkIn();
    var_dump($book);
    return $app['twig']->render(
    'book_list.html.twig',
    array(
        'book' => $book,
        'patrons_by_book' => $book->getPatrons(), 'books'=>Book::getAll())
    );
});

$app->get("/patron/{id}", function($id) use ($app) {
    $patron = Patron::find($id);
    return $app['twig']->render('patron_details.html.twig',
    array(
        'patron'=>$patron,
        'patrons' => Patron::getAll(),
        'books' => Book::getAll())
    );
});

$app->post("/add_book_to_patron/{id}", function($id) use ($app) {
    $patron = Patron::find($id);
    $book_id = $_POST['book_id'];
    $book = Book::find($book_id);
    $patron->addBook($book);
    $books = Book::getAll();
    return $app['twig']->render(
    'patron_list.html.twig',
    array(
        'patron' => $patron,
        'patrons' => Patron::getAll(),
        'books_by_patron' => $patron->getBooks(),
        'books'=>$books)
    );
});

$app->delete("/delete_book/{id}", function($id) use ($app) {
    $book = Book::find($id);
    $patron = Patron::find($id);
    $book->delete();
    return $app->redirect("/patron_list");
});
$app->patch("/update_book/{id}", function($id) use ($app) {
    $book = Book::find($id);
    var_dump($id);
    $book->update($_POST['checkout_date'], $_POST['due_date']);

    return $app->redirect("/book/".$id);
});



return $app;
?>
