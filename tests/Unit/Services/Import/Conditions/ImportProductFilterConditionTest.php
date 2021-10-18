<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Import\Conditions;

use App\Entity\Product;
use App\Services\Import\Conditions\CostAndStockCondition;
use App\Services\Import\Conditions\CostCondition;
use PHPUnit\Framework\TestCase;

final class ImportProductFilterConditionTest extends TestCase
{
    public function testCostConditionPass(): void
    {
        $product = $this->makeProduct();

        $condition = new CostCondition();

        $this->assertTrue($condition->pass($product));

        $product = $this->makeProduct(cost: '1500.12');

        $this->assertFalse($condition->pass($product));
    }

    public function testCostAndStockConditionPass(): void
    {
        $product = $this->makeProduct();

        $condition = new CostAndStockCondition();

        $this->assertTrue($condition->pass($product));

        $product = $this->makeProduct(stock: 9, cost: '3.99');

        $this->assertFalse($condition->pass($product));
    }

    private function makeProduct(int $stock = 100, string $cost = '850.50'): Product
    {
        return new Product(
            'test_code',
            'test_name',
            'Test product description',
            $stock,
            $cost
        );
    }
}