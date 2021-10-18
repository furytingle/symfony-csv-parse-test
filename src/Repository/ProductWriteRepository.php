<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductWriteRepository
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Product[] $products
     */
    public function batchInsert(array $products): void
    {
        foreach ($products as $product) {
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
