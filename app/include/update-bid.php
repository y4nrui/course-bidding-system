<?php

require_once "validation.php";
require_once "common.php";
require_once 'round-clear.php';
/**
 * This functions is to update a student's bid
 * Assumes the parameters have passed common and input validity checks
 * 
 * @param string $userid 
 * @param float  $amount
 * @param string $course_code
 * @param string $section_id
 * 
 * @return array $result, with errors if there is any
 */
function updateBid($userid, $amount, $course_code, $section_id)
{

    $errors = [];

    $studentDao = new StudentDAO();
    $student = $studentDao->retrieveStudent($userid);
    $currentbalance = $student->getEdollar();

    $bidDao = new BidDAO();
    $bid = $bidDao->retrieveBid($userid, $course_code);

    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();
    if (!validate_round_status()) {
        $errors[] = 'round ended';
    } elseif (!isset($bid)) { // the student has not bidded
        $errors[] = 'no such bid';
    } else {
        $currentbalance += $bid->getAmount(); // add back the previously bidded amount

        if ($round_info[0] == 2) {
            $min_bid = $adminDao->getMinBid($course_code, $section_id);
            if ($amount < $min_bid) {
                $errors[] = 'bid too low';
            }
        }

        if ($currentbalance < $amount) {
            $errors[] = 'insufficient e$';
        }

        if (validate_enrolment_exists($userid, $course_code, $section_id)) {
            $errors[] = 'course enrolled';
        }
    }

    if ((empty($errors))) {
        $bidDao->updateBid($userid, $course_code, $amount);
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
