<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopOrder extends Model
{
    protected $fillable = ['admin_id', 'user_id', 'items', 'total', 'status', 'payment_method'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'items' => 'array',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->id)) {
                $order->id = (string) Str::uuid();
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
