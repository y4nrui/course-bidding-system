<?php

require_once "../../app/include/common.php";

$dao = new CourseDAO();

$array = $dao->retrieveAll();
var_dump($array);
echo "<hr>";

$myCourse = 'IS102';
$course = $dao->retrieveCourse($myCourse);
var_dump($course);
echo "<hr>";

$del_status = $dao->deleteAll();
var_dump($del_status);
echo "<hr>";

$course_obj = new Course('IS212', 'SIS', 'Software Project Management', 
'Learn acting', '2019-12-25', '18:00', '21:00');
$add = $dao->add($course_obj);
var_dump($add);
?>