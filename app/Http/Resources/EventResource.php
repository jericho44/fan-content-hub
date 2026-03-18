<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'date' => $this->date ? \Carbon\Carbon::parse($this->date)->format('Y-m-d') : null,
            'location' => $this->location,
            'city' => $this->city,
            'description' => $this->description,
            'externalGalleryUrl' => $this->external_gallery_url,
            'thumbnailUrl' => $this->contents()->whereNotNull('google_drive_url')->orderBy('created_at', 'desc')->first()?->google_drive_url,
            'contentCount' => $this->contents()->count(),
            'createdAt' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updatedAt' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
