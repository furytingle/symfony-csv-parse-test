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

        $product->setCost('1500.00');

        $this->assertFalse($condition->pass($product));
    }

    public function testCostAndStockConditionPass(): void
    {
        $product = $this->makeProduct();
        $product->setCost('25.50')->setStock(15);

        $condition = new CostAndStockCondition();

        $this->assertTrue($condition->pass($product));

        $product->setCost('3.99')->setStock(9);

        $this->assertFalse($condition->pass($product));
    }

    private function makeProduct(): Product
    {
        $product = new Product();
        $product->setCode('test_code')
            ->setName('test_name')
            ->setCost('892.50')
            ->setStock(100)
            ->setDescription('Test product description');

        return $product;
    }
}