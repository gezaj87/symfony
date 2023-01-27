<?php

namespace App\Controller;

use Exception;
use App\Entity\Product;
use OpenApi\Annotations as OA;
use App\Controller\AuthController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    const MISSING_INPUT = 'Missing input';
    const AUTH_FAILED = 'Auth failed';

    /**
     * List products.
     *
     * @OA\Post(
     *     @OA\RequestBody(
     *         description="List products",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", example="adjjksdf564654645k378z34736tguhf4j78htiuerl"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="products", type="array", @OA\Items()),
     *         ),
     *     ),
     *     
     * )
     */
    public function list(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = AuthController::auth($request, $doctrine);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => self::AUTH_FAILED,
            ]);
        }

        $products = $doctrine->getRepository(Product::class)->findAll();
        $arr = [];
        foreach ($products as $product) {
            $arr[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
            ];
        }

        return $this->json([
            'success' => true,
            'products' => $arr,
        ]);

    }


    /**
     * Add product.
     *
     * @OA\Post(
     *     @OA\RequestBody(
     *         description="Add product",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", example="adjjksdf564654645k378z34736tguhf4j78htiuerl"),
     *             @OA\Property(property="name", type="string", example="Product name"),
     *             @OA\Property(property="price", type="number", example=100),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *         ),
     *     ),
     * )
     */
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = AuthController::auth($request, $doctrine);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => self::AUTH_FAILED,
            ]);
        }

        $content = $request->getContent();

        if (empty($content)) {
            return $this->json([
                'success' => false,
                'message' => self::MISSING_INPUT,
            ]);
        }

        $params = json_decode($content, true);

        if (empty($params['name']) || empty($params['price'])) {
            return $this->json([
                'success' => false,
                'message' => self::MISSING_INPUT,
            ]);
        }

        $product = new Product();
        $product->setName($params['name']);
        $product->setPrice($params['price']);
        $product->setUserId($user);

        $em = $doctrine->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * Edit product.
     *
     * @OA\Put(
     *     @OA\RequestBody(
     *         description="Edit product",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", example="adjjksdf564654645k378z34736tguhf4j78htiuerl"),
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Product name"),
     *             @OA\Property(property="price", type="number", example=100),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *         ),
     *     ),
     * )
     */
    public function edit(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = AuthController::auth($request, $doctrine);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => self::AUTH_FAILED,
            ]);
        }

        $content = $request->getContent();

        if (empty($content)) {
            return $this->json([
                'success' => false,
                'message' => self::MISSING_INPUT,
            ]);
        }

        $params = json_decode($content, true);

        if (empty($params['id']) || empty($params['name']) || empty($params['price'])) {
            return $this->json([
                'success' => false,
                'message' => self::MISSING_INPUT,
            ]);
        }

        $product = $doctrine->getRepository(Product::class)->find($params['id']);

        if (!$product) {
            return $this->json([
                'success' => false,
                'message' => 'Product not found',
            ]);
        }

        $product->setName($params['name']);
        $product->setPrice($params['price']);

        $em = $doctrine->getManager();
        $em->persist($product);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }


    /**
     * Delete product.
     *
     * @OA\Delete(
     *     @OA\RequestBody(
     *         description="Delete product",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", example="adjjksdf564654645k378z34736tguhf4j78htiuerl"),
     *             @OA\Property(property="id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *         ),
     *     ),
     *     
     * )
     */
    public function delete(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = AuthController::auth($request, $doctrine);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => self::AUTH_FAILED,
            ]);
            die();
        }

        $content = $request->getContent();

        if (empty($content)) {
            return $this->json([
                'success' => false,
                'message' => self::MISSING_INPUT,
            ]);
            die();
        }

        $params = json_decode($content, true);

        if (empty($params['id'])) {
            return $this->json([
                'success' => false,
                'message' => self::MISSING_INPUT,
            ]);
        }

        $product = $doctrine->getRepository(Product::class)->find($params['id']);

        if (!$product) {
            return $this->json([
                'success' => false,
                'message' => 'Product not found',
            ]);
        }

        $em = $doctrine->getManager();
        $em->remove($product);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
