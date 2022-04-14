<?php

namespace Twifer;

class API
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

    private function buildAutheaders($oauth)
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

    private function buildSignature($baseInfo)
    {
        $encodeKey = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_token_secret);
        $oauthSignature = base64_encode(hash_hmac('sha1', $baseInfo, $encodeKey, true));
        return $oauthSignature;
    }

    private function reqCurl($method = 'GET', $url, $params = false, $headers, $postfields = false)
    {
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

        if ($postfields != false) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        }

        $result = curl_exec($ch);
        return $result;
    }

    public function request($method, $apiUrl, $params = false)
    {
        $method = strtoupper($method);

        if ($apiUrl == 'media/upload') {
            return $this->upload($method, $apiUrl, $params);
            exit;
        }

        $url = $this->apiUrl . "{$apiUrl}.json";
        $oauth = $this->oauth;

        if ($params == false) {
            $baseInfo = $this->buildString($method, $url, $oauth);
            $oauth['oauth_signature'] = $this->buildSignature($baseInfo);
        } else {
            $oauth = array_merge($oauth, $params);
            $baseInfo = $this->buildString($method, $url, $oauth);
            $oauth['oauth_signature'] = $this->buildSignature($baseInfo);
        }

        $headers = [];
        $headers[] = $this->buildAutheaders($oauth);

        $result = $this->reqCurl($method, $url, $params, $headers, null);
        return $result;
    }

    public function upload($method, $apiUrl, $params)
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
            $arr = $params;
        }

        $oauth = $this->oauth;
        $baseInfo = $this->buildString($method, $url, $oauth);
        $oauth['oauth_signature'] = $this->buildSignature($baseInfo);

        $headers = [];
        $headers[] = $this->buildAutheaders($oauth);
        $headers[] = 'Content-Type: multipart/form-data';

        $result = $this->reqCurl($method, $url, null, $headers, $arr);
        return $result;

    }

    public function file($oauthUrl)
    {

        $oauth = $this->oauth;
        $baseInfo = $this->buildString("GET", $oauthUrl, $oauth);
        $oauth['oauth_signature'] = $this->buildSignature($baseInfo);

        $headers = [];
        $headers[] = $this->buildAutheaders($oauth);

        $result = $this->reqCurl("GET", $oauthUrl, null, $headers, null);
        return $result;

    }

}
