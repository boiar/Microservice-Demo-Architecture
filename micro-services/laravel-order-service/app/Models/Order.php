<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtHelper;


class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'total_price', 'address', 'notes'];


    // Status constants
    public const STATUS_PENDING   = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';


}
