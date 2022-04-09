<?php

// require 'src/Twifer.php';

$conn = new Twifer('CONSUMER_KEY', 'CONSUMER_SECRET', 'OAUTH_TOKEN', 'OAUTH_TOKEN_SECRET');

// Upload media
$filename = 'profile.jpg';
$parameter = ['media' => $filename];
$req = $conn->request('POST', 'media/upload', $parameter);
print_r($req);

// Post tweet
$parameter = ['status' => 'Hi World'];
$req = $conn->request('POST', 'statuses/update', $parameter);
print_r($req);

// Get user_timeline
$parameter = ['screen_name' => 'senggolbaok', 'count' => '2'];
$req = $conn->request('GET', 'statuses/user_timeline', $parameter);
print_r($req);

// Get direct message
$req = $conn->request('GET', 'direct_messages/events/list');
print_r($req);

// Fetch image direct message
$imgUrl = 'https://ton.twitter.com/i/ton/data/dm/1512741758110601221/1512741750716375042/Kc5APl6V.jpg';
$req = $conn->file($imgUrl);
file_put_contents('getDm.jpg', $req);

// Lookup users
$parameter = ['screen_name' => 'senggolbaok'];
$req = $conn->request('GET', 'users/lookup', $parameter);
print_r($req);
