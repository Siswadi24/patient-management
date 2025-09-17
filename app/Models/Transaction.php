<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_transaction_id',
        'name_transaction',
        'amount_transaction',
        'description_transaction',
        'transaction_date',
        'transaction_time',
        'photo_transaction',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoryTransaction()
    {
        return $this->belongsTo(CategoryTransaction::class);
    }
}
