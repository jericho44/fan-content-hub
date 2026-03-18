<?php

namespace App\Services;

use App\Interfaces\TagInterface;

class TagService
{
    protected $tagRepository;

    public function __construct(TagInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getAll(int $limit, string $search = '')
    {
        return $this->tagRepository->getAll($limit, $search);
    }

    public function findByIdHash(string $idHash)
    {
        return $this->tagRepository->findByIdHash($idHash);
    }

    public function create(array $data)
    {
        return $this->tagRepository->create($data);
    }

    public function update(string $idHash, array $data)
    {
        return $this->tagRepository->update($idHash, $data);
    }

    public function delete(string $idHash)
    {
        return $this->tagRepository->delete($idHash);
    }
}