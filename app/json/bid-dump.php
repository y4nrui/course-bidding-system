<?php
require_once '../include/common.php';
require_once '../include/validation.php';

# first step perform common validation

$errors = perform_common_validation(['course', 'section']);

if (empty($errors)) { // common validation succeeds without errors
    # step 2 input validity checking

    $inputs = json_decode($_REQUEST['r'], True); // get the inputs as an associative array

    $errors = validate_input_validity($inputs);

    # step 3 logic validation
    if (empty($errors)) { // input validity is done without errors --> logic validation
        // no logic validation for bid-dump
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

    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();

    $bidDao = new BidDAO();
    $bids = $bidDao->retrieveBySection($course, $section);

    $result = [
        'status' => 'success',
        'bids' => []
    ];

    $sortClass = new Sort();
    $bids = $sortClass->sort_it($bids, 'bid_userid'); // sort the bid by amount (high to low) followed by userid (a to z)


    for ($i = 1; $i <= count($bids); $i++) {
        $bid = $bids[$i - 1];

        if ($round_info[1] == 1) { // active round
            $outcome = '-';
        } else { // no active round
            $enrolDao = new EnrolmentDAO();
            $enroled = $enrolDao->retrieveEnrolmentBySection($course, $section);
            $outcome = 'out';
            foreach ($enroled as $enrolment) { // check across all enroled students
                if ($bid->getUserid() == $enrolment->getUserid()) {
                    $outcome = 'in';
                    break;
                }
            }
        }

        $result['bids'][] = [
            "row" => $i,
            "userid" => $bid->getUserid(),
            "amount" => (float) $bid->getAmount(),
            "result" => $outcome
        ];
    }
}


header('Content-Type: application/json');
echo json_encode($result, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
