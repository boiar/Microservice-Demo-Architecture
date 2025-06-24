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


    public static function getUserOrders()
    {
        $userId = JwtHelper::getUserIdFromToken();

        return self::where('orders.user_id', $userId)
                    ->orderBy('orders.created_at', 'desc')
                    ->get();

    }


    public static function orderById($orderId): ?object
    {
        return self::select('id as order_id', 'address', 'total_price', 'status', 'created_at')
                           ->where('id', $orderId)
                           ->first();

    }
}
