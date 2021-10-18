<?php

declare(strict_types=1);

namespace App\Services\Import\Conditions;

use App\Entity\Product;

class CostCondition implements ProductFileCondition
{
    private const MAX_COST = 1000;

    /**
     * @param Product $product
     * @return bool
     */
    public function pass(Product $product): bool
    {
        return (float)$product->getCost() < self::MAX_COST;
    }
}
