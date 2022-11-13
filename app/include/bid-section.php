<?php

require_once "validation.php";
require_once "common.php";
require_once 'round-clear.php';
/**
 * This functions is to bid a section of a course with the given amount
 * Assumes the parameters have passed common and input validity checks
 * 
 * @param string $userid 
 * @param float  $amount
 * @param string $course_code
 * @param string $section_id
 * 
 * @return array $result, with errors if there is any
 */
function bidSection($userid, $amount, $course_code, $section_id)
{
    $errors = [];

    $studentDao = new StudentDAO();
    $student = $studentDao->retrieveStudent($userid);
    $currentbalance = $student->getEdollar();

    $courseDao = new CourseDAO();
    $course = $courseDao->retrieveCourse($course_code);

    $sectionDao = new SectionDAO();
    $section = $sectionDao->retrieveSection($course_code, $section_id);

    $enrolmentDao = new EnrolmentDAO();
    $enrolments = $enrolmentDao->retrieveEnrolmentBySection($course_code, $section_id);

    $bidDao = new BidDAO();
    $bid = $bidDao->retrieveBid($userid, $course_code);

    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();
    if (isset($bid)) { // the student has not bidded
        $errors[] = 'bid exists';
    } elseif (!validate_round_status()) {
        $errors[] = 'round ended';
    } else {
        if ($round_info[0] == 2) {
            $min_bid = $adminDao->getMinBid($course_code, $section_id);
            if ($amount < $min_bid) {
                $errors[] = 'bid too low';
            }
        }

        if ($currentbalance < $amount) {
            $errors[] = 'insufficient e$';
        }

        if (validate_timetable_clash('class', $userid, $course_code, $section_id)) {
            $errors[] = 'class timetable clash';
        }

        if (validate_timetable_clash('exam', $userid, $course_code, $section_id)) {
            $errors[] = 'exam timetable clash';
        }

        if (!validate_prerequisite_completed($userid, $course_code)) {
            $errors[] = 'incomplete prerequisites';
        }

        if (validate_course_completed($userid, $course_code)) {
            $errors[] = 'course completed';
        }

        if (validate_enrolment_exists($userid, $course_code, $section_id)) {
            $errors[] = 'course enrolled';
        }

        if (!validate_bid_number($userid)) {
            $errors[] = 'section limit reached';
        }

        if ($adminDao->getRound()[0] == 1 && $course->getSchool() != $student->getSchool()) {
            $errors[] = 'not own school course';
        }

        if (count($enrolments) == $section->getSize()) {
            $errors[] = 'no vacancy';
        }
    }

    if ((empty($errors))) {
        $bid = new Bid($userid, $amount, $course_code, $section_id);
        $bidDao->add($bid);

        $studentDao->updateEdollar($userid, $currentbalance - $amount);

        if ($round_info[0] == 2) {
            update_real_time_info($course_code, $section_id);
        }
        $result = [
            'status' => 'success'
        ];
    } else {
        sort($errors);
        $result = [
            'status' => 'error',
            'message' => $errors
        ];
    }
    return $result;
}
