<?php
require_once "../../app/include/common.php";
$dao = new CourseCompletedDAO;
$retrieveAll = $dao->retrieveAll();
var_dump($retrieveAll);

$retrieveByStudent = $dao->retrieveByStudent("ben.ng.2009");
var_dump($retrieveByStudent);

$dao->deleteAll();

$new = new CourseCompleted("ben.ng.2009", "IS100");
$add = $dao->add($new);

var_dump($add);
?>