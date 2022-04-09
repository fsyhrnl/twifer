<?php

namespace Senggolbaok;

class Twifer
{
    private $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret;
    private $apiUrl = 'https://api.twitter.com/1.1/';
    private $apiUploadUrl = 'https://upload.twitter.com/1.1/';
    private $oauth = [];

    public function __construct($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->oauth_token = $oauth_token;
        $this->oauth_token_secret = $oauth_token_secret;

        $this->oauth = [
            'oauth_consumer_key' => $this->consumer_key,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->oauth_token,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
        ];

    }

    private function getAuth($oauth)
    {
        $headers = 'Authorization: OAuth ';
        $values = [];
        foreach ($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }

        $headers .= implode(', ', $values);
        return $headers;
    }

    private function buildString($method, $url, $params)
    {
        $headers = [];
        ksort($params);
        foreach ($params as $key => $value) {
            $headers[] = "$key=" . rawurlencode($value);
        }
        return $method . "&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $headers));
    }

    public function request($method, $apiUrl, $params = false)
    {

        if ($apiUrl == 'media/upload') {
            return $this->reqUpload($method, $apiUrl, $params);
            exit;
        }

        $url = $this->apiUrl . "{$apiUrl}.json";

        $oauth = $this->oauth;

        if ($params == false) {

            $base_info = $this->buildString($method, $url, $oauth);
            $composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_token_secret);
            $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
            $oauth['oauth_signature'] = $oauth_signature;

        } else {

            $oauth = array_merge($oauth, $params);
            $base_info = $this->buildString($method, $url, $oauth);
            $composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_token_secret);
            $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
            $oauth['oauth_signature'] = $oauth_signature;
        }

        $headers = [];
        $headers[] = $this->getAuth($oauth);

        $ch = curl_init();
        if ($params == false) {
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        $json = curl_exec($ch);
        return json_decode($json, true);

    }

    public function reqUpload($method, $apiUrl, $params)
    {

        $url = $this->apiUploadUrl . "{$apiUrl}.json";

        if (isset($params['media'])) {
            $filename = file_get_contents($params['media']);
            $base64 = base64_encode($filename);
            $arr = ['media_data' => $base64];
        } elseif (isset($params['media_data'])) {
            $filename = $params['media_data'];
            $arr = ['media_data' => $filename];
        } else {
            return 'doesnt work';
            exit;
        }

        $oauth = $this->oauth;

        $base_info = $this->buildString($method, $url, $oauth);
        $composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        $headers = [];
        $headers[] = $this->getAuth($oauth);
        $headers[] = 'Content-Type: multipart/form-data';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        $json = curl_exec($ch);
        return json_decode($json, true);

    }

    public function file($oauthUrl)
    {

        $oauth = $this->oauth;
        $base_info = $this->buildString("GET", $oauthUrl, $oauth);
        $composite_key = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        $headers = [];
        $headers[] = $this->getAuth($oauth);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $oauthUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $json = curl_exec($ch);
        return $json;

    }

}
