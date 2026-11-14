<?php

namespace App\Controller\Api;

use App\Entity\Sale;
use App\Entity\SaleItem;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SaleController extends AbstractController
{
    #[Route('/api/sales', name: 'app_api_sale_index', methods: ['GET'])]
    public function index(SaleRepository $saleRepository): JsonResponse
    {
        $sales = $saleRepository->findAll();

        return $this->json(
            array_map(fn (Sale $sale) => $this->serializeSale($sale), $sales),
            200
        );
    }

    #[Route('/api/sales/{id}', name: 'app_api_sale_show', methods: ['GET'])]
    public function show(SaleRepository $saleRepository, int $id): JsonResponse
    {
        $sale = $saleRepository->find($id);

        if (!$sale) {
            return $this->json(['message' => 'Sale not found.'], 404);
        }

        return $this->json($this->serializeSale($sale), 200);
    }

    #[Route('/api/sales', name: 'app_api_sale_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ProductRepository $productRepository,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data['items']) || !is_array($data['items']) || empty($data['items'])) {
            return $this->json(['message' => 'A list of sale items is required.'], 400);
        }

        $sale = new Sale();
        $sale->setSaleDate(new \DateTimeImmutable());
        $sale->setTotalAmount('0.00');

        $totalAmount = 0.0;

        $em->getConnection()->beginTransaction();

        try {
            foreach ($data['items'] as $index => $itemData) {
                if (!isset($itemData['productId'], $itemData['quantity'])) {
                    return $this->json([
                        'message' => sprintf('Item #%d must include productId and quantity.', $index + 1),
                    ], 400);
                }

                $productId = (int) $itemData['productId'];
                $quantity = (int) $itemData['quantity'];

                if ($quantity <= 0) {
                    return $this->json([
                        'message' => sprintf('Item #%d quantity must be greater than zero.', $index + 1),
                    ], 400);
                }

                $product = $productRepository->find($productId);

                if (!$product) {
                    return $this->json([
                        'message' => sprintf('Product #%d was not found.', $productId),
                    ], 404);
                }

                if ($product->getStock() < $quantity) {
                    return $this->json([
                        'message' => sprintf('Insufficient stock for product "%s".', $product->getName()),
                    ], 400);
                }

                $unitPrice = $product->getPrice();

                if ($unitPrice === null) {
                    return $this->json([
                        'message' => sprintf('Product "%s" has no valid unit price.', $product->getName()),
                    ], 400);
                }

                $subtotal = (float) $unitPrice * $quantity;
                $totalAmount += $subtotal;

                $saleItem = new SaleItem();
                $saleItem->setProduct($product);
                $saleItem->setQuantity($quantity);
                $saleItem->setUnitPrice((string) number_format((float) $unitPrice, 2, '.', ''));
                $saleItem->setSale($sale);

                $sale->addSaleItem($saleItem);
                $product->setStock($product->getStock() - $quantity);
            }

            $sale->setTotalAmount((string) number_format($totalAmount, 2, '.', ''));

            $em->persist($sale);
            foreach ($sale->getSaleItems() as $saleItem) {
                $em->persist($saleItem);
            }

            $em->flush();
            $em->getConnection()->commit();

            return $this->json([
                'message' => 'Sale created successfully.',
                'sale' => $this->serializeSale($sale),
            ], 201);
        } catch (\Throwable $exception) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->getConnection()->rollBack();
            }

            return $this->json([
                'message' => 'Unable to create sale: ' . $exception->getMessage(),
            ], 500);
        }
    }

    private function serializeSale(Sale $sale): array
    {
        return [
            'id' => $sale->getId(),
            'saleDate' => $sale->getSaleDate()?->format('Y-m-d\TH:i:sP'),
            'totalAmount' => $sale->getTotalAmount(),
            'items' => array_map(fn (SaleItem $item) => [
                'id' => $item->getId(),
                'productId' => $item->getProduct()?->getId(),
                'productName' => $item->getProduct()?->getName(),
                'quantity' => $item->getQuantity(),
                'unitPrice' => $item->getUnitPrice(),
                'subtotal' => number_format((float) $item->getUnitPrice() * (int) $item->getQuantity(), 2, '.', ''),
            ], $sale->getSaleItems()->toArray()),
        ];
    }
}
