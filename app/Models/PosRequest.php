<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PosRequest extends Model
{
    protected $fillable = ['admin_id', 'order_id', 'amount', 'status', 'note'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pos) {
            if (empty($pos->id)) {
                $pos->id = (string) Str::uuid();
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
