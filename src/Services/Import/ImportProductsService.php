<?php

declare(strict_types=1);

namespace App\Services\Import;

use App\Entity\Product;
use App\Repository\ProductWriteRepository;
use App\Services\Import\Conditions\CostAndStockCondition;
use App\Services\Import\Conditions\CostCondition;
use App\Services\Import\Conditions\ProductFileCondition;
use App\Services\Import\Exceptions\FileReadException;
use App\Services\Import\Exceptions\ImportFileReaderException;
use Carbon\Carbon;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportProductsService implements ImportProductsServiceInterface
{
    protected const BATCH_SIZE = 100;

    protected const DISCONTINUED_MARKER = 'yes';

    /**
     * @var ProductFileCondition[]
     */
    protected array $conditions = [];

    /**
     * @var int
     */
    protected int $rowsProcessed = 0;

    /**
     * @var int
     */
    protected int $rowsFiltered = 0;

    /**
     * @var int
     */
    protected int $rowsInvalid = 0;

    /**
     * @param ImportFileHelper $importFileHelper
     * @param ValidatorInterface $validator
     * @param ProductWriteRepository $writeRepository
     */
    public function __construct(
        private ImportFileHelper       $importFileHelper,
        private ValidatorInterface     $validator,
        private ProductWriteRepository $writeRepository
    ) {
        $this->addCondition(new CostCondition());
        $this->addCondition(new CostAndStockCondition());
    }

    /**
     * @param ProductFileCondition $condition
     */
    public function addCondition(ProductFileCondition $condition): void
    {
        $this->conditions[] = $condition;
    }

    /**
     * @param string $path
     * @param bool $test
     * @throws FileReadException
     * @throws ImportFileReaderException
     */
    public function importFromFile(string $path, bool $test = false): void
    {
        $this->clear();

        $validationConstraint = $this->setupProductValidator();
        $fileReader = $this->importFileHelper->getFileReader($path);

        $productsBatch = [];

        foreach ($fileReader->readFile($path) as $productItem) {
            $errors = $this->validator->validate($productItem, $validationConstraint);

            if (count($errors) > 0) {
                $this->rowsInvalid++;
                continue;
            }

            $product = new Product(
                $productItem['code'],
                $productItem['name'],
                $productItem['description'],
                (int)$productItem['stock'],
                $productItem['cost']
            );

            if ($productItem['discontinued'] === self::DISCONTINUED_MARKER) {
                $product->setDiscontinuedAt(Carbon::now()->toDateTimeImmutable());
            }

            if (!$this->applyFilers($product)) {
                $this->rowsFiltered++;
                continue;
            }

            $productsBatch[] = $product;

            if (count($productsBatch) % self::BATCH_SIZE) {
                if (!$test) {
                    $this->writeRepository->batchInsert($productsBatch);
                }

                $productsBatch = [];
            }

            $this->rowsProcessed++;
        }

        $this->writeRepository->batchInsert($productsBatch);
    }

    /**
     * @return int
     */
    public function getRowsProcessed(): int
    {
        return $this->rowsProcessed;
    }

    /**
     * @return int
     */
    public function getRowsFiltered(): int
    {
        return $this->rowsFiltered;
    }

    /**
     * @return int
     */
    public function getRowsInvalid(): int
    {
        return $this->rowsInvalid;
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function applyFilers(Product $product): bool
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->pass($product)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return Collection
     */
    private function setupProductValidator(): Collection
    {
        return new Collection([
            'code' => new NotBlank(),
            'name' => new NotBlank(),
            'description' => new AtLeastOneOf([
                new Blank(),
                new Length(max: 255)
            ]),
            'stock' => new PositiveOrZero(),
            'cost' => new PositiveOrZero(),
            'discontinued' => new AtLeastOneOf([
                new Blank(),
                new Length(max: 3)
            ]),
        ]);
    }

    /**
     * Clears counters
     */
    private function clear(): void
    {
        $this->rowsProcessed = 0;
        $this->rowsFiltered = 0;
        $this->rowsInvalid = 0;
    }
}
