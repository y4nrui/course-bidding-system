<?php

require_once '../include/common.php';
require_once '../include/update-bid.php';
require_once '../include/validation.php';
require_once '../include/bid-section.php';
require_once '../include/delete-bid.php';

$errors = perform_common_validation(['course', 'section', 'userid', 'amount']);

if (empty($errors)) { // common validation succeeds without errors
    # step 2 input validity checking
    $inputs = json_decode($_REQUEST['r'], true);
    $errors = validate_input_validity($inputs);

    # step 3 logic validation
    if (empty($errors)) { // input validity is done without errors --> logic validation
        $bidDao = new BidDAO();
        $bid = $bidDao->retrieveBid($inputs['userid'], $inputs['course']);

        if (isset($bid)) {
            if ($bid->getSection() == $inputs['section']) {
                $result = updateBid($inputs['userid'], $inputs['amount'], $inputs['course'], $inputs['section']);
            } else {
                deleteBid($inputs['userid'], $inputs['course'], $bid->getSection());
                $result = bidSection($inputs['userid'], $inputs['amount'], $inputs['course'], $inputs['section']);
            }
        } else {
            $result = bidSection($inputs['userid'], $inputs['amount'], $inputs['course'], $inputs['section']);
        }
    }
}

if (!empty($errors)) {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
