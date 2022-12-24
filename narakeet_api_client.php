<?php

class NarakeetApiClient {
  private $api_key;
  private $poll_interval;
  private $api_url;
  public function __construct($api_key,$poll_interval = 5, $api_url = "https://api.narakeet.com") {
    $this->api_key = $api_key;
    $this->poll_interval = $poll_interval;
    $this->api_url = $api_url;
  }
  public function request_upload_token() {
    $options = [
      CURLOPT_URL => $this->api_url . "/video/upload-request/zip",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "x-api-key: $this->api_key",
      ]
    ];
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $responseData = json_decode($response, true);
    curl_close($curl);
    return $responseData;
  }
  public function upload_zip_file($upload_token, $zip_archive) {
    $options = [
      CURLOPT_URL => $upload_token["url"],
      CURLOPT_PUT => true,
      CURLOPT_HTTPHEADER => [
        "Content-Type: " . $upload_token["contentType"]
      ],
      CURLOPT_INFILE => fopen($zip_archive, 'r'),
      CURLOPT_INFILESIZE => filesize($zip_archive)
    ];
    $curl = curl_init();
    curl_setopt_array($curl, $options);
    curl_exec($curl);
    $upload_error = curl_error($curl);
    curl_close($curl);
    if ($upload_error) {
      throw new Exception('Error: ' . $upload_error);
    }
  }
  public function request_build_task($upload_token, $source_file_in_zip) {
    $request = (object) [
      "repositoryType" => $upload_token["repositoryType"],
      "repository" => $upload_token["repository"],
      "source" => $source_file_in_zip
    ];
    $json_request = json_encode($request);
    $utf8_request = utf8_encode($json_request);
    $options = [
      CURLOPT_URL => $this->api_url . "/video/build",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $utf8_request,
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        "x-api-key: $this->api_key",
      ]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);
    $taskResponse = curl_exec($curl);
    $taskResponseData = json_decode($taskResponse, true);
    curl_close($curl);
    return $taskResponseData;
  }
  public function poll_until_finished($task_url, $progress_callback) {
    $options = [
      CURLOPT_URL => $task_url,
      CURLOPT_RETURNTRANSFER => true
    ];

    $curl = curl_init();
    curl_setopt_array($curl, $options);

    while (true) {
      $statusResponse = curl_exec($curl);
      $statusResponseData = json_decode($statusResponse, true);
      if ($statusResponseData["finished"]) {
        break;
      } else if ($progress_callback) {
        $progress_callback($statusResponseData);
      }
      sleep($this->poll_interval);
    }
    curl_close($curl);
    return $statusResponseData;
  }
  public function download_to_temp_file($url) {
    $tempFile = tempnam(sys_get_temp_dir(), "video");
    $curl = curl_init();

    // Set the URL and options for the download
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FILE, fopen($tempFile, "w"));

    $result = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($result === false) {
      throw new Exception('Error: ' . $error);
    }
    return $tempFile;
  }
}
?>
