<?php
# edit the file included below. the bootstrap logic is there
require_once 'include/protect-admin.php';
require_once 'include/bootstrap.php';
$result = doBootstrap();
?>
<html>

<h1>
    Bootstrap Result
</h1>
<hr>
<?php
$style = '';
$round_start = False;
if (array_key_exists('error', $result)) {
    if ($result['error'] == ['input files not found']) {
        echo "<font color=red><strong>Result: Input files not found</strong></font><hr>";
        $style = 'style="display:none"';
    } else {
        $round_start = True;
    }
} else {
    $round_start = True;
}

if ($round_start) {
    echo "<strong>Result: Round 1 is started successfully</strong><hr>";
}
?>
<table width=73%>
    <tr>
        <td <?= $style ?>>
            <table border='1' width=100% style='text-align:center'>
                <tr>
                    <th colspan=2>Successfully Loaded</th>
                </tr>
                <tr>
                    <th>File</th>
                    <th>Num of records loaded</th>
                </tr>
                <?php
                foreach ($result['num-record-loaded'] as $file) {
                    foreach ($file as $file_name => $num) {
                        echo
                            "<tr>
                            <td>$file_name</td>
                            <td>$num</td>
                        </tr>";
                    }
                } ?>
            </table>
        </td>

        <td width=10 <?= $style ?>></td>
        <td>
            <h3>Reupload Bootstrap File</h3>
            <form id='bootstrap-form' action="process-bootstrap.php" method="post" enctype="multipart/form-data">
                Upload your bootstrap file: &nbsp
                <input id='bootstrap-file' type="file" name="bootstrap-file"><br><br>
                <input type="submit" name="submit" value="Import">
            </form>
            <form action="admin-home.php"><input type="submit" value="Return to home page"></form>
        </td>

    </tr>
</table>
<br>
<?php
if (isset($result['error']) && $result['error'] != ['input files not found']) {
    echo "<table border='1' width = 30% style='text-align:center'>
    <tr>
        <th colspan=3>Failed to Load</th>
    </tr>
    <tr>
        <th>File</th>
        <th>Line</th>
        <th>Error</th>
    </tr>";
    #ksort($result['error']);
    foreach ($result['error'] as $messages) {
        sort($messages['message']);
        $error_msg = join('<br>', ($messages['message']));
        #$err .= $messages['file'] . ":" . $messages['line'] . ':' . $error_msg . '<br>';
        echo
            "<tr>
                <td>{$messages['file']}</td>
                <td>{$messages['line']}</td>
                <td>$error_msg</td>
            </tr>";
    }
    echo "</table>";
}
?>

</html>