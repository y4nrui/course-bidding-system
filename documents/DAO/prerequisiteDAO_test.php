<?php
require_once "../../app/include/common.php";
$dao = new PrerequisiteDAO();
$pre = $dao->retrieveAll();
var_dump($pre);

$retrievebyCourse = $dao->retrievebyCourse("IS101", "IS100");
var_dump($retrievebyCourse);

$dao->deleteAll();

$new = new Prerequisite("IS101", "IS100");
$add = $dao->add($new);
var_dump($add)
?>

