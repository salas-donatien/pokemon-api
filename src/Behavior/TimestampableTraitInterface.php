<?php

namespace App\Behavior;

use DateTimeInterface;

interface TimestampableTraitInterface
{
    public function getCreatedAt(): ?DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $createdAt): TimestampableTraitInterface;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function setUpdatedAt(DateTimeInterface $updatedAt): TimestampableTraitInterface;
}
