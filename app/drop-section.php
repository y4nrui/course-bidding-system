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
    <title>Drop A Section</title>
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
    <h1>Drop Enrolled Sections</h1>
    <h3>Current Round: Round <?php echo $round . ' - ' . $status ?></h3>
    <h3>Current edollar balance: $<?php echo $student->getEdollar(); ?></h3>
    <?php
    $style = 'style="width:40%;display:inline-table"';
    $hidden = '';
    if ($round_info[1] != 1) {
        echo "<h3><font style='color:red'>You cannot drop sections now as there is no active round</font></h3>";
        $style = "style='display:none'";
    }
    ?>
    <hr style='border:1px solid grey'>
    <div id='table' width='100%'>
        <div <?= $style ?>>
            <table border=1 id='drop-section table'>
                <tr align='center'>
                    <td colspan=5> <strong>Enrolled Sections</strong> </td>
                </tr>
                <tr>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Bidded Amount</th>
                    <th>Select to Drop</th>
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

                    /**  as we want to drop bids of the selected row, we need to submit all the information
                     * through the form
                     * there will be 4 arrays submitted and since the data in the same row will have the same index
                     * in different arrays, we will use that to know what bids the user wanna drop
                     */
                    echo "<tr align='center'>
            <td><input id='course_id' name='course[$id]' value = $course hidden>$course</td>
            <td><input id='section_id' name='section[$id]' value = $section hidden>$section</td>
            <td><input id='amount_id' name='amount[$id]' value = $amount hidden>$amount</td>
            <td><input type = 'checkbox' name = 'enrolments_to_drop[$id]'></td>
            </tr>";

                    $id++;
                }

                ?>

            </table>
            <br>
            <input type='submit' value='Drop Enrolled Sections'>
            </form>
            <?php
            printErrors();
            if (isset($_SESSION['action_outcome'])) {
                echo "<br><br>";
                echo $_SESSION['action_outcome'];
                echo "Your current balance is $" . $student->getEdollar();
                unset($_SESSION['action_outcome']);
            }
            ?>
        </div>
        <div <?= $style ?>>
            <div style="margin: 0 0 0 0;text-align:center">
                <h3>Lesson Timetable of Enrolled Courses </h3>
            </div>
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
                <h3>Exam Time of Enrolled Courses</h3>
            </div>
            <div id='exam table'>
                <table border=1>
                    <tr align='center'>
                        <th width='145px'>Course</th>
                        <th width='145px'>Exam Date</th>
                        <th width='145px'>Weekday</th>
                        <th width='145px'>Exam Start</th>
                        <th width='145px'>Exam End</th>
                    </tr>
                    <?php
                    // $dayofweek = date('w', strtotime($date));
                    $courseDao = new CourseDAO();
                    foreach ($enrolments as $enrolment) {
                        $course_code = $enrolment->getCourse();
                        $course = $courseDao->retrieveCourse($course_code);
                        $exam_date = $course->getExamDate();
                        $weekday = date('w', strtotime($exam_date));
                        echo "
                    <tr align='center'> 
                        <td>$course_code</td>
                        <td>$exam_date</td>
                        <td>$weekDay</td>
                        <td>{$course->getExamStart()}</td>
                        <td>{$course->getExamEnd()}</td>
                    </tr>
                ";
                    }
                    ?>
                </table>

            </div>
        </div>
    </div>
</body>

</html>