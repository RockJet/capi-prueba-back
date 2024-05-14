<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    /**
     * Eloquent Relationship: Phone numbers that belong to project.
     */

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    /**
     * Eloquent Relationship: emails that belong to project.
     */

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Eloquent Relationship: addresses that belong to project.
     */

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
