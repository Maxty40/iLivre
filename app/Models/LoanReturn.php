<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanReturn extends Model
{
    // Explicitly binding model to the 'returns' table since class name differs
    protected $table = 'returns';

    protected $fillable = ['loan_id', 'actual_return_date', 'fine'];

    /**
     * Relationship: A return record points back to its original loan transaction.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
