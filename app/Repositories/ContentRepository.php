<?php

namespace App\Repositories;

use App\Interfaces\ContentInterface;
use App\Models\Content;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentRepository implements ContentInterface
{
    protected $model;

    public function __construct(Content $model)
    {
        $this->model = $model;
    }

    public function getAll(int $limit, string $search = '')
    {
        $query = $this->model->with(['event', 'tags']);

        if (!empty($search)) {
            $query->whereRaw("search_vector @@ to_tsquery('english', ?)", [$search . ':*']);
        }

        return $query->latest()->paginate($limit);
    }

    public function getPublicContents(int $limit, string $search = '', ?string $year = null, ?string $idolSlug = null, ?string $eventSlug = null)
    {
        $query = $this->model->with(['event', 'tags'])->where('status', 'active');

        if (!empty($search)) {
            $query->whereRaw("search_vector @@ to_tsquery('english', ?)", [$search . ':*']);
        }

        if (!empty($year)) {
            $query->whereYear('created_at', $year);
        }

        if (!empty($idolSlug)) {
            $query->whereRaw("metadata->>'idol' = ?", [$idolSlug]);
        }

        if (!empty($eventSlug)) {
            $query->whereHas('event', function ($q) use ($eventSlug) {
                $q->where('slug', $eventSlug);
            });
        }

        return $query->latest()->paginate($limit);
    }

    public function findByIdHash(string $idHash)
    {
        $content = $this->model->with(['event', 'tags'])->where('id_hash', $idHash)->first();
        if (!$content) {
            throw new NotFoundHttpException('Content not found');
        }
        return $content;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $content = $this->model->create($data);
            if (isset($data['tags'])) {
                $content->tags()->sync($data['tags']);
            }
            return $content;
        });
    }

    public function update(string $idHash, array $data)
    {
        return DB::transaction(function () use ($idHash, $data) {
            $content = $this->findByIdHash($idHash);
            $content->update($data);
            if (isset($data['tags'])) {
                $content->tags()->sync($data['tags']);
            }
            return $content->fresh(['event', 'tags']);
        });
    }

    public function delete(string $idHash)
    {
        return DB::transaction(function () use ($idHash) {
            $content = $this->findByIdHash($idHash);
            $content->delete();
            return true;
        });
    }
}