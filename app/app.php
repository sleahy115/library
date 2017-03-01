<?php
date_default_timezone_set('America/Los_Angeles');

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../src/Student.php";
require_once __DIR__ . "/../src/Course.php";

$app = new Silex\Application();

$app['debug'] = true;

$server = 'mysql:host=localhost:8889;dbname=school';
$username = 'root';
$password = 'root';
$DB = new PDO($server, $username, $password);

use Symfony\Component\HttpFoundation\Request;
Request::enableHttpMethodParameterOverride();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
));

$app->get("/", function() use ($app) {
    return $app['twig']->render('homepage.html.twig');
});

$app->get("/students", function() use ($app) {
    return $app['twig']->render('students.html.twig', array('students' => Student::getAll()));
});

$app->get("/update_student/{id}", function($id) use ($app) {
    $student = Student::find($id);
    return $app['twig']->render('edit_student.html.twig', array('student' => $student));
});
$app->get("/update_course/{id}", function($id) use ($app) {
    $course = Course::find($id);
    return $app['twig']->render('edit_course.html.twig', array('course' => $course, 'students_in_class' => $course->getStudents(), 'all_students' => Student::getAll()));
});
$app->get("/courses", function() use ($app) {
    return $app['twig']->render('courses.html.twig', array('courses' => Course::getAll()));
});

$app->post("/add_class", function() use ($app) {
    $id = null;
    $name = $_POST['name'];
    $course_number = $_POST['course_number'];
    $new_course = new Course($id, $name, $course_number);
    $new_course->save();
    return $app['twig']->render('courses.html.twig', array('courses' => Course::getAll()));
});
$app->post("/add_student", function() use ($app) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $enrolment_date = $_POST['enrolment_date'];
    $new_student = new Student(null, $first_name, $last_name, $enrolment_date);
    $new_student->save();
    return $app['twig']->render('students.html.twig', array('students' => Student::getAll()));
});
$app->delete("/delete_student/{id}", function($id) use ($app) {
    $student = Student::find($id);
    $student->delete();
    return $app['twig']->render('students.html.twig', array('students' => Student::getAll()));
});
$app->delete("/delete_course/{id}", function($id) use ($app) {
    $course = Course::find($id);
    $course->delete();
    return $app['twig']->render('courses.html.twig', array('courses' => Course::getAll()));
});
$app->delete("/delete_all_students", function() use ($app) {
    Student::deleteAll();
    return $app['twig']->render('students.html.twig', array('students' => Student::getAll()));
});


$app->patch("/student_editor/{id}", function($id) use ($app) {
    $student = Student::find($id);
    $student->updateFirstName($_POST['first_name']);
    $student->updateLastName($_POST['last_name']);
    $student->updateEnrolmentDate($_POST['enrolment_date']);
    return $app['twig']->render('students.html.twig', array('students' => Student::getAll()));
});
$app->patch("/course_editor/{id}", function($id) use ($app) {
    $course = Course::find($id);
    $course->updateName($_POST['name']);
    $course->updateCourseNumber($_POST['course_number']);
    return $app['twig']->render('courses.html.twig', array('courses' => Course::getAll()));
});

$app->post("/add_student_to_course/{id}", function($id) use ($app) {
    $course = Course::find($id);
    $student_id = $_POST['students'];
    $student = Student::find($student_id);
    $course->addStudent($student);

    return $app['twig']->render('edit_course.html.twig', array('course' => $course, 'students_in_class' => $course->getStudents(), 'all_students' => Student::getAll()));
});


return $app;
?>
