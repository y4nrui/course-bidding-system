<?php
require_once "../../app/include/common.php";
$prerequisite = new prerequisite("IS212", "IS113");
var_dump($prerequisite);
echo $prerequisite->getCourse()."<br>";
echo $prerequisite->getPrerequisite()."<br>";
?>