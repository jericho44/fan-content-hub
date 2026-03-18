<?php

namespace App\Jobs;

use App\Models\Content;
use App\Services\GoogleDriveService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadImageToDriveJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60; // Wait 60 seconds before retrying

    protected Content $content;
    protected string $localFilePath;

    /**
     * Create a new job instance.
     */
    public function __construct(Content $content, string $localFilePath)
    {
        $this->content = $content;
        $this->localFilePath = $localFilePath;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleDriveService $driveService): void
    {
        Log::info("Starting Google Drive upload for Content ID: {$this->content->id}");

        try {
            if (!Storage::disk('local')->exists($this->localFilePath)) {
                throw new \Exception("Local file not found: {$this->localFilePath}");
            }

            // Determine folder structure: Year / Idol / Event
            // Assuming event name is available, otherwise default to 'General'
            // Assuming idol is in metadata or default to 'Unknown'
            $year = Carbon::parse($this->content->created_at)->format('Y');
            $event = $this->content->event ? $this->content->event->slug : 'general';

            // Extract idol from metadata if present, else default
            $metadata = $this->content->metadata ?? [];
            $idol = $metadata['idol'] ?? 'general';

            $folderPath = $driveService->createFolderIfNotExists($year, $idol, $event);

            $fileContents = Storage::disk('local')->get($this->localFilePath);
            $fileName = basename($this->localFilePath);
            $driveFilePath = trim($folderPath, '/') . '/' . $fileName;

            // $fileContents should be a stream ideally, but get() returns string
            // We can stream it using readStream
            $fileStream = Storage::disk('local')->readStream($this->localFilePath);

            $result = $driveService->uploadFile($fileStream, $driveFilePath);

            if (is_resource($fileStream)) {
                fclose($fileStream);
            }

            // Update content record
            $this->content->update([
                'google_drive_file_id' => $result['file_id'],
                'google_drive_url' => $result['url'],
                'status' => 'active',
            ]);

            // Clean up local file
            Storage::disk('local')->delete($this->localFilePath);

            Log::info("Successfully uploaded Content ID: {$this->content->id} to Google Drive.");

        }
        catch (Throwable $e) {
            Log::error("Failed to upload Content ID: {$this->content->id} to Google Drive. Error: " . $e->getMessage());

            // Re-throw so the queue knows it failed and can retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error("Job UploadImageToDriveJob failed permanently for Content ID: {$this->content->id}. Error: " . $exception->getMessage());
        $this->content->update([
            'status' => 'inactive'
        ]);
    }
}
