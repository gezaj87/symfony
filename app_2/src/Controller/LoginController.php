<?php

namespace App\Controller;

use App\Entity\User;
use OpenApi\Annotations as OA;

use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    const USER_NOT_FOUND = "User not found";
    const WRONG_PASSWORD = "Wrong password";
    const LOGIN_SUCCESS = "Login success";
    const MISSING_INPUT = 'Missing input';

    /**
     * Login.
     *
        * Required: email, password in request body
        *
        * @OA\Post(
        *     @OA\RequestBody(
        *         description="Login",
        *         required=true,
        *         @OA\JsonContent(
        *             type="object",
        *             @OA\Property(property="email", type="string", example="admin@admin.hu"),
        *             @OA\Property(property="password", type="string", example="1234"),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Successful operation",
        *         @OA\JsonContent(
        *             type="object",
        *             @OA\Property(property="success", type="boolean", example=true),
        *             @OA\Property(property="message", type="string", example="Login success"),
        *             @OA\Property(property="token", type="string", example="adjjksdfk378z34736tguhf4j78htiuerl"),
        *         ),
        *     ),
    *     )
     */
    public function login(Request $request, ManagerRegistry $doctrine): Response
    {
        $content = $request->getContent();
        $decoded = json_decode($content, true);
        
        try
        {
            $email = $decoded['email'];
            $password = $decoded['password']; // => length = 60
        }
        catch (Exception $e)
        {
            return new Response(
                json_encode([
                    'success' => false,
                    'message' => self::MISSING_INPUT
                ])
            );
            die();
        }
        

        // $entityManager = $doctrine->getManager();

        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return new Response(
                json_encode([
                    'success' => false,
                    'message' => self::USER_NOT_FOUND
                ])
            );
            die();
        }

        
        if (!password_verify($password, $user->getPassword())) {
            return new Response(
                json_encode([
                    'success' => false,
                    'message' => self::WRONG_PASSWORD
                ])
            );
            die();
        }

        $token = self::generateToken($user->getEmail(), $user->getPassword());

        return new Response(
            json_encode([
                'success' => true,
                'message' => self::LOGIN_SUCCESS,
                'token' => $token,
            ])
        );
    }

    public static function generateToken($email, $password): string
    {
        $token = openssl_encrypt($password . $email, 'AES-128-ECB', $_ENV['APP_SECRET']);

        return $token;
    }

    
    
}