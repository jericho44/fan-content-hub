<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Wildside\Userstamps\Userstamps;

class Tag extends Model
{
    use HasFactory, SoftDeletes, HasUUID, Userstamps;

    protected $fillable = [
        'id_hash',
        'name',
        'slug',
    ];

    public function contents()
    {
        return $this->belongsToMany(Content::class);
    }
}
