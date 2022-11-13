<?php
require_once "../../app/include/common.php";
$course = new Course("IS212", "SIS", "SPM", "Project Management", "25/12/2019", "12:00", "15:00");
var_dump($course);
echo $course->getCourse()."<br>";
echo $course->getSchool()."<br>";
echo $course->getTitle()."<br>";
echo $course->getDescription()."<br>";
echo $course->getExamdate()."<br>";
echo $course->getExamstart()."<br>";
echo $course->getExamend()."<br>";
?>
