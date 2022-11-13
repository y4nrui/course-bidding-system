<?php

require_once '../../app/include/common.php';

$dao = new AdminDAO();

echo "get password<br>";
$all = $dao->getPassword();
var_dump($all);
echo "<hr>";


echo "update round <br>";
$dao->setRound(1, 1);
echo "<hr>";


echo "get round <br>";
var_dump($dao->getRound());
echo "<hr>";
