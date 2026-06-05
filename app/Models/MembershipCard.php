<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipCard extends Model
{
    protected $fillable = ['card_number', 'issued_date', 'user_id'];

    /**
     * Relationship: A membership card belongs strictly to one user (1:1 inverse).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
