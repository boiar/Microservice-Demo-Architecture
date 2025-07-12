<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $fillable = ['product_id', 'quantity', 'price'];


}
