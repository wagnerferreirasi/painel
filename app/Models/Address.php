<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'zipcode',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'uf'
    ];

    public function user(): HasOne
    {
        return $this->HasOne(User::class);
    }
}
