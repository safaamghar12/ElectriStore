<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\SaleItem;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

    $products = $manager->getRepository(Product::class)->findAll();

        if (count($products) < 1) {
            return;
        }

        // Each sale contains product indexes and quantities
        $salesData = [
            [0 => 2, 1 => 3],
            [2 => 2],
            [0 => 4, 3 => 2],
            [1 => 5],
            [3 => 3],
            [2 => 1, 1 => 2],
            [0 => 1, 2 => 3],
            [1 => 2, 3 => 1],
        ];

        foreach ($salesData as $index => $itemsData) {

            $sale = new Sale();
            $sale->setSaleDate(
                new \DateTimeImmutable("-$index days")
            );

            $total = 0;

            foreach ($itemsData as $productIndex => $quantity) {

                if (!isset($products[$productIndex])) {
                    continue;
                }

                $product = $products[$productIndex];

                $stock = $product->getStock();

                if ($stock === null || $stock <= 0) {
                    continue;
                }

                // Do not sell more than available stock
                $quantity = min($quantity, $stock);

                $unitPrice = (float) $product->getPrice();

                $subtotal = $quantity * $unitPrice;
                $total += $subtotal;

                $saleItem = new SaleItem();
                $saleItem->setProduct($product);
                $saleItem->setQuantity($quantity);
                $saleItem->setUnitPrice(
                    number_format($unitPrice, 2, '.', '')
                );

                $sale->addSaleItem($saleItem);

                // Update product stock
                $product->setStock($stock - $quantity);
            }

            $sale->setTotalAmount(
                number_format($total, 2, '.', '')
            );

            if ($total > 0) {
                $manager->persist($sale);
            }
        }


    // for ($i = 1; $i <= 5; $i++) {
    //         $category = new Category();
    //         $category->setName('Category ' . $i);
    //         $manager->persist($category);

    //         $manager->persist($category);
    //     }
        
    //     $manager->flush();


    //     for ($i = 1; $i <= 10; $i++) {
    //         $product = new Product();

    //         $product->setName('Product ' . $i);
    //         $product->setPrice((string) random_int(10, 100));
    //         $product->setStock(random_int(0, 100));
    //         $product->setIsAvailable(true);
    //         $category = $manager->getRepository(Category::class)->findOneBy(['name' => 'Category ' . random_int(1, 5)]);
    //         $product->setCategory($category);

    //         $manager->persist($product);
    //     }

        $manager->flush();
    }
}
