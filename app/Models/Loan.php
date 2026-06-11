<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = ['user_id', 'book_id', 'loan_date', 'due_date', 'status', 'quantity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function returnRecord()
    {
        return $this->hasOne(BookReturn::class, 'loan_id');
    }
}
