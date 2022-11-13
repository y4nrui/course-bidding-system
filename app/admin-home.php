<?php

require_once 'include/common.php';
require_once 'include/protect-admin.php';

$adminDao = new AdminDAO();
$round_info = $adminDao->getRound();
if ($round_info[1] == '1') {
	$status = "Active";
} elseif ($round_info[1] == 0) {
	$status = "Ended";
} else {
	$status = 'Not started';
}
?>

<html>

<head>
	<title>Admin Home</title>
</head>

<h1> Welcome Admin</h1>
<hr>

<div id='heading' style='width:100%;'>
	<div id='bootstrap heading' style='width:40%;display:inline-table'>
		<h3>Bootstrap</h3>
	</div>
	<div id='round-control heading' style='width:40%;display:inline-table'>
		<h3>Round Control</h3>
	</div>
	<div id='logout' style='position:absolute;right:10px;display:inline-table'>
		<form action="logout.php" method="GET">
			<input type='submit' value='Logout'>
		</form>
	</div>
</div>

<div id='functions' width=100%>
	<div id='bootstrap' style='width:40%;display:inline-table'>
		<form id='bootstrap-form' action="process-bootstrap.php" method="post" enctype="multipart/form-data">
			Upload your bootstrap file: <br><br>
			<input id='bootstrap-file' type="file" name="bootstrap-file"><br><br>
			<input type="submit" name="submit" value="Import">
		</form>
	</div>

	<div id='round-control' style='width:40%;display:inline-table'>
		<?php
		echo "<h4>Round {$round_info[0]} - $status</h4>"
		?>
		<form action="process-round-control.php">
			<input type="submit" name='round-control' value="Start round">
			<input type="submit" name='round-control' value='End round'>
		</form>
		<?php
		if (isset($_SESSION['action-outcome'])) {
			echo "<br>";
			echo $_SESSION['action-outcome'];
			unset($_SESSION['action-outcome']);
		}
		printErrors();
		?>
	</div>
</div>
</html>