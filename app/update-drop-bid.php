<?php
require_once "include/common.php";
require_once "include/protect-student.php";

$studentDao = new StudentDAO();
$student = $studentDao->retrieveStudent($_SESSION['username']);

$adminDao = new AdminDAO();
$round_info = $adminDao->getRound(); # round, status: 1/2, 1/0/-1
$round = $round_info[0];

if ($round_info[1] == 1) {
    $status = "Active";
} elseif ($round_info[1] == 0) {
    $status = "Ended";
} else {
    $status = "Not started";
}
?>
<html>

<head>
    <title>Update & Drop Bids</title>
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
    <h1>Update & Drop Bids</h1>
    <h3>Current Round: Round <?php echo $round . ' - ' . $status ?></h3>
    <h3>Current edollar balance: $<?php echo $student->getEdollar(); ?></h3>
    <?php
    $style = 'style="width:40%;display:inline-table"';
    $hidden = '';
    if ($round_info[1] != 1) {
        echo "<h3><font style='color:red'>You cannot update/drop bids now as there is no active round</font></h3>";
        $style = "style='display:none'";
    } else {
        echo "Note: If you select to drop a bid and update its amount concurrently, the bid will be dropped";
    }
    ?>
    <hr style='border:1px solid grey'>
    <div id='table' width='100%'>
        <div <?= $style ?>>
            <table border=1 id='update-drop-bid table'>
                <tr align='center'>
                    <td colspan=5> <strong>Bidded Sections</strong> </td>
                </tr>
                <tr>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Original Amount</th>
                    <th>Updated Amount</th>
                    <th>Select to Drop</th>
                </tr>
                <?php
                $bidDao = new BidDAO();
                $bids = $bidDao->retrieveByStudent($_SESSION['username']);
                echo "<form action = 'process-update-drop-bid.php' method = 'get'>";
                $id = 0;
                foreach ($bids as $bid) {
                    if ($round_info[1] == 1) {
                        $course = $bid->getCourse();
                        $section = $bid->getSection();
                        $amount = $bid->getAmount();

                        /**  as we want to drop bids of the selected row, we need to submit all the information
                         * through the form
                         * there will be 4 arrays submitted and since the data in the same row will have the same index
                         * in different arrays, we will use that to know what bids the user wanna drop
                         */
                        echo "<tr align='center'>
            <td><input id='course_id' name='course[$id]' value = $course type='hidden'>$course</td>
            <td><input id='section_id' name='section[$id]' value = $section type='hidden'> $section</td>
            <td><input id='amount_id' name='amount[$id]' value = $amount type='hidden'>$amount</td>
            <td><input id='updated_amount_id' name='updated_amount[$id]' value='' style='width:100%'></td>
            <td align='center'><input type = 'checkbox' name = 'bids_to_drop[$id]'></td>
            </tr>";
                        $id++;
                    }
                }
                ?>

            </table>
            <br>
            <input type='submit' value='Update & Drop Bids' <?= $hidden ?>>
            </form>
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

                $add_row = True;
                $finalsed = False;

                foreach ($bids as $bid) {

                    $course = $bid->getCourse();
                    $section = $bid->getSection();
                    $amount = $bid->getAmount();
                    $result = $bid->getResult();
                    $vacancy = $adminDao->getVacancy($course, $section);
                    $min_bid = $adminDao->getMinBid($course, $section);
                    $bgcolor = '';

                    if ($result == "Success") {
                        $bgcolor = '#06bf41';
                    } elseif ($result == 'Fail') {
                        $bgcolor = '#eb1010';
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
    if (isset($_SESSION['action_outcome'])) {
        echo "<br><br>";
        echo $_SESSION['action_outcome'];
        echo "Your current balance is $" . $student->getEdollar();
        unset($_SESSION['action_outcome']);
    }
    ?>
</body>

</html>