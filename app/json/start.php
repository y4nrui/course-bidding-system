<?php

require_once '../include/common.php';
require_once '../include/validation.php';

$errors = perform_common_validation([]);

if (empty($errors)) {
    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();
    $bidDao = new BidDAO();
    if ($round_info[0] == 2 && $round_info[1] == 0) {
        # round 2 ended --> error
        $errors[] = 'round 2 ended';
    } else {
        if ($round_info[1] == 0) {
            $adminDao->setRound(2, 1);
            $bidDao->deleteAll();
        }
    }
}

if (!empty($errors)) {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
} else {
    $round_info = $adminDao->getRound();
    $result = [
        'status' => 'success',
        'round' => (int) $round_info[0]
    ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
