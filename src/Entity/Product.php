<?php

declare(strict_types=1);

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $cost;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $discontinued_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;


    /**
     * @param string $code
     * @param string $name
     * @param string $description
     * @param int $stock
     * @param string $cost
     */
    public function __construct(
        string $code,
        string $name,
        string $description,
        int $stock,
        string $cost
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->stock = $stock;
        $this->cost = $cost;

        $this->created_at = Carbon::now()->toDateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @return string
     */
    public function getCost(): string
    {
        return $this->cost;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDiscontinuedAt(): ?\DateTimeImmutable
    {
        return $this->discontinued_at;
    }

    /**
     * @param \DateTimeImmutable $discontinued_at
     * @return $this
     */
    public function setDiscontinuedAt(\DateTimeImmutable $discontinued_at): self
    {
        $this->discontinued_at = $discontinued_at;

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }
}
