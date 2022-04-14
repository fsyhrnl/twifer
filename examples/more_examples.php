<?php

require 'vendor/autoload.php';
require 'config.php';
use Twifer\API;

$conn = new API(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

/*
 *
 * This is example
 * Read more https://developer.twitter.com/en/docs/twitter-api/v1 to know other parameters.
 *
 */

// Upload media
$filename = 'profile.jpg';
$parameter = ['media' => $filename];
$req = $conn->request('POST', 'media/upload', $parameter);
print_r($req);

// Post tweet
$parameter = ['status' => 'Hi World'];
$req = $conn->request('POST', 'statuses/update', $parameter);
print_r($req);

// Delete tweet
$id = '1512864814338506753'; //id tweet
$req = $conn->request('POST', 'statuses/destroy/' . $id);
print_r($req);

// Post retweet
$id = '1511820056430538755'; //id tweet
$req = $conn->request('POST', 'statuses/retweet/' . $id);
print_r($req);

// Get user_timeline
$parameter = ['screen_name' => 'senggolbaok', 'count' => '2'];
$req = $conn->request('GET', 'statuses/user_timeline', $parameter);
print_r($req);

// Get direct message
$req = $conn->request('GET', 'direct_messages/events/list');
print_r($req);

// Fetch image direct message / save image in direct message
$imgUrl = 'https://ton.twitter.com/i/ton/data/dm/1512867595292057605/1512867589323882496/_6uELIwA.png'; 
$req = $conn->file($imgUrl);
file_put_contents('saveImage.jpg', $req);
//print_r(base64_encode($req));

// Lookup user by username
$parameter = ['screen_name' => 'senggolbaok'];
$req = $conn->request('GET', 'users/lookup', $parameter);
print_r($req);

// Lookup user by id
$parameter = ['user_id' => '965702083'];
$req = $conn->request('GET', 'users/lookup', $parameter);
print_r($req);

// Follow user
$parameter = ['screen_name' => 'senggolbaok'];
$req = $conn->request('POST', 'friendships/create', $parameter);
print_r($req);