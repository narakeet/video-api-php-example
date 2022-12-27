<?php

$apikey = getenv('NARAKEET_API_KEY');
$zip_archive = realpath(dirname(__FILE__) . '/video/video.zip');
$source_file_in_zip = "source.txt";
function print_progress($task_progress) {
  // do something more useful here, to print progress using percent, message and thumbnail
  var_dump($task_progress);
}


require_once "narakeet_api_client.php";

$narakeet_api_client = new NarakeetApiClient($apikey /*, poll interval defaults to 5 seconds*/);

// upload the zip to Narakeet
$upload_token = $narakeet_api_client->request_upload_token();
$narakeet_api_client->upload_zip_file($upload_token, $zip_archive);

// start a build task from the uploaded zip and
// wait for it to finish
$task = $narakeet_api_client->request_build_task($upload_token, $source_file_in_zip);
$task_result = $narakeet_api_client->poll_until_finished($task["statusUrl"], "print_progress");

if ($task_result["succeeded"]) {
  $result_file = $narakeet_api_client->download_to_temp_file($task_result["result"]);
  echo "downloaded result to " . $result_file;
  echo " file size " . filesize($result_file);
} else {
  echo "there was a problem building the video " . $task_result["message"];
}

?>
