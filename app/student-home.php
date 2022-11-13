<?php
require_once "include/common.php";
require_once "include/protect-student.php";

$studentDao = new StudentDAO();
$student = $studentDao->retrieveStudent($_SESSION['username']);
$name = $student->getName();
$edollar = $student->getEdollar();

$adminDao = new AdminDAO();
$round_info = $adminDao->getRound();

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
    <title>Student Home</title>
</head>

<body>

    <h1>Welcome <?php echo $name ?>!</h1>
    <hr>
    <div id='functions' width='100%'>
        <div id='bid-section' style="width:20%;display: inline-table">
            <form action="bid-section.php" method="POST">
                <input type="submit" value='Bid Section'>
            </form>
        </div>
        <div id='update-drop-bid' style="width:20%;display: inline-table">
            <form action="update-drop-bid.php" method="POST">
                <input type="submit" value='Update/Drop Bid'>
            </form>
        </div>
        <div id='drop-section' style="width:20%;display: inline-table">
            <form action="drop-section.php" method="POST">
                <input type="submit" value='Drop Section'>
            </form>
        </div>
        <div id='view-section' style="width:10%;display: inline-table">
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
    <hr style='border:1px solid grey'>
    <div id='info-heading' width='100%'>
        <div id='e$-balance' style='width:20%;margin:0 10px 0 0px;display: inline-table'>
            <h3>Your edollar balance: $ <?= $edollar ?></h3>
        </div>

        <div id='round-info' style='width:20%;margin:0 10px 0 0px;display: inline-table'>
            <h3>Current Round: <?php echo $round_info[0] . " - " . $status ?></h3>
        </div>
        <div id='enrolment heading' style='width:50%;margin:0 0 0 60px;display: inline-table;text-align:center'>
            <h3>Lesson Information of Enrolled Courses </h3>
        </div>
    </div>

    <div id='table' width='100%'>
        <div style='width:40%;margin:0 10px 0 0px;display: inline-table'>
            <div id='bid table'>
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
            <p></p>
            <?php
            $style = '';
            if ($round_info[0] == 1 && ($round_info[1] == 1 || $round_info[1] == -1)) {
                $style = "style='display:none'";
            }
            ?>
            <div id='enrolled bidding results' <?= $style ?>>
                <table border=1 width='100%'>
                    <tr align='center'>
                        <td colspan=5> <strong>Successfully Bidded Courses</strong> </td>
                    </tr>
                    <tr>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Bidded Amount</th>
                    </tr>
                    <?php
                    $enrolmentDao = new EnrolmentDAO();
                    $enrolments = $enrolmentDao->retrieveEnrolmentByStudent($_SESSION['username']);
                    echo "<form action = 'process-drop-section.php' method = 'get'>";
                    $id = 0;
                    foreach ($enrolments as $enrolment) {

                        $course = $enrolment->getCourse();
                        $section = $enrolment->getSection();
                        $amount = $enrolment->getAmount();

                        echo "<tr align='center'>
                            <td>$course</td>
                            <td>$section</td>
                            <td>$amount</td>
                            
                            </tr>";

                        $id++;
                    }

                    ?>

                </table>
            </div>
            <?php
            if (isset($_SESSION['action_outcome'])) {
                echo "<br>";
                echo $_SESSION['action_outcome'];
                unset($_SESSION['action_outcome']);
            }
            printErrors();
            ?>
        </div>
        <div style='width:55%;margin:0 0 0 40px; display: inline-table'>
            <div id='enrolment table'>
                <table border=1 width='100%'>
                    <tr>
                        <th>Weekday</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Lesson Start</th>
                        <th>Lesson End</th>
                        <th>Venue</th>
                    </tr>
                    <?php
                    $enrolmentDao = new EnrolmentDAO();
                    $enrolments = $enrolmentDao->retrieveEnrolmentByStudent($_SESSION['username']);
                    $sectionDao = new SectionDAO();
                    $sortClass = new Sort();
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

                    foreach ($days as $weekDay) {
                        $same_day_enrolments = [];

                        foreach ($enrolments as $enrolment) { # find all enrolment in the same day
                            $section = $sectionDao->retrieveSection($enrolment->getCourse(), $enrolment->getSection());
                            if ($weekDay == getWeekday($section->getDay())) {
                                $same_day_enrolments[] = $section;
                            }
                        }

                        $print_weekday = False;
                        $rowspan = count($same_day_enrolments);
                        $same_day_enrolments = $sortClass->sort_it($same_day_enrolments, 'lesson_start');

                        foreach ($same_day_enrolments as $section) {
                            $start_time = substr($section->getStart(), 0, strpos($section->getStart(), ":", 4));
                            echo "<tr align=center>";
                            if (!$print_weekday) { # havent printed weekday cell 
                                echo "<td rowspan=$rowspan>{$weekDay}</td>";
                                $print_weekday = True;
                            }
                            echo " <td>{$section->getCourse()}</td>
                                    <td>{$section->getSection()}</td>
                                    <td>{$section->getStart()}</td>
                                    <td>{$section->getEnd()}</td>
                                    <td>{$section->getVenue()}</td></tr>";
                        }
                    }

                    echo "</tr>";
                    ?>
                </table>
            </div>
            <div style="margin: 10px 0 0 0;text-align:center">
                <h3>Exam Information of Enrolled Courses </h3>
            </div>
            <div id='exam table'>
                <table border=1 width='100%'>
                    <tr align='center'>
                        <th width='145px'>Course</th>
                        <th width='145px'>Exam Date</th>
                        <th width='145px'>Weekday</th>
                        <th width='145px'>Exam Start</th>
                        <th width='145px'>Exam End</th>
                    </tr>
                    <?php
                    $courseDao = new CourseDAO();
                    $courses_enrolled = [];
                    foreach ($enrolments as $enrolment) {
                        $course_code = $enrolment->getCourse();
                        $courses_enrolled[] = $courseDao->retrieveCourse($course_code);
                    }

                    $courses_enrolled = $sortClass->sort_it($courses_enrolled, 'exam_start');

                    foreach ($courses_enrolled as $course) {
                        $exam_date = $course->getExamDate();
                        $weekday = getWeekday(date('w', strtotime($exam_date)));
                        echo "
                        <tr align='center'> 
                            <td>{$course->getCourse()}</td>
                            <td>$exam_date</td>
                            <td>$weekday</td>
                            <td>{$course->getExamStart()}</td>
                            <td>{$course->getExamEnd()}</td>
                        </tr>";
                    }
                    ?>
                </table>

            </div>
        </div>
    </div>
</body>

</html>