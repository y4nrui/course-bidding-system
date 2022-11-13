<?php
require_once "../../app/include/common.php";
$section = new Section("IS212", "G1", "Monday", "12:00", "15:00", "Sun Jun", "SR 2-3", 40);
var_dump($section);
echo $section->getCourse()."<br>";
echo $section->getSection()."<br>";
echo $section->getDay()."<br>";
echo $section->getStart()."<br>";
echo $section->getEnd()."<br>";
echo $section->getInstructor()."<br>";
echo $section->getVenue()."<br>";
echo $section->getSize()."<br>";

//$course, $section, $day, $start, $end, $instructor, $venue, $size
?>


