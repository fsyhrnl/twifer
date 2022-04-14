<?php

require 'vendor/autoload.php';
require 'config.php';
use Twifer\API;

if ($_REQUEST['oauth_token'] !== null && $_REQUEST['oauth_verifier'] !== null) {

    $conn = new API(CONSUMER_KEY, CONSUMER_SECRET);
    $params = ['oauth_token' => $_REQUEST['oauth_token'], 'oauth_verifier' => $_REQUEST['oauth_verifier']];
    $access_token = $conn->oauth('oauth/access_token', $params);
    $_SESSION['access_token'] = $access_token;
    header('Location: login.php');

} else {
    header('Location: login.php');
}
