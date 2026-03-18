<?php

namespace App\Repositories;

use App\Interfaces\EventInterface;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventRepository implements EventInterface
{
    protected $model;

    public function __construct(Event $model)
    {
        $this->model = $model;
    }

    public function getAll(int $limit, string $search = '')
    {
        $query = $this->model->query();

        if (!empty($search)) {
            $query->whereRaw("search_vector @@ to_tsquery('english', ?)", [$search . ':*']);
        }

        return $query->latest()->paginate($limit);
    }

    public function findByIdHash(string $idHash)
    {
        $event = $this->model->where('id_hash', $idHash)->first();
        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }
        return $event;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(string $idHash, array $data)
    {
        return DB::transaction(function () use ($idHash, $data) {
            $event = $this->findByIdHash($idHash);
            $event->update($data);
            return $event->fresh();
        });
    }

    public function delete(string $idHash)
    {
        return DB::transaction(function () use ($idHash) {
            $event = $this->findByIdHash($idHash);
            $event->delete();
            return true;
        });
    }
}