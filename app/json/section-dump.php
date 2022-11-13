<?php

require_once '../include/common.php';
require_once '../include/validation.php';

$errors = perform_common_validation(['course', 'section']);

if (empty($errors)) { // common validation succeeds without errors
    # step 2 input validity checking
    $inputs = json_decode($_REQUEST['r'], True); // get the inputs as an associative array

    $errors = validate_input_validity($inputs);

    # step 3 logic validation
    if (empty($errors)) { // input validity is done without errors --> logic validation
        // no logic validation for section-dump
    }
}

if (!empty($errors)) {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
} else {
    $course = $inputs['course'];
    $section = $inputs['section'];

    $enrolmentDao = new EnrolmentDAO();
    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound(); // $round_info = array(current_round, round_status)

    # round_status = -1 at the beginning before bootstrap
    # status = 1 means during a round/ active round
    # status = 0 means a round has ended
    if ($round_info[0] == 1 and $round_info[1] == 0) { // means round 1 has ended 
        $enrolments = $enrolmentDao->retrieveEnrolmentBySection($course, $section);
    } elseif ($round_info[0] == 2 and $round_info[1] == 1) { // means during round 2
        $enrolments = $enrolmentDao->retrieveEnrolmentBySectionRound($course, $section, 1);
    } elseif (($round_info[0] == 2 and $round_info[1] == 0)) { // means round 2 has ended
        $enrolments = $enrolmentDao->retrieveEnrolmentBySection($course, $section);
    } else {
        $enrolments = [];
    }

    // a valid course & section
    $students = [];
    $sortClass = new Sort();
    $enrolments = $sortClass->sort_it($enrolments, 'userid'); // sort the enrolments by userid (a-z)

    foreach ($enrolments as $enrolment) {
        $userid = $enrolment->getUserid();
        $amount = $enrolment->getAmount();
        $students[] = [
            "userid" => $userid,
            "amount" => (float) $amount
        ];
    }

    $result = [
        "status" => "success",
        "students" => $students
    ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
