<?php

declare(strict_types=1);

namespace App\Tests\Integration\Services\Import;

use App\Exceptions\FileReadException;
use App\Services\Import\ImportProductsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ImportProductServiceTest extends KernelTestCase
{
    public function testImportFromCsvFileDoesntExist(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $importProductService = $container->get(ImportProductsServiceInterface::class);

        $this->expectException(FileReadException::class);
        $importProductService->importFromCSV('some-invalid/filepath.csv');
    }

    public function testImportFromCsvFile(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $importProductService = $container->get(ImportProductsServiceInterface::class);

        $importProductService->importFromCSV(dirname(__FILE__) . '/test_stock.csv', true);

        $this->assertEquals(23, $importProductService->getRowsProcessed());
        $this->assertEquals(4, $importProductService->getRowsInvalid());
        $this->assertEquals(2, $importProductService->getRowsFiltered());
    }
}