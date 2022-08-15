<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model {

    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'term',
        'status',
        'paid_loan_status',
        'date'
    ];
    protected $dates = ['created_at', 'updated_at'];

}
