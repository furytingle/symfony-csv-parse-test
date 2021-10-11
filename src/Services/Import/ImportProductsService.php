<?php

declare(strict_types=1);

namespace App\Services\Import;

use App\Entity\Product;
use App\Exceptions\FileReadException;
use App\Services\Import\Conditions\CostAndStockCondition;
use App\Services\Import\Conditions\CostCondition;
use App\Services\Import\Conditions\ProductFilerCondition;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ImportProductsService implements ImportProductsServiceInterface
{
    /** @var ProductFilerCondition[] */
    protected array $conditions = [];

    protected array $headers = [
        'code',
        'name',
        'description',
        'stock',
        'cost',
        'discontinued'
    ];

    protected int $rowsProcessed = 0;

    protected int $rowsFiltered = 0;

    protected int $rowsInvalid = 0;

    public function __construct(
        private ValidatorInterface     $validator,
        private EntityManagerInterface $entityManager
    )
    {
        $this->addCondition(new CostCondition());
        $this->addCondition(new CostAndStockCondition());
    }

    public function addCondition(ProductFilerCondition $condition): void
    {
        $this->conditions[] = $condition;
    }

    public function importFromCSV(string $path, bool $test = false): void
    {
        $this->clear();

        $validationConstraint = $this->setupProductValidator();

        foreach ($this->readCSVFile($path) as $productItem) {
            $errors = $this->validator->validate($productItem, $validationConstraint);

            if (count($errors) > 0) {
                $this->rowsInvalid++;
                continue;
            }

            $product = new Product();

            $product->setCode($productItem['code'])
                ->setName($productItem['name'])
                ->setDescription($productItem['description'])
                ->setCost($productItem['cost'])
                ->setStock((int)$productItem['stock']);

            if ($productItem['discontinued'] === 'yes') {
                $product->setDiscontinuedAt(Carbon::now()->toDateTimeImmutable());
            }

            if (!$this->applyFilers($product)) {
                $this->rowsFiltered++;
                continue;
            }

            if (!$test) {
                $this->insert($product);
            }

            $this->rowsProcessed++;
        }
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

    private function readCSVFile(string $path): iterable
    {
        try {
            $fs = fopen($path, 'r');
        } catch (\Throwable $e) {
            throw new FileReadException('No such file');
        }

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

    private function applyFilers(Product $product): bool
    {
        foreach ($this->conditions as $condition) {
            if (!$condition->pass($product)) {
                return false;
            }
        }

        return true;
    }

    private function prepareProduct(array $productRow): array
    {
        $formattedProduct = [];

        foreach ($this->headers as $i => $header) {
            $formattedProduct[$header] = $productRow[$i] ?? '';
        }

        return $formattedProduct;
    }

    private function insert(Product $product): void
    {
        $product->setCreatedAt(Carbon::now()->toDateTimeImmutable());

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

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

    private function clear(): void
    {
        $this->rowsProcessed = 0;
        $this->rowsFiltered = 0;
        $this->rowsInvalid = 0;
    }
}