<?php

namespace Twifer;

class API
{
    protected $consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret;
    protected $apiUrl = 'https://api.twitter.com/';
    protected $apiStandardUrl = 'https://api.twitter.com/1.1/';
    protected $apiUploadUrl = 'https://upload.twitter.com/1.1/';
    protected $oauth = [];

    public function __construct($consumer_key, $consumer_secret, $oauth_token = false, $oauth_token_secret = false)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->oauth_token = $oauth_token;
        $this->oauth_token_secret = $oauth_token_secret;

        if ($oauth_token == false && $oauth_token_secret == false) {
            $this->oauth = [
                'oauth_consumer_key' => $this->consumer_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0',
            ];
        } elseif ($oauth_token !== false && $oauth_token_secret == false) {
            $this->oauth = $oauth_token;
        } else {
            $this->oauth = [
                'oauth_consumer_key' => $this->consumer_key,
                'oauth_nonce' => time(),
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $this->oauth_token,
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0',
            ];
        }

    }

    protected function buildAutheaders($oauth)
    {
        $headers = 'Authorization: OAuth ';
        $values = [];
        foreach ($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }

        $headers .= implode(', ', $values);
        return $headers;
    }

    protected function buildBearerheaders($oauth)
    {
        $headers = 'Authorization: Bearer ' . $oauth;
        return $headers;
    }

    protected function buildString($method, $url, $params)
    {
        $headers = [];
        ksort($params);
        foreach ($params as $key => $value) {
            $headers[] = "$key=" . rawurlencode($value);
        }
        return $method . "&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $headers));
    }

    protected function buildSignature($baseInfo)
    {
        $encodeKey = rawurlencode($this->consumer_secret) . '&' . rawurlencode($this->oauth_token_secret);
        $oauthSignature = base64_encode(hash_hmac('sha1', $baseInfo, $encodeKey, true));
        return $oauthSignature;
    }

    protected function getSignature($method, $url, $params = false)
    {
        $oauth = $this->oauth;

        if ($params == false) {
            $baseInfo = $this->buildString($method, $url, $oauth);
            $oauth['oauth_signature'] = $this->buildSignature($baseInfo);
        } else {
            $oauth = array_merge($oauth, $params);
            $baseInfo = $this->buildString($method, $url, $oauth);
            $oauth['oauth_signature'] = $this->buildSignature($baseInfo);
        }
        return $oauth;
    }

    protected function reqCurl($method = 'GET', $url, $params = false, $headers = false, $postfields = false, $userpwd = false)
    {
        $ch = curl_init();

        if ($params == false) {
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        if ($params == true) {
            curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($params));
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        if ($postfields == true) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        }

        if ($headers == true) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($userpwd == true) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->consumer_key . ':' . $this->consumer_secret);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        return $result;
    }

    protected function request2($method, $req, $params = false)
    {

        $req = substr($req, 1);
        $url = $this->apiUrl . $req;
        if ($this->oauth_token !== false && $this->oauth_token_secret == false) {

            $oauth = $this->buildBearerheaders($this->oauth);
            $headers = [];
            $headers[] = $oauth;

        } else {

            $oauth = $this->getSignature($method, $url);
            $headers = [];
            $headers[] = 'Content-type: application/json';
            $headers[] = $this->buildAutheaders($oauth);
        }

        $result = $this->reqCurl($method, $url, null, $headers, $params);
        return json_decode($result, true);
    }

    public function request($method, $req, $params = false)
    {

        $method = strtoupper($method);

        if ($req == 'media/upload') {
            return $this->upload($method, $req, $params);
        }

        $version = explode('/', $req);
        if ($version[0] == '2') {
            $req = "/$req";
            return $this->request2($method, $req, $params);
        }

        if ($version[1] == '2') {
            return $this->request2($method, $req, $params);
        }

        if ($version[0] == 'labs') {
            $req = "/$req";
            return $this->request2($method, $req, $params);
        }

        if ($version[1] == 'labs') {
            return $this->request2($method, $req, $params);
        }

        $url = $this->apiStandardUrl . $req . ".json";

        if ($this->oauth_token !== false && $this->oauth_token_secret == false) {

            $oauth = $this->buildBearerheaders($this->oauth);
            $headers = [];
            $headers[] = $oauth;

        } else {

            $oauth = $this->getSignature($method, $url, $params);

            $headers = [];
            $headers[] = $this->buildAutheaders($oauth);
        }

        $result = $this->reqCurl($method, $url, $params, $headers, null);
        return json_decode($result, true);

    }

    protected function reqUpload($method, $req, $params)
    {
        $url = $this->apiUploadUrl . $req . ".json";
        $oauth = $this->getSignature($method, $url);

        $headers = [];
        $headers[] = $this->buildAutheaders($oauth);
        $headers[] = 'Content-Type: multipart/form-data';

        $result = $this->reqCurl($method, $url, null, $headers, $params);
        return json_decode($result, true);
    }

    protected function uploadChunked($req, $params)
    {
        $_params = [
            'command' => 'INIT',
            'total_bytes' => filesize($params['media']),
            'media_type' => $params['media_type'],
        ];

        if (isset($params['additional_owners'])) {
            $_params['additional_owners'] = $params['additional_owners'];
        }

        if (isset($params['media_category'])) {
            $_params['media_category'] = $params['media_category'];
        }

        $req = $this->reqUpload('POST', 'media/upload', $_params);

        $fp = fopen($params['media'], 'r');
        $segment_id = 0;
        while (!feof($fp)) {
            $chunk = fread($fp, 40960);

            $__params = [
                "command" => "APPEND",
                "media_id" => $req['media_id'],
                "segment_index" => $segment_id++,
                'media_data' => base64_encode($chunk),
            ];
            $this->reqUpload('POST', 'media/upload', $__params);
        }
        fclose($fp);
        $lastParams = array(
            "command" => "FINALIZE",
            "media_id" => $req['media_id'],
        );
        $result = $this->reqUpload('POST', 'media/upload', $lastParams);
        return $result;
    }

    public function upload($method, $req, $params)
    {
        if ($method == 'GET') {
            return "METHOD MUST BE POST";
        }

        $url = $this->apiUploadUrl . $req . ".json";
        $c = count($params);
        if ($c == 1 && isset($params['media'])) {

            $filename = file_get_contents($params['media']);
            $base64 = base64_encode($filename);
            $_params = ['media_data' => $base64];
            return $this->reqUpload('POST', 'media/upload', $_params);

        } elseif ($c == 1 && isset($params['media_data'])) {

            $base64 = $params['media_data'];
            $_params = ['media_data' => $base64];
            return $this->reqUpload('POST', 'media/upload', $_params);

        } else {
            return $this->uploadChunked($req, $params);
        }

    }

    public function file($oauthUrl)
    {

        $oauth = $this->getSignature("GET", $oauthUrl);

        $headers = [];
        $headers[] = $this->buildAutheaders($oauth);

        $result = $this->reqCurl("GET", $oauthUrl, null, $headers, null);
        return $result;

    }

    public function oauth($req, $params)
    {
        $url = $this->apiUrl . $req;

        $oauth = $this->getSignature("POST", $url, $params);
        $headers = [];
        $headers[] = $this->buildAutheaders($oauth);

        $result = $this->reqCurl("POST", $url, $params, $headers, null);
        parse_str($result, $arr);
        return $arr;

    }

    public function url($req, $params)
    {
        $url = $this->apiUrl . $req . "?" . http_build_query($params);
        return $url;
    }

    public function oauth2($req, $params)
    {
        $url = $this->apiUrl . $req;
        $result = $this->reqCurl("POST", $url, $params, null, null, true);
        return json_decode($result, true);
    }

}
