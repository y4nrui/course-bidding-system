<?php

require_once "../../app/include/common.php";

$dao = new EnrolmentDAO();

#retrieveAll
echo "retrieveAll test case: <br>";
$enrolments = $dao->retrieveAll();
var_dump($enrolments);
echo "<hr>";

#retrieveEnrolmentBySection
echo "retrieveEnrolmentBySection test case: <br>";
$enrolmentBySection = $dao->retrieveEnrolmentBySection('IS100', 'S1');
var_dump($enrolmentBySection);

#retrieveEnrolmentByRound
echo "retrieveEnrolmentByRound test case: <br>";
$enrolmentByRound = $dao->retrieveEnrolmentByRound('1');
var_dump($enrolmentByRound);

#retrieveEnrolmentByStudent
echo "retrieveEnrolmentByStudent test case: <br>";
$enrolmentByStudent = $dao->retrieveEnrolmentByStudent('ben.ng.2009');
var_dump($enrolmentByStudent);

#deleteAll
// echo "deleteAll test case: <br>";
// $status = $dao->deleteAll();
// var_dump($status);

# die; # remove after data is created
#addEnrolment (run it only once)
// echo "addEnrolment test case: <br>";
// $enrolment_arr = [];
// $enrolment_arr[] = new Enrolment('amy.ng.2009', 'IS100', 'S1', '11.52', '1');
// $enrolment_arr[] = new Enrolment('ben.ng.2009', 'IS100', 'S1', '12.52', '2');
// $enrolment_arr[] = new Enrolment('calvin.ng.2009', 'IS100', 'S1', '13.52', '3');

// foreach($enrolment_arr as $obj){
//     $dao->addEnrolment($obj);
// }


?>