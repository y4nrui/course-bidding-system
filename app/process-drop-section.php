<?php
require_once "include/common.php";
require_once "include/validation.php";
require_once 'include/drop-section.php';


if (!isset($_GET) || empty($_GET)) { // when user simply click Drop Enrolled Sections button without any input
    $_SESSION['errors'] = ['You did not submit any requests'];
    header("Location: drop-section.php");
    exit;
}

if (!validate_round_status()) { // check if during active round
    $_SESSION['errors'] = ['You can only update and drop bids during active rounds'];
    header("Location: drop-section.php");
    exit;
} else {
    $valid_action = False;

    if (isset($_GET['enrolments_to_drop'])) { // first drop all enrolments to get all e$ back
        foreach ($_GET['enrolments_to_drop'] as $id => $value) {
            # use the index in the enrolments_to_drop (checkbox selected) to get the data
            # drop enrolment by enrolment and get error for a particular enrolment if any
            $deleteResult = dropSection($_SESSION['username'], $_GET['course'][$id], $_GET['section'][$id]);

            if ($deleteResult['status'] == 'error') {
                # if there is error, print out which enrolment contains the error 
                $errors[] = "For course {$_GET['course'][$id]} section {$_GET['section'][$id]}: " . implode(",", $deleteResult['message']);
            } else {
                # there is valid enrolments dropped
                $valid_action = True;
            }
        }
    }
}

if ($valid_action) {
    $_SESSION['action_outcome'] = "Successfully drop enrolled section.";
}
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
}
header("Location: drop-section.php");
exit;
