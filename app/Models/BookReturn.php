<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookReturn extends Model
{
    protected $table = 'returns';
    
    protected $fillable = ['loan_id', 'actual_return_date', 'fine'];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
