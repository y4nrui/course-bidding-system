<?php
require_once 'include/common.php';
require_once 'include/bid-section.php';
require_once 'include/update-bid.php';
require_once 'include/delete-bid.php';
require_once 'include/drop-section.php';
require_once 'include/round-clear.php';

$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwiZGF0ZXRpbWUiOiIyMDE5LTEwLTAyIDA1OjQxOjMwIn0.wLzLkYRwnT9zqPiR0eqgolVfcqAYBFtaJWJd9fGd3EY';

$url = 'localhost/spm/json/';

$func = 'bid-status';

$input = [
    'course' => 'IS100',
    'section' => 'S2'
];

$encoded_input  = json_encode($input);

$bidDAO = new BidDAO();
$bids = $bidDAO->retrieveAll();

// var_dump($bids);
// exit;

// $enrolmentDAO = new EnrolmentDAO();
// $enrolments = $enrolmentDAO->retrieveAll();

// $courseCompletedDAO = new CourseCompletedDAO();
// $coursesCompleted = $courseCompletedDAO->retrieveAll();

// $sectionDAO = new SectionDAO();
// $sections = $sectionDAO->retrieveAll();

// $studentDAO = new StudentDAO();
// $students = $studentDAO->retrieveAll();

// $sortClass = new Sort();
// $students = $sortClass->sort_it($students, 'userid'); 
// var_dump($students);
echo $url . $func . '?r=' . $encoded_input . "&" . "token=$token";

// var_dump($_GET['r']);

// var_dump(json_decode($_GET['r'], True));

// bidSection('amy.ng.2009',8,'IS100','S1');
