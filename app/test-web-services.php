<?php
require_once 'include/common.php';
require_once 'include/token.php';
function get_json_response($json_input, $url)
{
    $token_pos = strpos($url, 'token');
    $valid_token = "FALSE";

    if ($token_pos && verify_token(substr($url, $token_pos + 6))) {
        $valid_token = "TRUE";
    }

    $cURL = curl_init();
    echo "test url<br>$url<br>";
    echo "input<br>$json_input<br>token validity<br>$valid_token<br>";
    curl_setopt($cURL, CURLOPT_URL, $url);
    curl_setopt($cURL, CURLOPT_HTTPGET, true);

    curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));

    curl_exec($cURL);
    curl_close($cURL);
    echo "<hr>";
}

function add($key, $valid, $invalid, $array)
{
    $result = [];
    foreach ($array as $arr) {
        for ($i = 0; $i < 4; $i++) {
            switch ($i) {
                case 1:
                    $arr[$key] = '';
                    break;
                case 2:
                    $arr[$key] = $invalid[$key];
                    break;
                case 3:
                    $arr[$key] = $valid[$key];
                    break;
                case 0:
                    // $arr[$key] = 'missing';
                    break;
            }
            $result[] = $arr;
        }
    }
    return $result;
}

function generate_web_service_testcases($func, $valid, $invalid)
{
    /**
     * generate all possible cases for the webservices and run them and show the json response in the webpage
     * @param string $func the function name of the webservice, must match the php file name
     * @param array $valid an array of full valid inputs
     * @param array $invalid an array of invalid inputs
     * 
     */
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIiwiZGF0ZXRpbWUiOiIyMDE5LTEwLTAyIDA1OjQxOjMwIn0.wLzLkYRwnT9zqPiR0eqgolVfcqAYBFtaJWJd9fGd3EY';

    $base_url = 'http://localhost/spm/json/'; # you may need to change this part 

    $func_url = $base_url . $func;

    $all_inputs = [[]];
    for ($i = 0; $i < count($valid); $i++) {
        $all_inputs = add(array_keys($valid)[$i], $valid, $invalid, $all_inputs);
    }
    $n = 2;
    foreach ($all_inputs as $input) {
        $json_input = json_encode($input);
        if (!empty($input)) {
            $link = "$func_url?r=$json_input&token=$token";
        } else {
            $link = "$func_url?&token=$token";
        }
        // $test_links[] = $link;
        echo "$n<br>";
        get_json_response($json_input, $link);
        $n++;
    }

    get_json_response($json_input, "$func_url?r={}&token=$token");
    get_json_response($json_input, "$func_url?r=$json_input");
    get_json_response($json_input, "$func_url?r=$json_input&token=");
    get_json_response($json_input, "$func_url?r=$json_input&token=123");

    #manual ones below

    get_json_response($json_input, $func_url . '?r={}&token=123');
    get_json_response($json_input, $func_url . '?r={"userid":"amy.ng.2009","course":""}&token=' . $token);
}
function open_window($url)
{
    echo '<script>window.open ("' . $url . '", "mywindow","status=0,toolbar=0")</script>';
}

$testcases = [
    # webservice         # valid                                 #invalid
    'section-dump' => [['course' => 'IS100', 'section' => 'S1'], ['course' => 'IS111', 'section' => 'S100']],
    'user-dump' => [['userid' => 'amy.ng.2009'], ['userid' => 'ads.211']],
    'bid-dump' => [['course' => 'IS100', 'section' => 'S1'], ['course' => 'IS111', 'section' => 'S100']],
    'update-bid' => [['userid' => 'ben.ng.2009', 'course' => 'IS100', 'section' => 'S1', 'amount' => 12.5], ['userid' => 'ben.ng.2019', 'course' => 'IS111', 'section' => 'S100', 'amount' => 212.5]]
];


$func = 'user-dump'; // to be changed for other functions

echo "<pre>";
generate_web_service_testcases($func, $testcases[$func][0], $testcases[$func][1]);
echo "</pre>";



/**
 * JSON validation: missing, blank, invalid, valid
 * section-dump: 3 inputs:
 *          course, section, token 
 * 1. missing token missing course missing section
 * 2. missing token missing course blank section
 * ...
 * 50. correct token valid coures invalid section:  token,  course=IS100, section=S100
 * 
 * 
 * 1.missing course missing section 
 *                  blank section
 *                  
 * 2. blank course
 * 3. invalid course
 * 4. valid course
 * 
 */
