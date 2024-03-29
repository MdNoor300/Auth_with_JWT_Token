<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key; // Corrected import

class JWTToken 
{
    public static function CreateToken($userEmail){
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'userEmail' => $userEmail,
        ];
        return JWT::encode($payload, $key, 'HS256');
    }


    public static function CreateTokenForSetPassword($userEmail){
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-token',
            'iat' => time(),
            'exp' => time() + 60 * 5,    //set time for passsword resed time duration
            'userEmail' => $userEmail,
        ];
        return JWT::encode($payload, $key, 'HS256');
    }


    public static function VerifyToken($token): string  {
        try {
            $key = env('JWT_KEY');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded->userEmail;
        } 
        catch (Exception $e) {
            return 'Unauthorized';
        }
    }

   
}
