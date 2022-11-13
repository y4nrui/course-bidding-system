<?php

require_once 'include/common.php';
require_once 'include/round-clear.php';

$adminDao = new AdminDAO();
$round_info = $adminDao->getRound();
$bidDao = new BidDAO();
$errors = [];

if ($_GET['round-control'] == 'Start round') {
    if ($round_info[1] == '1') {
        $errors[] = 'Round has started';
    } else {
        if ($round_info[0] == 2) {
            $errors[] = 'Please bootstrap to start round 1';
        } else {
            $adminDao->setRound(2, 1);
            $bidDao->deleteAll();
            $_SESSION['action-outcome'] = 'Round 2 is started successfully.';
        }
    }
} elseif ($_GET['round-control'] == 'End round') {
    if ($round_info[1] != 1) {
        $errors[] = 'Round has not started';
    } else {
        if ($round_info[0] == 1) {
            clear_round_one();
            $_SESSION['action-outcome'] = 'Round 1 is ended successfully.';
        } else {
            clear_round_two();
            $_SESSION['action-outcome'] = 'Round 2 is ended successfully.';
        }
        $adminDao->setRound($round_info[0], 0);
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
}

header('Location:admin-home.php');
