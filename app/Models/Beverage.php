<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Beverage extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'admin_id',
        'category_id',
        'name',
        'price',
        'description',
        'ingredients',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'ingredients' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(BeverageCategory::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($beverage) {
            if (empty($beverage->id)) {
                $beverage->id = Str::uuid()->toString();
            }
        });
    }
}
