<?php 

header("Content-Type: application/json");

require_once('setup.php');
require_once('classes.php');

// Get POST 
$request = file_get_contents('php://input');

// Get URL
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// Get METHOD
$method = $_SERVER['REQUEST_METHOD'];

// Get HEADERS
$headers = "";

if(!function_exists('apache_request_headers')) {
    function apache_request_headers() {
        $headers_array = array();
        foreach($_SERVER as $key => $value) {
            $headers_array[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            //if(substr($key, 0, 5) == 'HTTP_') {
            //    $headers_array[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
            //}
        }
        return $headers_array;
    }
}

$headers_arr = apache_request_headers();
foreach ($headers_arr as $header => $value) {
    $headers = $headers.'<br>'.$header.':'.$value;
}

// Init Apicall
$myApicall = NEW clsApicall($verzeichnis_data);

// Get Next Apicall
$myApicall->getNext();

// Fill Apicall
$myApicall->unixtimestamp_used = time();
$myApicall->method = $method;
$myApicall->request_base64 = base64_encode($request);
$myApicall->headers_base64 = base64_encode($headers);
$myApicall->url_base64 = base64_encode($url);
$myApicall->isanswered = "1";

// Update Apicall
$myApicall->update();

// Echo Response
print base64_decode($myApicall->response_base64);
?>