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
    return $app['twig']->render('index.html.twig');
});

$app->get("/book_list", function() use ($app) {
    // if (Book::getAll() == false){
    //     $books= array();
    // }else {
    //     $books = Book::getAll();
    // }
    return $app['twig']->render('book_list.html.twig', array('books'=>Book::getAll()));
});
$app->post("/book_list", function() use ($app) {
    $new_book = new Book($_POST['title'],$_POST['due_date'], $_POST['checkout_date'], $_POST['author']);
    $new_book->save();
    return $app['twig']->render('book_list.html.twig', array('books'=>Book::getAll()));
});
$app->get("/delete", function() use ($app) {
    Book::deleteAll();
    return $app['twig']->render('index.html.twig', array('books'=>Book::getAll()));
});
$app->get("/book/{id}", function($id) use ($app) {
    $book =Book::find($id);
    return $app['twig']->render('book_details.html.twig', array('book'=>$book, 'patrons' => Patron::getAll()));
});
$app->get("/patron_list", function() use ($app) {
    return $app['twig']->render('patron_list.html.twig', array('patrons'=>Patron::getAll()));
});
$app->post("/patron_list", function() use ($app) {
    $new_patron = new Patron($_POST['name']);
    $new_patron->save();
    return $app['twig']->render('patron_list.html.twig', array('patrons'=>Patron::getAll()));
});
$app->get("/delete/patron", function() use ($app) {
    Patron::deleteAll();
    return $app['twig']->render('index.html.twig', array('patrons'=>Patron::getAll()));
});
$app->get("/patron/{id}", function($id) use ($app) {
    $patron =Patron::find($id);
    return $app['twig']->render('patron_details.html.twig', array('patron'=>$patron, 'patrons' => Patron::getAll()));
});




return $app;
?>
