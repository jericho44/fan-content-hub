<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Event extends Model
{
    use HasFactory, SoftDeletes, HasUUID, Userstamps;

    protected $fillable = [
        'id_hash',
        'name',
        'slug',
        'date',
        'location',
        'city',
        'description',
        'external_gallery_url',
    ];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
