<?php
require_once "validation.php";
require_once "common.php";
require_once 'round-clear.php';
/**
 * This functions is to drop a student's section and refund e$
 * Assumes the parameters have passed common and input validity checks
 * 
 * @param string $userid 
 * @param string $course_code
 *       
 * @return array $result, with errors if there is any
 */
function dropSection($userid, $course_code)
{
    $studentDao = new StudentDAO();
    $student = $studentDao->retrieveStudent($userid);
    $currentbalance = $student->getEdollar();

    $adminDao = new AdminDAO();

    $enrolmentDao = new EnrolmentDAO();
    $enrolment = $enrolmentDao->retrieveEnrolmentByStudentCourse($userid, $course_code);

    $currentbalance += $enrolment->getAmount(); // add back the previously bidded amount

    $enrolmentDao->deleteEnrolment($userid, $course_code);
    $vacancy = $adminDao->getVacancy($enrolment->getCourse(), $enrolment->getSection());
    $min_bid = $adminDao->getMinBid($enrolment->getCourse(), $enrolment->getSection());
    $adminDao->updateSectionStatus($enrolment->getCourse(), $enrolment->getSection(), $min_bid, $vacancy + 1);

    if ($adminDao->getRound()[0] == 2) {
        update_real_time_info($course_code, $enrolment->getSection());
    }

    $studentDao->updateEdollar($userid, $currentbalance);
    $result = [
        'status' => 'success'
    ];

    return $result;
}
