<?php

namespace App\Http\Controllers\ApiPublic;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Helpers\ResponseFormatter;
use App\Models\Event;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 6);
        
        $events = Event::orderBy('date', 'desc')->paginate($limit);
        
        return ResponseFormatter::success(
            EventResource::collection($events)->response()->getData(true),
            'Public event list successfully retrieved'
        );
    }
}
