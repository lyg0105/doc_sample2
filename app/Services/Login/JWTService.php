<?php
namespace App\Services\Login;

use \Firebase\JWT\JWT;

class JWTService
{

    public function ecodeToken()
    {
        $key =env('APP_KEY');
        $token = array(
            "iss" => "dlit_code",
            "aud" => "aaaa",
            "iat" => time(),
            "exp" =>  time() + 60 * 60 * 24
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($token, $key);
        return $jwt;
    }

    public function decodeToken($jwt)
    {
        $key =env('APP_KEY');
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        return $decoded;
    }
}
