<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['bot_contact_id', 'direction', 'message_type', 'content', 'payload', 'external_id'])]
class BotMessage extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(BotContact::class, 'bot_contact_id');
    }
}
