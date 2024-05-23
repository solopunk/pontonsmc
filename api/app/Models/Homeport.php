<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Homeport extends Model
{
    use HasFactory;

    public function boats(): HasMany
    {
        return $this->hasMany(Boat::class);
    }
}
