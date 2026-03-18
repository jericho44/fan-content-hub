<?php

namespace App\Http\Controllers\ApiPublic;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentResource;
use App\Helpers\ResponseFormatter;
use App\Services\ContentService;
use Illuminate\Http\Request;

class FanContentController extends Controller
{
    protected $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 12); // slightly larger default for grid displays
        $search = (string) $request->input('search', '');
        $year = $request->input('year');
        $idolSlug = $request->input('idol');
        $eventSlug = $request->input('event');
        
        $contents = $this->contentService->getPublicContents($limit, $search, $year, $idolSlug, $eventSlug);
        
        return ResponseFormatter::success(
            ContentResource::collection($contents)->response()->getData(true),
            'Fan content list successfully retrieved'
        );
    }
}
