<?php

// this will autoload the class that we need in our code
spl_autoload_register(function ($class) {

    // we are assuming that it is in the same directory as common.php
    // otherwise we have to do
    // $path = 'path/to/' . $class . ".php"    
    require_once "$class.php";
});


// session related stuff

session_start();


function printErrors()
{
    if (isset($_SESSION['errors'])) {
        echo "<ul id='errors' style='color:red;'>";

        foreach ($_SESSION['errors'] as $value) {
            echo "<li>" . $value . "</li>";
        }

        echo "</ul>";
        unset($_SESSION['errors']);
    }
}



function isMissingOrEmpty($name)
{
    if (!isset($_REQUEST[$name])) {
        return "missing $name";
    }

    // client did send the value over
    $value = $_REQUEST[$name];
    if (empty($value)) {
        return "blank $name";
    }
}

# check if an int input is an int and non-negative
function isNonNegativeInt($var)
{
    if (is_numeric($var) && $var >= 0 && $var == round($var))
        return TRUE;
}

# check if a float input is numeric and non-negative
function isNonNegativeFloat($var)
{
    if (is_numeric($var) && $var >= 0)
        return TRUE;
}

# this is better than empty when use with array, empty($var) returns FALSE even when
# $var has only empty cells
function isEmpty($var)
{
    if (isset($var) && is_array($var))
        foreach ($var as $key => $value) {
            if (empty($value)) {
                unset($var[$key]);
            }
        }

    if (empty($var))
        return TRUE;
}
/**
 * To trim all leading and tailing whitespaces in each value in the array
 * @param array $arr the arr with values to be trimmed
 * 
 * @return array $trimmed_arr the arry with values trimmed
 */
function trim_all_value($arr)
{
    $trimmed_arr = [];
    foreach ($arr as $elem) {
        $trimmed_arr[] = trim($elem);
    }
    return $trimmed_arr;
}
/**
 * To retrieve the English word of weekdays based on a value
 * @param int $day 
 * 
 * @return string the corresponding weekday of the integer passed in
 * Note: both 0 and 7 corrspond to Sunday as different functions represent Sunday differently
 */
function getWeekday($day)
{
    $days = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
        0 => 'Sunday'
    ];

    return $days[(int) $day];
}
