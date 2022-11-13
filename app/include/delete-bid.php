<?php
require_once "validation.php";
require_once "common.php";
require_once 'round-clear.php';
/**
 * This functions is to detele a student's bid and refund e$
 * Assumes the parameters have passed common and input validity checks
 * 
 * @param string $userid 
 * @param string $course_code
 * @param string $section_id
 * 
 * @return array $result, with errors if there is any
 */
function deleteBid($userid, $course_code, $section_id)
{
    $studentDao = new StudentDAO();
    $student = $studentDao->retrieveStudent($userid);
    $currentbalance = $student->getEdollar();

    $bidDao = new BidDAO();
    $bid = $bidDao->retrieveBid($userid, $course_code);
    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();

    $currentbalance += $bid->getAmount(); // add back the previously bidded amount 

    $bidDao->dropBid($userid, $course_code, $section_id);
    $studentDao->updateEdollar($userid, $currentbalance);

    if ($round_info[0] == 2) {
        update_real_time_info($course_code, $section_id);
    }

    $result = [
        'status' => 'success'
    ];

    return $result;
}
