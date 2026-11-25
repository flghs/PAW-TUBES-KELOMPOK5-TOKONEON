<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    public const STATUS_WAITING_STAFF = 'waiting_staff';
    public const STATUS_ASSIGNED_STAFF = 'assigned_staff';
    public const STATUS_ESCALATED_ADMIN = 'escalated_admin';
    public const STATUS_ASSIGNED_ADMIN = 'assigned_admin';
    public const STATUS_RETURNED_STAFF = 'returned_staff';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    // Status, penanggung jawab, dan waktu hanya diubah oleh kode server kita,
    // jangan pernah mengisinya langsung dari $request->all().
    protected $fillable = [
        'customer_id',
        'custom_request_id',
        'subject',
        'status',
        'handler_id',
        'last_staff_id',
        'last_message_at',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function customRequest(): BelongsTo
    {
        return $this->belongsTo(CustomRequest::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    public function lastStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_staff_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(ConversationRead::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ConversationStatusLog::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
