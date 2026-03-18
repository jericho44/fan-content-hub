<?php

namespace App\Services;

use App\Interfaces\EventInterface;

class EventService
{
    protected $eventRepository;

    public function __construct(EventInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getAll(int $limit, string $search = '')
    {
        return $this->eventRepository->getAll($limit, $search);
    }

    public function findByIdHash(string $idHash)
    {
        return $this->eventRepository->findByIdHash($idHash);
    }

    public function create(array $data)
    {
        return $this->eventRepository->create($data);
    }

    public function update(string $idHash, array $data)
    {
        return $this->eventRepository->update($idHash, $data);
    }

    public function delete(string $idHash)
    {
        return $this->eventRepository->delete($idHash);
    }
}