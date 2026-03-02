<?php

namespace App\Repository;

use App\Repository;

interface UserRepositoryInterface
{
    public function find(int $id): ?UserRepositoryInterface;
    public function findByLogin(string $login): ?UserRepositoryInterface;
}
