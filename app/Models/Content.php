<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Content extends Model
{
    use HasFactory, SoftDeletes, HasUUID, Userstamps;

    protected $fillable = [
        'id_hash',
        'title',
        'type',
        'event_id',
        'google_drive_file_id',
        'google_drive_url',
        'metadata',
        'status',
        'view_count',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
