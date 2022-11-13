<?php
require_once "include/common.php";
require_once "include/protect-student.php";

$studentDao = new StudentDAO();
$student = $studentDao->retrieveStudent($_SESSION['username']);

$adminDao = new AdminDAO();
$round_info = $adminDao->getRound(); # round, status: 1/2, 1/0/-1
$round = $round_info[0];

if ($round_info[1] == '1') {
    $status = "Active";
} elseif ($round_info[1] == '0') {
    $status = "Ended";
} else {
    $status = "Not started";
}
?>

<html>

<head>
    <title>Bid A Section</title>
</head>

<body>

    <div id='heading functions' width='100%'>
        <div id='return-home' style="width:20%;display: inline-table">
            <form action="student-home.php" method="POST">
                <input type="submit" value='Return Home Page'>
            </form>
        </div>
        <div id='bid-section' style="width:15%;display: inline-table">
            <form action="bid-section.php" method="POST">
                <input type="submit" value='Bid Section'>
            </form>
        </div>
        <div id='update-drop-bid' style="width:20%;display: inline-table">
            <form action="update-drop-bid.php" method="POST">
                <input type="submit" value='Update/Drop Bid'>
            </form>
        </div>
        <div id='drop-section' style="width:15%;display: inline-table">
            <form action="drop-section.php" method="POST">
                <input type="submit" value='Drop Section'>
            </form>
        </div>
        <div id='view-section' style="width:20%;display: inline-table">
            <form target='_blank' action="section-info.php" method="POST">
                <input type="submit" value="View Available Section Information">
            </form>
        </div>
        <div id='logout' style='position:absolute;right:10px;display: inline-table'>
            <form action="logout.php" method="POST">
                <input type='submit' value='Logout'>
            </form>
        </div>
    </div>
    <hr style='margin:0 0 0 0;border:1px solid grey'>
    <h1>Bid A Section</h1>
    <h3>Current Round: Round <?php echo $round . ' - ' . $status ?></h3>
    <h3>Current edollar balance: $<?php echo $student->getEdollar(); ?></h3>
    <?php
    $style = "style='display:inline-table;width:35%'";
    $hidden = '';
    if ($round_info[1] != 1) {
        echo "<h3><font style='color:red'>You cannot bid sections now as there is no active round</font></h3>";
        $style = "style='display:none'";
    }
    ?>
    <hr style='border:1px solid grey'>

    <div id='table' width='100%'>
        <div <?= $style ?>>
            <table id='bidding table'>
                <form action="process-bid-section.php" method="get">
                    <tr>
                        <td>Enter Course Code:</td>
                        <td><input type="text" name="course_code" placeholder="E.g. IS111">
                    </tr>
                    <tr>
                        <td>Enter Section Id:</td>
                        <td><input type="text" name="section_id" placeholder="E.g. S1">
                    </tr>
                    <tr>
                        <td>Enter bidding amount:</td>
                        <td><input type="text" name="amount" placeholder="E.g. 24.9">
                    </tr>
                    <tr>
                        <td>
                            <br>
                            <input type="submit" value="Submit">
                        <td>
                    </tr>
                </form>
            </table>
        </div>
        <div id='bid table' <?= $style ?>>
            <table border=1 width='100%'>
                <tr align='center'>
                    <?php
                    $colspan = 4;
                    $heading = 'Bidded Sections';
                    if ($round_info[1] == 0) {
                        $heading = "Bidding Results";
                    }

                    if ($round_info[0] == 2) {
                        $colspan = 6;
                    }

                    echo "<td colspan=$colspan><strong>$heading</strong> </td>"
                    ?>
                </tr>
                <tr>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Amount</th>
                    <?php
                    if ($round_info[0] == 2) {
                        echo "<th>Minimum Bid</th>
                    <th>Vacancy</th>";
                    }
                    ?>
                    <th>Result</th>

                </tr>
                <?php
                $bidDao = new BidDAO();
                $bids = $bidDao->retrieveByStudent($_SESSION['username']);

                foreach ($bids as $bid) {

                    $course = $bid->getCourse();
                    $section = $bid->getSection();
                    $amount = $bid->getAmount();
                    $result = $bid->getResult();
                    $vacancy = $adminDao->getVacancy($course, $section);
                    $min_bid = $adminDao->getMinBid($course, $section);
                    $bgcolor = '';

                    if ($result == "Success") {
                        $bgcolor = '#42FF33';
                    } elseif ($result == 'Fail') {
                        $bgcolor = '#FA1515';
                    }

                    echo "<tr align='center'>
                        <td>$course</td>
                        <td>$section</td>
                        <td>$$amount</td>";
                    if ($round_info[0] == 2) {
                        echo "  <td>$min_bid</td>
                            <td>$vacancy</td>";
                    }
                    echo "<td bgcolor='$bgcolor'>$result</td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <?php
    printErrors();
    ?>

</body>

</html>