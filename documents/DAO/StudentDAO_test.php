<?php

require_once '../../app/include/common.php';

$dao = new StudentDAO();

echo "Retrieve all <br>";
$all = $dao->retrievetAll();
var_dump($all);
echo "<hr>";

echo "Retrieve Student <br> ";
$stu = $dao->retrieveStudent('amy.ng.2009');
var_dump($stu);
echo "<hr>";

echo "delete Student <br> ";
$dao->deleteAll();
echo "<hr>";

echo "add Student <br> ";
$new = new Student('test1.2019', 'asdfsafas', 'test1', 'smu', 100);
$dao->add($new);
echo "<hr>";

echo "update Student <br> ";
$new = new Student('test2.2019', 'asdfsafas', 'test2', 'smu', 100);
$dao->add($new);
$dao->updateEdollar('test2.2019', 50);
echo "<hr>";
