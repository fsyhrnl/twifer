<?php

require 'vendor/autoload.php';
require 'config.php';
use Twifer\API;

if (!isset($_SESSION['access_token'])) {
    $conn = new API(CONSUMER_KEY, CONSUMER_SECRET);
    $req = $conn->oauth('oauth/request_token', ['oauth_callback' => OAUTH_CALLBACK]);
    $makeUrl = $conn->url('oauth/authorize', ['oauth_token' => $req['oauth_token']]);
    echo "<a href='$makeUrl'> Login with Twitter </a>";
} else {
    $access_token = $_SESSION['access_token'];
    $conn = new API(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $user = $conn->request("GET", "account/verify_credentials");

    echo "<table width='50%' align='center'>";
    echo "<tr><td><img src='" . $user['profile_image_url'] . "'>";
    echo "</td>";
    echo "<td>Username : " . $user['screen_name'];
    echo "<br>User ID : " . $user['id'] . "</td></tr>";
    echo "<tr></tr><tr></tr><tr></tr><tr></tr>";
    echo "<tr><td>CONSUMER KEY</td><td><input type='text' size='60' value='Mfmoj6oxdQLhOokIRpYv4AVHw' readonly></td><tr>";
    echo "<tr><td>CONSUMER SECRET</td><td><input type='text' size='60' value='XHL720OJFEakYvoSnjjs30NT56voE4NcVkFKm9tFB7aoYxd23K' readonly></td><tr>";
    echo "<tr><td>OAUTH_TOKEN</td><td><input type='text' size='60' value='" . $access_token['oauth_token'] . "' readonly></td><tr>";
    echo "<tr><td>OAUTH_TOKEN_SECRET</td><td><input type='text' size='60' value='" . $access_token['oauth_token_secret'] . "' readonly></td><tr>";
    echo "</table>";
}
