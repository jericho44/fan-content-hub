<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_hash,
            'title' => $this->title,
            'type' => $this->type,
            'google_drive_url' => $this->google_drive_url,
            'metadata' => $this->metadata,
            'status' => $this->status,
            'view_count' => $this->view_count,
            'event' => new EventResource($this->whenLoaded('event')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
