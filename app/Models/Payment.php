<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['id'];

    const STRIPE = 'stripe';
    const PAYPAL = 'paypal';
}
