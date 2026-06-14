<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shared_account_id', 'whatsapp_group_member_id', 'amount_cents', 'paid_amount_cents', 'paid_at'])]
class SharedAccountParticipant extends Model
{
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(SharedAccount::class, 'shared_account_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(WhatsappGroupMember::class, 'whatsapp_group_member_id');
    }
}
