<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomRequestFile extends Model
{
    protected $fillable = [
        'custom_request_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function customRequest(): BelongsTo
    {
        return $this->belongsTo(CustomRequest::class);
    }
}
