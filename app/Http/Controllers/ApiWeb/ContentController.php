<?php

namespace App\Http\Controllers\ApiWeb;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Helpers\ResponseFormatter;
use App\Services\ContentService;
use Illuminate\Http\Request;
use Exception;

class ContentController extends Controller
{
    protected $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = (string) $request->input('search', '');
        
        $contents = $this->contentService->getAll($limit, $search);
        
        return ResponseFormatter::success(
            ContentResource::collection($contents)->response()->getData(true),
            'Data content list successfully retrieved'
        );
    }

    public function show(string $idHash)
    {
        try {
            $content = $this->contentService->findByIdHash($idHash);
            return ResponseFormatter::success(new ContentResource($content), 'Data content successfully retrieved');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Content not found');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'event_id' => 'required|exists:events,id_hash',
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,mp4|max:51200', // 50MB max per file
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id_hash',
            'metadata' => 'nullable|array'
        ]);

        try {
            $data = $request->except('files');
            
            // Resolve event_id hash to real ID
            $event = \App\Models\Event::where('id_hash', $data['event_id'])->firstOrFail();
            $data['event_id'] = $event->id;
            
            // Resolve tag hashes to real IDs
            if (isset($data['tags'])) {
                $data['tags'] = \App\Models\Tag::whereIn('id_hash', $data['tags'])->pluck('id')->toArray();
            }
            
            $files = $request->file('files');
            $contents = [];

            foreach ($files as $file) {
                // Queue the upload to Google Drive using the service
                $contents[] = $this->contentService->processUpload($data, $file);
            }
            
            return ResponseFormatter::success(
                ContentResource::collection($contents), 
                count($contents) . ' contents upload started', 
                201
            );
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to process content upload');
        }
    }

    public function update(Request $request, string $idHash)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,active,inactive',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id_hash',
            'metadata' => 'nullable|array'
        ]);

        try {
            $data = $request->only(['title', 'status', 'tags', 'metadata']);

            // Resolve tag hashes to real IDs
            if (isset($data['tags'])) {
                $data['tags'] = \App\Models\Tag::whereIn('id_hash', $data['tags'])->pluck('id')->toArray();
            }

            $content = $this->contentService->update($idHash, $data);
            
            return ResponseFormatter::success(new ContentResource($content), 'Success update content');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to update content');
        }
    }

    public function destroy(string $idHash)
    {
        try {
            $this->contentService->delete($idHash);
            return ResponseFormatter::success(null, 'Success delete content');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to delete content');
        }
    }
}
