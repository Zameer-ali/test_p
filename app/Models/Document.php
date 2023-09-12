<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'extension'
    ];
    protected $appends = [
        'path_url',
    ];

    public function getPathUrlAttribute()
    {
        return  url('/') . '/uploads/' . $this->user_id . "/" . $this->path;
    }
}
