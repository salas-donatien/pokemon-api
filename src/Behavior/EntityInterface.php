<?php

namespace App\Behavior;

use Ramsey\Uuid\UuidInterface;

interface EntityInterface
{
    public function getId(): ?int;

    public function getUuid(): ?string;

    public function setUuid(UuidInterface $uuid): EntityInterface;

    public function setUuidValue(): EntityInterface;
}
