<?php

namespace App\Repositories;

use App\Interfaces\TagInterface;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TagRepository implements TagInterface
{
    protected $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function getAll(int $limit, string $search = '')
    {
        $query = $this->model->query();

        if (!empty($search)) {
            $query->where('name', 'ilike', '%' . $search . '%');
        }

        return $query->latest()->paginate($limit);
    }

    public function findByIdHash(string $idHash)
    {
        $tag = $this->model->where('id_hash', $idHash)->first();
        if (!$tag) {
            throw new NotFoundHttpException('Tag not found');
        }
        return $tag;
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
            $tag = $this->findByIdHash($idHash);
            $tag->update($data);
            return $tag->fresh();
        });
    }

    public function delete(string $idHash)
    {
        return DB::transaction(function () use ($idHash) {
            $tag = $this->findByIdHash($idHash);
            $tag->delete();
            return true;
        });
    }
}