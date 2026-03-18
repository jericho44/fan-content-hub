<?php

namespace App\Http\Controllers\ApiWeb;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Helpers\ResponseFormatter;
use App\Services\EventService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Str;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $search = (string) $request->input('search', '');
        
        $events = $this->eventService->getAll($limit, $search);
        
        return ResponseFormatter::success(
            EventResource::collection($events)->response()->getData(true),
            'Data event list successfully retrieved'
        );
    }

    public function show(string $idHash)
    {
        try {
            $event = $this->eventService->findByIdHash($idHash);
            return ResponseFormatter::success(new EventResource($event), 'Data event successfully retrieved');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Event not found');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only(['name', 'date', 'location', 'description']);
            $data['slug'] = Str::slug($data['name']);

            $event = $this->eventService->create($data);
            
            return ResponseFormatter::success(new EventResource($event), 'Success create event', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to create event');
        }
    }

    public function update(Request $request, string $idHash)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only(['name', 'date', 'location', 'description']);
            $data['slug'] = Str::slug($data['name']);

            $event = $this->eventService->update($idHash, $data);
            
            return ResponseFormatter::success(new EventResource($event), 'Success update event');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to update event');
        }
    }

    public function destroy(string $idHash)
    {
        try {
            $this->eventService->delete($idHash);
            return ResponseFormatter::success(null, 'Success delete event');
        } catch (Exception $e) {
            return ResponseFormatter::error($e, 'Failed to delete event');
        }
    }
}
