<?php

namespace App\Interfaces;

interface TagInterface
{
    public function getAll(int $limit, string $search = '');
    public function findByIdHash(string $idHash);
    public function create(array $data);
    public function update(string $idHash, array $data);
    public function delete(string $idHash);
}