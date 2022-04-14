<?php

require 'vendor/autoload.php';
require 'config.php';
use Twifer\API;

$conn = new API(CONSUMER_KEY, CONSUMER_SECRET);
$params = ['grant_type' => 'client_credentials'];
$bearerToken = $conn->oauth2('oauth2/token', $params);

$conn2 = new API(CONSUMER_KEY, CONSUMER_SECRET, $bearerToken['access_token']);
$res = $conn2->request('GET', '/2/users/by/username/senggolbaok');
print_r($res);

/* 
 * Twitter API v2 calls :
 * https://developer.twitter.com/apitools/api
 *
 */