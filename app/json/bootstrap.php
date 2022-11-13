<?php
require_once '../include/bootstrap.php';
require_once '../include/validation.php';

$errors = perform_common_validation([]);
if (empty($errors)) {
    $result = doBootstrap();
} else {
    $result = [
        'status' => 'error',
        'message' => $errors
    ];
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
