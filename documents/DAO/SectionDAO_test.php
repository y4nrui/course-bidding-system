<?php

require_once '../../app/include/common.php';

$dao = new SectionDAO();

echo "Retrieve all <br>";
$all = $dao->retrieveAll();
var_dump($all);
echo "<hr>";

echo "Retrieve Section <br>";
$indiv = $dao->retrieveSection('IS100', 'S1');
var_dump($indiv);
echo "<hr>";

echo "delete all<br> ";
$dao->deleteAll();
echo "<hr>";

echo "add <br> ";
$new = new Section('IS100', 'S1', '1', '12:00', '15:00', 'rajesh', 'sis sr2-3', 50);
$dao->add($new);
echo "<hr>";
