<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    public function getAll();

    public function find(string|int $id);

    public function create(array $attributes = []);

    public function update(string|int $id, $attributes = []);

    public function delete(string|int $id);
}
