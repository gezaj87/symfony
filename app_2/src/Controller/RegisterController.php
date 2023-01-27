<?php

namespace App\Controller;

use Exception;
use App\Entity\User;

use OpenApi\Annotations as OA;
use App\Helper\ValidationHelper;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{

    const MISSING_INPUT = 'Missing input';
    const USER_CREATE_SUCCESS = 'User created successfully';

    /**
     * Register user.
     *
     * @OA\Post(
     *     @OA\RequestBody(
     *         description="Register user",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Teszt Elek"),
     *             @OA\Property(property="email", type="string", example="teszt@gmail.com"),
     *             @OA\Property(property="password1", type="string", example="1234"),
     *             @OA\Property(property="password2", type="string", example="1234"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *         ),
     *     ),
     *     
     * )
     */
    public function register(Request $request, ManagerRegistry $doctrine): Response
    {
        $content = $request->getContent();
        $decoded = json_decode($content, true);
        
        try
        {
            $name = $decoded['name'];
            $email = $decoded['email'];
            $password1 = $decoded['password1'];
            $password2 = $decoded['password2'];
        }
        catch(Exception $e)
        {
            return new Response(
                json_encode([
                    'success' => false,
                    'message' => self::MISSING_INPUT
                ])
            );
            die();
        }
        
        try
        {
            ValidationHelper::validateMissingInput([$name, $email, $password1, $password2]);
            ValidationHelper::validateEmail($email);
            ValidationHelper::validateName($name);
            ValidationHelper::validatePassword($password1, $password2);
        }
        catch(Exception $e)
        {
            return new Response(
                json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ])
            );
            die();
        }

        $password = password_hash($decoded['password1'], PASSWORD_BCRYPT); // => length = 60

        $entityManager = $doctrine->getManager();

        try
        {
            $user = new User();
            $user->setName($name);
            $user->setPassword($password);
            $user->setEmail($email);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        catch(Exception $e)
        {
            return new Response(
                json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ])
            );
            die();
        }
        

        return new Response(
            json_encode([
                'success' => true,
                'message' => self::USER_CREATE_SUCCESS
            ])
        );
    }
}