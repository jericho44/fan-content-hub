<?php

namespace App\Interfaces;

interface ContentInterface
{
    public function getAll(int $limit, string $search = '');
    public function getPublicContents(int $limit, string $search = '', ?string $year = null, ?string $idolSlug = null, ?string $eventSlug = null);
    public function findByIdHash(string $idHash);
    public function create(array $data);
    public function update(string $idHash, array $data);
    public function delete(string $idHash);
}