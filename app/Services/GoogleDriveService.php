<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemAdapter;

class GoogleDriveService
{
    private FilesystemAdapter $adapter;

    public function __construct()
    {
        $this->adapter = Storage::disk('google')->getAdapter();
    }

    public function createFolderIfNotExists(string $year, string $idol, string $event): string
    {
        $path = "{$year}/{$idol}/{$event}";
        Storage::disk('google')->makeDirectory($path);
        
        $metadata = Storage::disk('google')->getMetadata($path);
        return $metadata['path'];
    }

    public function uploadFile($fileStream, string $path): array
    {
        Storage::disk('google')->put($path, $fileStream);
        
        $metadata = Storage::disk('google')->getMetadata($path);
        
        $fileId = $metadata['path'];
        $this->setPublicPermission($fileId);
        
        return [
            'file_id' => $fileId,
            'url' => $this->getPublicUrl($fileId),
        ];
    }

    private function setPublicPermission(string $fileId): void
    {
        // Handled via Flysystem adapter config or manually if needed
        // The masbug adapter sets this to public by default on write
    }

    public function getPublicUrl(string $fileId): string
    {
        return Storage::disk('google')->url($fileId);
    }
}