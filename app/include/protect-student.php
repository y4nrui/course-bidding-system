<?php

require_once 'common.php';

if (!isset($_SESSION['username'])) {
	$_SESSION['errors'] = ["You have not logged in."];
	header("Location: login.php"); # it's included in other file, so the dir is relative to the main (/pokemon) dir
	exit;
} elseif ($_SESSION['username'] == 'admin') {
	$_SESSION['errors'] = ["The page you wish to access is only accessible by students."];
	header("Location: admin-home.php");
	exit;
}
