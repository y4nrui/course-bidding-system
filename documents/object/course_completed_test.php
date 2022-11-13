<?php
require_once "../../app/include/common.php";
$course_completed = new CourseCompleted("Ethan Yang", "IS212");
var_dump($course_completed);
echo $course_completed->getUserid()."<br>";
echo $course_completed->getCourse()."<br>";
?>