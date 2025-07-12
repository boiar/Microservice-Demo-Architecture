<?php

namespace App\Models;

use App\Helpers\JwtHelper;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',

    ];


}
