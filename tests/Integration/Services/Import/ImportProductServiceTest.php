<?php

declare(strict_types=1);

namespace App\Tests\Integration\Services\Import;

use App\Services\Import\Exceptions\FileReadException;
use App\Services\Import\Exceptions\ImportFileReaderException;
use App\Services\Import\ImportProductsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ImportProductServiceTest extends KernelTestCase
{
    public function testImportFromCsvFileDoesntExist(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        /** @var ImportProductsServiceInterface $importProductService */
        $importProductService = $container->get(ImportProductsServiceInterface::class);

        $this->expectException(FileReadException::class);
        $importProductService->importFromFile('some-invalid/filepath.csv');
    }

    public function testImportFromXmlFileNoReader(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        /** @var ImportProductsServiceInterface $importProductService */
        $importProductService = $container->get(ImportProductsServiceInterface::class);

        $this->expectException(ImportFileReaderException::class);
        $importProductService->importFromFile(dirname(__FILE__) . '/test_stock.xml', true);
    }

    public function testImportFromCsvFile(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        /** @var ImportProductsServiceInterface $importProductService */
        $importProductService = $container->get(ImportProductsServiceInterface::class);

        $importProductService->importFromFile(dirname(__FILE__) . '/test_stock.csv', true);

        $this->assertEquals(23, $importProductService->getRowsProcessed());
        $this->assertEquals(4, $importProductService->getRowsInvalid());
        $this->assertEquals(2, $importProductService->getRowsFiltered());
    }
}