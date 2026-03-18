<?php

namespace App\Services;

use App\Interfaces\ContentInterface;
use App\Jobs\UploadImageToDriveJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentService
{
    protected $contentRepository;

    public function __construct(ContentInterface $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    public function getAll(int $limit, string $search = '')
    {
        return $this->contentRepository->getAll($limit, $search);
    }

    public function getPublicContents(int $limit, string $search = '', ?string $year = null, ?string $idolSlug = null, ?string $eventSlug = null)
    {
        return $this->contentRepository->getPublicContents($limit, $search, $year, $idolSlug, $eventSlug);
    }

    public function findByIdHash(string $idHash)
    {
        return $this->contentRepository->findByIdHash($idHash);
    }

    public function create(array $data)
    {
        return $this->contentRepository->create($data);
    }

    public function update(string $idHash, array $data)
    {
        return $this->contentRepository->update($idHash, $data);
    }

    public function delete(string $idHash)
    {
        return $this->contentRepository->delete($idHash);
    }

    /**
     * Handles the initial upload process by creating a pending record
     * and dispatching a job to upload the file to Google Drive.
     */
    public function processUpload(array $data, UploadedFile $file)
    {
        // Save file locally first so the queue worker can access it
        $filename = md5(uniqid('queue_')) . '.' . $file->getClientOriginalExtension();
        $localPath = "temp/{$filename}";
        Storage::disk('local')->putFileAs('temp', $file, $filename);

        // Create pending content record
        $data['status'] = 'pending';
        // Assume default type if not provided
        $data['type'] = $data['type'] ?? 'image';
        
        $content = $this->create($data);

        // Dispatch the job
        UploadImageToDriveJob::dispatch($content, $localPath);

        return $content;
    }
}