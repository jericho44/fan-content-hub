<?php

namespace App\Http\Controllers\ApiWeb;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Helpers\ResponseFormatter;
use App\Services\TagService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Str;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = (string) $request->input('search', '');
        
        $tags = $this->tagService->getAll($limit, $search);
        
        return ResponseFormatter::success(
            TagResource::collection($tags)->response()->getData(true),
            'Data tag list successfully retrieved'
        );
    }

    public function show(string $idHash)
    {
        try {
            $tag = $this->tagService->findByIdHash($idHash);
            return ResponseFormatter::success(new TagResource($tag), 'Data tag successfully retrieved');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Tag not found');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $data = $request->only(['name']);
            $data['slug'] = Str::slug($data['name']);

            $tag = $this->tagService->create($data);
            
            return ResponseFormatter::success(new TagResource($tag), 'Success create tag', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to create tag');
        }
    }

    public function update(Request $request, string $idHash)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $data = $request->only(['name']);
            $data['slug'] = Str::slug($data['name']);

            $tag = $this->tagService->update($idHash, $data);
            
            return ResponseFormatter::success(new TagResource($tag), 'Success update tag');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to update tag');
        }
    }

    public function destroy(string $idHash)
    {
        try {
            $this->tagService->delete($idHash);
            return ResponseFormatter::success(null, 'Success delete tag');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to delete tag');
        }
    }
}
