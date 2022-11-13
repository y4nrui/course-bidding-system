<?php
require_once "../../app/include/common.php";
$student = new student("Ethan Yang", "abcd", "Ethan Yuzhe Yang", "SIS", 100.0);
var_dump($student);
echo $student->getUserid()."<br>";
echo $student->getPassword()."<br>";
echo $student->getName()."<br>";
echo $student->getSchool()."<br>";
echo $student->getEdollar()."<br>";
echo $student->setEdollar(98.0)."<br>";
echo $student->getEdollar();

//($userid, $passwordHashed, $E, $sch, $edollar)
?>
