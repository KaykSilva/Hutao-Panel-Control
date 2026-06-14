<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['whatsapp_id', 'name'])]
class WhatsappGroup extends Model
{
    public function members(): HasMany
    {
        return $this->hasMany(WhatsappGroupMember::class);
    }

    public function sharedAccounts(): HasMany
    {
        return $this->hasMany(SharedAccount::class);
    }
}
