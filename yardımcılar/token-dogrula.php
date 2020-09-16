<?php

require "../vendor/autoload.php";

use \Firebase\JWT\JWT;

class TokenDogrula
{

    public static function dogrula()
    {
        $secret_key = "nosbirciler";
        //$header = apache_request_headers(); // gelen isteğin headerı bilgilerini çektik
        $token = $_SERVER["HTTP_AUTHORIZATION"]; // header içinde authorization anahtarı ile gelen tokenı aldıık
        $token=explode(" ",$token);
        $token=$token[1];
        if ($token) { // eğer token varsa
            try { // token doğru gelmişse bilgiler değiştirilmemişse try çalışcak yanlışsa catch çalışcak
                $decoded = JWT::decode($token, $secret_key, array("HS256"));
                return json_encode(array(
                    "durum" => 1,
                    "token" => $decoded
                ));//eğer token doğruysa gelen tokenı ve durumu 1 dönderiyoruz
            } catch (Exception $e) {
                return json_encode(array(
                    "durum" => 0
                ));// token değiştirilmişse veya hatalıysa durum 0 dönderiyoruz
            }
        } else {
            http_response_code(401);
            return json_encode(array(
                "durum" => 0
            ));// header kısmında token gelmediyse durum 0 dönderiyoruz
        }
        

    }

}

