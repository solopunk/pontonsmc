<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'first',
        'last',
        'birthdate',
        'address',
        'postal_code',
        'city',
        'phone',
        'job',
        'pending',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function boat(): HasOne
    {
        return $this->hasOne(Boat::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function member_types(): BelongsToMany
    {
        return $this->belongsToMany(MemberType::class);
    }
}
