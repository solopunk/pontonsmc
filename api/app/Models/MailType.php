<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailType extends Model
{
    use HasFactory;

    public function mails(): HasMany
    {
        return $this->hasMany(Mail::class);
    }
}
