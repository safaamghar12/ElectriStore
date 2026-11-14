<?php

namespace App\Controller\Api;

use App\Entity\SaleItem;
use App\Repository\SaleItemRepository;
use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SaleItemController extends AbstractController
{
    #[Route('/api/sale-items/{id}', name: 'app_api_sale_item_show', methods: ['GET'])]
    public function show(SaleItemRepository $saleItemRepository, int $id): JsonResponse
    {
        $saleItem = $saleItemRepository->find($id);

        if (!$saleItem) {
            return $this->json(['message' => 'Sale item not found.'], 404);
        }

        return $this->json([
            'id' => $saleItem->getId(),
            'saleId' => $saleItem->getSale()?->getId(),
            'productId' => $saleItem->getProduct()?->getId(),
            'productName' => $saleItem->getProduct()?->getName(),
            'quantity' => $saleItem->getQuantity(),
            'unitPrice' => $saleItem->getUnitPrice(),
            'subtotal' => number_format((float) $saleItem->getUnitPrice() * (int) $saleItem->getQuantity(), 2, '.', ''),
        ], 200);
    }

    #[Route('/api/sales/{id}/items', name: 'app_api_sale_items_index', methods: ['GET'])]
    public function saleItems(SaleRepository $saleRepository, SaleItemRepository $saleItemRepository, int $id): JsonResponse
    {
        $sale = $saleRepository->find($id);

        if (!$sale) {
            return $this->json(['message' => 'Sale not found.'], 404);
        }

        $items = array_map(fn (SaleItem $saleItem) => [
            'id' => $saleItem->getId(),
            'productId' => $saleItem->getProduct()?->getId(),
            'productName' => $saleItem->getProduct()?->getName(),
            'quantity' => $saleItem->getQuantity(),
            'unitPrice' => $saleItem->getUnitPrice(),
            'subtotal' => number_format((float) $saleItem->getUnitPrice() * (int) $saleItem->getQuantity(), 2, '.', ''),
        ], $saleItemRepository->findBySale($sale));

        return $this->json([
            'saleId' => $sale->getId(),
            'items' => $items,
        ], 200);
    }
}
