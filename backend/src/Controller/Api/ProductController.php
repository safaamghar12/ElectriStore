<?php

namespace App\Controller\Api    ;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;



final class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'app_api_product_index', methods: ['GET'])]
    public function index(
        EntityManagerInterface $em,
        ): JsonResponse
    {
        $products = $em->getRepository(Product::class)->findAll();
        return $this->json(
            $products,
            200,
            [],
            ['groups' => ['product', 'Product-category']]
        );
    }
    
    #[Route('/api/products/{id}', 
    name: 'app_api_product_show',
    requirements: ['id' => '\d+'], 
    methods: ['GET'])]
    public function show(
        EntityManagerInterface $em,
        int $id,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        return new JsonResponse(
            $serializer->serialize($product, 'json', ['groups' => ['product', 'Product-category']]),
            200,
            [],
            true
        );
    } 
    
    #[Route('/api/products', name: 'app_api_product_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $product = new Product();   
        $product->setName($data['name'] ?? 'Default Product Name');
        $product->setPrice($data['price'] ?? '100.00');
        $product->setStock($data['stock'] ?? 10);
        $product->setIsAvailable($data['isAvailable'] ?? true);
        // throw new \Exception(
        //     'Category name: ' . ($data['categoryName'] ?? 'NOT FOUND')
        // );
        $category = $em->getRepository(Category::class)->findOneBy(['name' => $data['categoryName'] ?? 'Uncategorized']);
        $product->setCategory($category);

        $em->persist($product);
        $em->flush();

        return new JsonResponse(
            $serializer->serialize($product, 'json', ['groups' => ['product', 'Product-category']]),
            201,
            [],
            true
        );
    }

    #[Route('/api/products/{id}', name: 'app_api_product_update', methods: ['PUT'])]
    public function update(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        int $id
    ): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $product->setName($data['name'] ?? $product->getName());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $product->setStock($data['stock'] ?? $product->getStock());
        $product->setIsAvailable($data['isAvailable'] ?? $product->isAvailable());
        
        if (isset($data['categoryId'])) {
            $category = $em->getRepository(Category::class)->find($data['categoryId']);
            if ($category) {
                $product->setCategory($category);
            }
        }

        $em->flush();

        return new JsonResponse(
            $serializer->serialize($product, 'json', ['groups' => ['product', 'Product-category']]),
            200,
            [],
            true
        );
    }   

    #[Route('/api/products/{id}', name: 'app_api_product_delete', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $em,
        int $id
    ): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted successfully'], 200);
    }

    #[Route('/api/products/count', methods: ['GET'])]
     public function count(ProductRepository $productRepository): JsonResponse
    {
        return $this->json([
            'totalProducts' => $productRepository->countProducts()
         ]);
    }

    #[Route('/api/products/stats', methods: ['GET'])]
    public function getStats(ProductRepository $repository): JsonResponse
    {
        return $this->json([
            'outOfStock' => $repository->countOutOfStock(),
            'lowStock' => $repository->countLowStock()
        ]);
    }
}
