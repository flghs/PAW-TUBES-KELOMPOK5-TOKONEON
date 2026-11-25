<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationStatusLog extends Model
{
    protected $fillable = [
        'conversation_id',
        'changed_by',
        'action',
        'from_status',
        'to_status',
        'from_handler_id',
        'to_handler_id',
        'reason',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function fromHandler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_handler_id');
    }

    public function toHandler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_handler_id');
    }
}
