<?php

require_once '../include/common.php';
require_once '../include/token.php';


# This page is to generate tokens for the end user to have access to the webservice
# Input: username & password from authenticate_form.php
# Validates: username and password match
# Output: json response with status & token ; errors


// isMissingOrEmpty(...) is in common.php
$errors = [
    isMissingOrEmpty('password'),
    isMissingOrEmpty('username')
];
$errors = array_filter($errors);


if (!isEmpty($errors)) {
    sort($errors);
    $result = [
        "status" => "error",
        "message" => array_values($errors)
    ];
} else {

    $adminDao = new AdminDAO();

    $messages = [];

    if ($_POST['username'] !== 'admin') {
        $messages[] = "invalid username";
    } elseif (password_verify($_POST['password'], $adminDao->getPassword())) {
        $result = [
            'status' => 'success',
            'token' => generate_token($_POST['username'])
        ];
    } else {
        $messages[] = "invalid password";
    }

    if (!empty($messages)) {
        $result = [
            'status' => 'error',
            'message' => $messages
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>