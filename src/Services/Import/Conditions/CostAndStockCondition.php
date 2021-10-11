<?php

declare(strict_types=1);

namespace App\Services\Import\Conditions;

use App\Entity\Product;

class CostAndStockCondition implements ProductFilerCondition
{
    public const MIN_COST = 5;

    public const MIN_STOCK = 10;

    public function pass(Product $product): bool
    {
        return ((float) $product->getCost() > self::MIN_COST) || ($product->getStock() > self::MIN_STOCK);
    }
}