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
        if (!Storage::disk('google')->exists($path)) {
            Storage::disk('google')->makeDirectory($path);
        }
        
        return $path;
    }

    public function uploadFile($fileStream, string $path): array
    {
        // For Flysystem v3, put() returns void or boolean.
        // The masbug adapter uses the path as the identifier.
        Storage::disk('google')->put($path, $fileStream);
        Storage::disk('google')->setVisibility($path, 'public');
        
        return [
            'file_id' => $path,
            'url' => $this->getPublicUrl($path),
        ];
    }

    private function setPublicPermission(string $fileId): void
    {
        // Handled via Flysystem adapter config or manually if needed
        // The masbug adapter sets this to public by default on write
    }

    public function getPublicUrl(string $fileId): string
    {
        // For masbug adapter in Flysystem 3, we can get the raw Google ID
        // and construct a more stable 'usercontent' URL for embedding.
        try {
            if (method_exists($this->adapter, 'getMetadata')) {
                $metadata = $this->adapter->getMetadata($fileId);
                $id = $metadata->extraMetadata()['id'] ?? null;
                
                if ($id) {
                    // This format is often more reliable for direct embedding in <img> tags
                    return "https://lh3.googleusercontent.com/d/{$id}";
                }
            }
        } catch (\Exception $e) {
            // Fallback if metadata fails
        }

        if (method_exists($this->adapter, 'getUrl')) {
            return $this->adapter->getUrl($fileId);
        }
        
        return Storage::disk('google')->url($fileId);
    }
}