<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomRequest extends Model
{
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_DISCUSSION = 'in_discussion';
    public const STATUS_QUOTED = 'quoted';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'request_number',
        'neon_text',
        'description',
        'width_cm',
        'height_cm',
        'color',
        'quantity',
        'deadline_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CustomRequestFile::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
