<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'publisher', 'stock'];

    /**
     * Relationship: One book can have multiple loan transactions.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
