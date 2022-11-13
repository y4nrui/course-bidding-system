<?php

require_once '../include/common.php';
require_once '../include/validation.php';
require_once '../include/drop-section.php';

$errors = perform_common_validation(['course', 'section', 'userid']);

if (empty($errors)) {
    $inputs = json_decode($_REQUEST['r'], true);
    $errors = validate_input_validity($inputs);

    if (!validate_round_status()) {
        $errors[] = 'round not active';
    }

    if (empty($errors)) {
        $result = dropSection($inputs['userid'], $inputs['course'], $inputs['section']);
    } else {
        sort($errors);
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
