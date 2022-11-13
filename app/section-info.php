<?php
require_once 'include/common.php';
require_once 'include/protect-student.php';

$sectionDao = new SectionDAO();
$courseDao = new CourseDAO();
$adminDao = new AdminDAO();
$conditions = [];
if (isset($_GET['search-result'])) {
    unset($_GET['search-result']);
    $result = 'Section filters: ';

    if (!isEmpty(array_values($_GET))) {
        foreach ($_GET as $field => $value) {
            if (!empty($value)) {
                if ($field == 'day') {
                    $result .= "$field = " . getWeekday($value);
                } else {
                    $result .= "$field = $value";
                }
                $result .= ' & ';
                $conditions[$field] = $value;
            }
        }
        $result = substr($result, 0, strlen($result) - 2);
    }
}
$sections = $adminDao->filterSectionInformation($conditions);

?>
<html>

<head>
    <title>Section Information List</title>
</head>

<body>
    <p><a href='student-home.php'>Return to home page</a></p>
    <hr style='border:1px solid grey'>
    <h1 style='text-align:center'>Section Information List</h1>
    <hr style='border:1px solid grey'>
    <form action="section-info.php" method="get" align='center'>
        Course: <input type="text" name="course" id=""> |
        Section: <input type="text" name="section" id=""> |
        School: <input type="text" name="school" id=""> |
        Weekday: <select name="day">
            <option disabled selected value> -- select an option -- </option>
            <?php
            for ($i = 1; $i <= 7; $i++) {
                $wkday = getWeekday($i);
                echo "<option value=$i>$wkday</option>";
            }
            ?>
        </select>
        <br>
        <br>
        Lesson Start Time (HH:MM): <input type="text" name="start" id="" size=10> |
        Instructor: <input type="text" name="instructor" id=""> |
        <input type="submit" value="Seach Sections" name='search-result'>
        <input type="reset" value="Clear filter">
    </form>
    <?php
    if (!empty($result)) {
        echo "<div style='text-align:center'><strong>$result</strong></div>";
    }
    ?>
    <hr style='border:1px solid grey'>
    <table border=1 align='center' width='80%'>
        <tr style="font-size:20px;">
            <th>Course</th>
            <th>Section</th>
            <th>School</th>
            <th>Lesson Day & Time</th>
            <th>Exam Date & Time</th>
            <th>Vacancy</th>
            <th>Minimum Bid</th>
            <th>Instructor</th>
        </tr>
        <?php
        $alt = True;
        foreach ($sections as $section) {
            $course_code = $section->getCourse();
            $section_id = $section->getSection();
            $lesson_time =  getWeekday($section->getDay()) . " <br> " . $section->getStart() . " - " . $section->getEnd();

            $course = $courseDao->retrieveCourse($course_code);
            $school = $course->getSchool();
            $exam_weekday = getWeekday(date('w', strtotime($course->getExamDate())));
            $exam_time = $course->getExamDate() . ", $exam_weekday" . " <br> " . $course->getExamStart() . " - " . $course->getExamEnd();

            $vacancy = $adminDao->getVacancy($course_code, $section_id);
            $min_bid = $adminDao->getMinBid($course_code, $section_id);

            $instructor = $section->getInstructor();

            if ($alt) {
                $bg = '#E6E1E1 ';
            } else {
                $bg = '';
            }
            $alt = !$alt;

            echo "
            <tr style='text-align:center' bgcolor=$bg>
                <td>$course_code</td>
                <td>$section_id</td>
                <td>$school</td>
                <td>$lesson_time</td>
                <td>$exam_time</td>
                <td>$vacancy</td>
                <td>$min_bid</td>
                <td>$instructor</td>
            </tr>";
        }
        ?>

    </table>



</body>

</html>