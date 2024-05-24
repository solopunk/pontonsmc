<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MemberType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class);
    }
}
