<?php
#admin wants to know what's happening in the bidding process 

require_once '../include/common.php';
require_once '../include/validation.php';

$errors = perform_common_validation(['course', 'section']);

if (empty($errors)) { // common validation succeeds without errors
    # step 2 input validity checking

    $inputs = json_decode($_REQUEST['r'], True); // get the inputs as an associative array

    $errors = validate_input_validity($inputs);
}

if (!empty($errors)) {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
} else {
    $course = $inputs['course'];
    $section = $inputs['section'];

    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();

    $bidDao = new BidDAO();
    $bids = $bidDao->retrieveBySection($course, $section);
    $sortClass = new Sort();
    $bids = $sortClass->sort_it($bids, 'bid_userid'); // sort the bid by amount (high to low) followed by userid (a to z)

    $studentDao = new StudentDAO();
    $enrolmentDao = new EnrolmentDAO();

    $vacancy = $adminDao->getVacancy($course, $section);

    switch ($round_info) {
        case [1, 1]: # during round 1
            if (empty($bids)) {
                $min_bid = 10.0;
            } elseif (count($bids) < $vacancy) {
                $min_bid = $bids[count($bids) - 1]->getAmount();
            } else {
                $min_bid = $bids[$vacancy - 1]->getAmount();
            }
            break;

        case [1, 0]: # round 1 ends
            if (empty($bids)) {
                $min_bid = 10.0;
            } else {
                $enrolments = $enrolmentDao->retrieveEnrolmentBySectionRound($course, $section, 1);
                $enrolments = $sortClass->sort_it($enrolments, 'amount');
                if (empty($enrolments)) {
                    $min_bid = 10.0;
                } else {
                    $min_bid = end($enrolments)->getAmount();
                }
            }
            break;

        case [2, 1]: # during round 2
            $min_bid = $adminDao->getMinBid($course, $section);
            break;

        case [2, 0]:
            if (empty($bids)) {
                $min_bid = 10.0;
            } else {

                $enrolments = $enrolmentDao->retrieveEnrolmentBySectionRound($course, $section, 2);
                $enrolments = $sortClass->sort_it($enrolments, 'amount');

                $min_bid = $enrolments[0]->getAmount();
            }
            break;
    }

    $result = [
        'status' => 'success',
        'vacancy' => (int) $vacancy,
        'min-bid-amount' => (float) $min_bid,
        'students' => []
    ];

    for ($i = 1; $i <= count($bids); $i++) { # for loop thru an array of Bid objects
        $bid = $bids[$i - 1];

        $userid = $bid->getUserid();
        $amount = $bid->getAmount();
        $student = $studentDao->retrieveStudent($userid); #a Student object
        $balance = $student->getEdollar();
        $status = $bid->getResult();

        $result['students'][] = [
            "userid" => $userid,
            "amount" => (float) $amount,
            "balance" => (float) $balance,
            "status" => strtolower($status)
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
