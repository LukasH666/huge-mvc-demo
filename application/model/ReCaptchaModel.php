<?php

class ReCaptchaModel
{
    public static function verify($recaptchaResponse)
    {
        if (empty($recaptchaResponse)) {
            return false;
        }

        $secretKey = Config::get('RECAPTCHA_SECRET_KEY');

        if (empty($secretKey)) {
            return false;
        }

        $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

        $postData = http_build_query(array(
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ));

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $postData,
                'timeout' => 10
            )
        ));

        $result = file_get_contents($verifyUrl, false, $context);

        if ($result === false) {
            return false;
        }

        $resultData = json_decode($result);

        if (!isset($resultData->success)) {
            return false;
        }

        return $resultData->success === true;
    }
}