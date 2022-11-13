<?php

require_once 'include/common.php';
require_once 'include/protect-student.php';
require_once 'include/bid-section.php';
require_once 'include/validation.php';

$errors = [];

foreach ($_GET as $key => $value) {
    if (!isset($value) || empty($value)) {
        $errors[] = $key . ' cannot be empty';
    }
}

if (empty($errors)) {
    $student_id = $_SESSION['username'];
    $course_code = strtoupper(trim($_GET['course_code']));
    $section_id = strtoupper(trim($_GET['section_id']));
    $amount = (float) trim($_GET['amount']);

    $inputs = [
        'userid' => $student_id,
        'course' => $course_code,
        'section' => $section_id,
        'amount' => $amount
    ];

    $errors = validate_input_validity($inputs);
}

if (empty($errors)) {
    $result = bidSection($student_id, $amount, $course_code, $section_id);
    if ($result['status'] == 'error') {
        $errors = $result['message'];
    }
}

if (empty($errors)) {
    $_SESSION['action_outcome'] = "Successfully bidded section $section_id for $course_code with $$amount";

    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();
    header('Location: student-home.php');
    exit;
} else {
    $_SESSION['errors'] = $errors;
    header('Location: bid-section.php');
    exit;
}
