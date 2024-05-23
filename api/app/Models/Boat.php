<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Boat extends Model
{
    use HasFactory;


    public function coowner(): HasOne
    {

        return $this->hasOne(Coowner::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function boat_type(): BelongsTo
    {
        return $this->belongsTo(BoatType::class);
    }

    public function homeport(): BelongsTo
    {
        return $this->belongsTo(Homeport::class);
    }
}
