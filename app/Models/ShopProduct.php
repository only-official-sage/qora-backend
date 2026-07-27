<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopProduct extends Model
{
    protected $fillable = ['admin_id', 'name', 'description', 'price', 'image', 'stock'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->id)) {
                $product->id = (string) Str::uuid();
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
