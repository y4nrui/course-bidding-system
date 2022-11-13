<?php

require_once '../../app/include/common.php';

$dao = new BidDAO();

echo "Retrieve all <br>";
$all = $dao->retrieveAll();
var_dump($all);
echo "<hr>";

echo "Retrieve by Section <br>";
$indiv = $dao->retrieveBySection('IS100', 'S1');
var_dump($indiv);
echo "<hr>";

echo "Retrieve by Student <br>";
$indiv = $dao->retrieveByStudent('amy.ng.2009');
var_dump($indiv);
echo "<hr>";

echo "Retrieve by Student Bid <br>";
$indiv = $dao->retrieveBid('amy.ng.2009', 'IS100');
var_dump($indiv);
echo "<hr>";

echo "delete all<br> ";
$dao->deleteAll();
echo "<hr>";

echo "add <br> ";
$new = new Bid('amy.ng.2009', 123, 'IS100', 'S1');
$dao->add($new);
echo "<hr>";

echo "update <br> ";
$dao->updateBid('amy.ng.2009', 'IS100', 100);
echo "<hr>";
