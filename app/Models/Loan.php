<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'status',
        'quantity'
    ];

    /**
     * Relationship: A loan belongs to a single user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A loan belongs to a specific book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Relationship: A loan has one specific return detail record (1:1).
     */
    public function loanReturn(): HasOne
    {
        // Explicitly defining foreign key due to specific model naming mapping to 'returns' table
        return $this->hasOne(LoanReturn::class, 'loan_id');
    }
}
