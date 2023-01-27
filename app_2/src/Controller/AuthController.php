<?php

namespace App\Controller;

use Exception;
use App\Entity\User;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthController extends AbstractController
{
    const USER_NOT_FOUND = "User not found";
    const WRONG_PASSWORD = "Wrong password";
    const LOGIN_SUCCESS = "Login success";
    const MISSING_INPUT = 'Missing input';


    public static function auth(Request $request, ManagerRegistry $doctrine)
    {
        $content = $request->getContent();
        $decoded = json_decode($content, true);
        
        try
        {
            $token = $decoded['token'];
        }
        catch (Exception $e)
        {
            return false;
            die();
        }
        
        $token_decrypt = openssl_decrypt($token, 'AES-128-ECB', $_ENV['APP_SECRET']);

        if (!$token_decrypt) {
            return false;
            die();
        }

        $password = substr($token_decrypt, 0, 60);
        $email = substr($token_decrypt, 60);

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email, 'password' => $password]);
        if (!$user) {
            return false;
            die();
        }


        return $user;
        die();


    }
}