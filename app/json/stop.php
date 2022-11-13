<?php

require_once '../include/common.php';
require_once '../include/validation.php';
require_once '../include/round-clear.php';

$errors = perform_common_validation([]);

if (empty($errors)) {
    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();

    if ($round_info[1] != 1) {
        # round 2 ended --> error
        $errors[] = 'round already ended';
    } else {
        if ($round_info[0] == 1) {
            clear_round_one();
        } else {
            clear_round_two();
        }
        $adminDao->setRound($round_info[0], 0);
    }
}

if (!empty($errors)) {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
} else {
    $result = [
        'status' => 'success'
    ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
