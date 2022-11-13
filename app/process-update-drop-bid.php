<?php
require_once "include/common.php";
require_once "include/validation.php";
require_once 'include/update-bid.php';
require_once 'include/delete-bid.php';

if (!isset($_GET) || (isEmpty($_GET['updated_amount']) && !isset($_GET['bids_to_drop']))) { // when user simply click Update & Drop Bids button without any input
    $_SESSION['errors'] = ['You did not submit any requests'];
    header("Location: update-drop-bid.php");
    exit;
}

if (!validate_round_status()) { // check if during active round
    $_SESSION['errors'] = ['You can only update and drop bids during active rounds'];
    header("Location: update-drop-bid.php");
    exit;
} else {
    $valid_action = False;
    $adminDao = new AdminDAO();
    $round_info = $adminDao->getRound();

    if (isset($_GET['bids_to_drop'])) { // first drop all bids to get all e$ back
        # there is valid bids dropped
        $valid_action = True;
        foreach ($_GET['bids_to_drop'] as $id => $value) {
            # use the index in the bids_to_drop (checkbox selected) to get the data
            # drop bid by bid and get error for a particular bid if any
            $deleteResult = deleteBid($_SESSION['username'], $_GET['course'][$id], $_GET['section'][$id]);
        }
    }

    $errors = [];
    foreach ($_GET['updated_amount'] as $id => $value) {
        # use the index in the bids_to_drop (checkbox selected) to get the data
        if (!empty($value) && (!isset($_GET['bids_to_drop']) || $_GET['bids_to_drop'][$id] == '')) {
            // empty($value) checks if a user input any updated amount
            // (!isset($_GET['bids_to_drop']) || $_GET['bids_to_drop'][$id] == '') checks if a bid with updated amount
            //      is selected to be dropped: if so dropping is prioritised and the amount will not be updated

            $input_errors = validate_input_validity(['amount' => $_GET['updated_amount'][$id]]); // check input validity

            if (!empty($input_errors)) { // if there is input validity errors
                $errors[] = "For course {$_GET['course'][$id]} section {$_GET['section'][$id]}: " . implode(",", $input_errors);
            } else {
                // check logic validation and if no error update the bid amount 
                $updateResult = updateBid($_SESSION['username'], $_GET['updated_amount'][$id], $_GET['course'][$id], $_GET['section'][$id]);

                if ($updateResult['status'] == 'error') { // if there's error, print out all errors
                    $errors[] = "For course {$_GET['course'][$id]} section {$_GET['section'][$id]}: " . implode(",", $updateResult['message']);
                } else {
                    // if any bid is updated
                    $valid_action = True;
                }
            }
        }
    }

    if ($valid_action) {
        $_SESSION['action_outcome'] = "Successfully updated & dropped bids.";
    }
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
    header("Location: update-drop-bid.php");
    exit;
}
