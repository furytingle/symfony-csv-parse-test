<?php

declare(strict_types=1);

namespace App\Services\Import\CSV;

use App\Services\Import\Exceptions\FileReadException;
use App\Services\Import\ImportFileReaderInterface;

class CSVImportFileReader implements ImportFileReaderInterface
{
    /**
     * @var array|string[]
     */
    protected array $headers = [
        'code',
        'name',
        'description',
        'stock',
        'cost',
        'discontinued'
    ];

    /**
     * @param string $path
     * @return iterable
     * @throws FileReadException
     */
    public function readFile(string $path): iterable
    {
        $fs = fopen($path, 'r');

        if (!$fs) {
            throw new FileReadException();
        }

        $row = 0;

        while (($item = fgetcsv($fs, 1000, ',')) !== false) {
            if ($row === 0) {
                $row++;
                //Not going to use headers from file
                continue;
            }

            yield $this->prepareProduct($item);

            $row++;
        }

        fclose($fs);
    }

    /**
     * @param array $productRow
     * @return array
     */
    private function prepareProduct(array $productRow): array
    {
        $formattedProduct = [];

        foreach ($this->headers as $i => $header) {
            $formattedProduct[$header] = $productRow[$i] ?? '';
        }

        return $formattedProduct;
    }
}
