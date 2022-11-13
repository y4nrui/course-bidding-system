<?php
require_once "../../app/include/common.php";
$bid = new Bid("Ethan Yang", "100", "IS212", "G1");
var_dump($bid);
echo $bid->getUserid()."<br>";
echo $bid->getAmount()."<br>";
echo $bid->getCourse()."<br>";
echo $bid->getSection()."<br>";
?>
