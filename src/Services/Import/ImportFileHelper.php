<?php

declare(strict_types=1);

namespace App\Services\Import;

use App\Services\Import\CSV\CSVImportFileReader;
use App\Services\Import\Exceptions\FileReadException;
use App\Services\Import\Exceptions\ImportFileReaderException;

class ImportFileHelper
{
    /**
     * @var array|string[]
     */
    protected array $fileReaderMap = [
        'csv' => CSVImportFileReader::class
    ];

    /**
     * @param string $filepath
     * @return ImportFileReaderInterface
     * @throws FileReadException
     * @throws ImportFileReaderException
     */
    public function getFileReader(string $filepath): ImportFileReaderInterface
    {
        $fileInfo = new \SplFileInfo($filepath);

        if (!$fileInfo->isFile()) {
            throw new FileReadException();
        }

        $extension = $fileInfo->getExtension();

        if (!isset($this->fileReaderMap[$extension])) {
            throw new ImportFileReaderException();
        }

        $reader = $this->fileReaderMap[$extension];

        return new $reader();
    }
}
