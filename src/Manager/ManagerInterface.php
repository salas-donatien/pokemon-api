<?php

namespace App\Manager;

use App\Behavior\EntityInterface;

interface ManagerInterface
{
    public function persist(EntityInterface $entity): void;

    public function update(EntityInterface $entity): void;

    public function remove(EntityInterface $entity): void;
}
