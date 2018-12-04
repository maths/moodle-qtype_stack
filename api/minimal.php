<?php 
require_once('../config.php');

$url = $CFG->dataurl . 'api/endpoint.php';
$content = file_get_contents($CFG->wwwroot . '/api/minimal.json');

$parsed = json_decode($content, true);
echo "<h1>JSON input parsed as follows:</h1>";
echo "<pre>";
print_r($parsed);
echo "</pre>";


$fields = array('data' => $content);
$fields_string = http_build_query($fields);

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
curl_setopt($ch, CURLOPT_URL, $url);

//execute post
$result = curl_exec($ch);

echo "<h1>Result</h1>";
echo "<pre>";
echo $result;
echo "</pre>";


$parsed = json_decode($result, true);
echo "<h1>Result parsed as follows:</h1>";
echo "<pre>";
print_r($parsed);
echo "</pre>";

?>
