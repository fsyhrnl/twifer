<?php

require 'vendor/autoload.php';
use Twifer\API;

$conn = new Twifer('CONSUMER_KEY', 'CONSUMER_SECRET', 'OAUTH_TOKEN', 'OAUTH_TOKEN_SECRET');

// Upload media
$filename = 'profile.jpg';
$parameter = ['media' => $filename];
$req = $conn->request('POST', 'media/upload', $parameter);
$req = json_decode($req, true);
print_r($req);

// Post tweet
$parameter = ['status' => 'Hi World'];
$req = $conn->request('POST', 'statuses/update', $parameter);
$req = json_decode($req, true);
print_r($req);

// Delete tweet
$id = '1512864814338506753'; //id tweet
$req = $conn->request('POST', 'statuses/destroy/' . $id);
$req = json_decode($req, true);
print_r($req);

// Post retweet
$id = '1511820056430538755'; //id tweet
$req = $conn->request('POST', 'statuses/retweet/' . $id);
$req = json_decode($req, true);
print_r($req);

// Get user_timeline
$parameter = ['screen_name' => 'senggolbaok', 'count' => '2'];
$req = $conn->request('GET', 'statuses/user_timeline', $parameter);
$req = json_decode($req, true);
print_r($req);

// Get direct message
$req = $conn->request('GET', 'direct_messages/events/list');
$req = json_decode($req, true);
print_r($req);

// Fetch image direct message
$imgUrl = 'https://ton.twitter.com/i/ton/data/dm/1512741758110601221/1512741750716375042/Kc5APl6V.jpg';
$req = $conn->file($imgUrl);
$req = json_decode($req, true);
file_put_contents('fileDm.jpg', $req);

// Lookup user by username
$parameter = ['screen_name' => 'senggolbaok'];
$req = $conn->request('GET', 'users/lookup', $parameter);
$req = json_decode($req, true);
print_r($req);

// Lookup user by id
$parameter = ['user_id' => '965702083'];
$req = $conn->request('GET', 'users/lookup', $parameter);
$req = json_decode($req, true);
print_r($req);

// Follow user
$parameter = ['screen_name' => 'senggolbaok'];
$req = $conn->request('POST', 'friendships/create', $parameter);
$req = json_decode($req, true);
print_r($req);
