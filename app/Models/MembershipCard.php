<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipCard extends Model
{
    protected $fillable = ['card_number', 'issued_date', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
