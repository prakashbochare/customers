<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model {

    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_id',
        'amount',
        'emi_no',
        'status',
        'date'
    ];
    protected $dates = ['created_at', 'updated_at'];

}
