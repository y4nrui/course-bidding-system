<?php
require_once "../../app/include/common.php";
$enrolment = new enrolment("Ethan Yang", "IS212", "G1", 100, "2");
var_dump($enrolment);
echo $enrolment->getUserid()."<br>";
echo $enrolment->getCourse()."<br>";
echo $enrolment->getSection()."<br>";
echo $enrolment->getAmount()."<br>";
echo $enrolment->getRound()."<br>"
?>
