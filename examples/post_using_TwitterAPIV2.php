<?php

require 'vendor/autoload.php';
require 'config.php';
use Twifer\API;

$conn = new API(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);
$postfields = "{\"text\":\"Twifer v2\"}";
$res = $conn->request('POST', '/2/tweets', $postfields);
print_r($res);

/* 
 * Twitter API v2 calls :
 * https://developer.twitter.com/apitools/api
 *
 */