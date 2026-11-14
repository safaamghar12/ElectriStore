<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


final class CategoryController extends AbstractController
{
    #[Route('/api/categories', methods: ['GET'])]
    public function index(
        EntityManagerInterface $em,
        // SerializerInterface $serializer
    ): JsonResponse
    {
        $categories = $em->getRepository(Category::class)->findAll();

        // $json_content = $serializer->serialize($categories, 'json');

        // return JsonResponse::fromJsonString([
        //     'categories' => $json_content.name,
        //     'products' => $json_content.products.name,
        // ]);
        return $this->json(
            $categories,
            200,
            [],
            ['groups' => ['category', 'product-category']]
        );
        // return $this->json([
        //     'categories' => $categories,
        // ]);
    }

    #[Route('/api/categories/{id}', methods: ['GET'])]
    public function show(
        Category $category,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $json_content = $serializer->serialize($category, 'json', [
            ObjectNormalizer::IGNORED_ATTRIBUTES => ["id", "products"],
        ]);

        return JsonResponse::fromJsonString($json_content);
    }

    #[Route('/api/categories', methods: ['POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data =$request->getContent();

        $category = $serializer->deserialize($data, Category::class, "json");

        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            $error_messages = [];
            foreach ($errors as $error) {
                $error_messages[$error->getPropertyPath()] = [$error->getMessage()];
            }

            return $this->json([
                'message' => 'Validation failed',
                'errors' => $error_messages,
            ], 400);
        }

        // $data = json_decode($request->getContent(), true);

        // $category = new Category();
        // $category->setName($data['name']);

        $em->persist($category);
        $em->flush();

        return $this->json([
            'message' => 'Category created successfully'
        ], 201);
    }

    #[Route('/api/categories/{id}', methods: ['PUT', 'PATCH'])]
    public function update(
        Request $request, 
        Category $category,
        SerializerInterface $serializer, 
        EntityManagerInterface $em): JsonResponse
    {
        
        $serializer->deserialize(
            $request->getContent(), 
            Category::class, 'json', 
            ['object_to_populate' => $category]);

        $em->flush();

        return $this->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);

    }
    
    #[Route('/api/categories/{id}', methods: ['DELETE'])]
     public function delete(
            Category $category,
            EntityManagerInterface $em
        ): JsonResponse
        {
            $em->remove($category);
            $em->flush();   

            return $this->json([
                'message' => 'Category deleted successfully'
            ], 204 );
    }
}
