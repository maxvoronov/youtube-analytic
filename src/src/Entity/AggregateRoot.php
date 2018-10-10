<?php declare(strict_types=1);

namespace App\Entity;

abstract class AggregateRoot
{
    protected $id = '';

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
