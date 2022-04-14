<?php

require 'vendor/autoload.php';
require 'config.php';
use Twifer\API;

$conn = new API(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

$filename = 'example-image.jpg';
$img = $conn->request('POST', 'media/upload', ['media' => $filename]);

$params = ['status' => 'Twifer v2', 'media_ids' => $img['media_id']];
$res = $conn->request('POST', 'statuses/update', $params);
print_r($res);