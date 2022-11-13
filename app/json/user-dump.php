<?php

require_once '../include/common.php';
require_once '../include/validation.php';

# first step perform common validation

$errors = perform_common_validation(['userid']);

if (empty($errors)) { // common validation succeeds without errors
    # step 2 input validity checking
    $inputs = json_decode($_REQUEST['r'], True); // get the inputs as an associative array

    $errors = validate_input_validity($inputs);

    # step 3 logic validation
    if (empty($errors)) { // input validity is done without errors --> logic validation
        // no logic validation ofr user-dump
    }
}

if (!empty($errors)) {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
} else {

    $userid = $inputs['userid'];
    $studentDao = new StudentDAO();
    $student = $studentDao->retrieveStudent($userid);

    $userid = $student->getUserid();
    $password = $student->getPassword();
    $name = $student->getName();
    $school = $student->getSchool();
    $edollar = $student->getEdollar();

    $result = [
        "status" => "success",
        "userid" => $userid,
        "password" => $password,
        "name" => $name,
        "school" => $school,
        "edollar" => (float) $edollar
    ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
