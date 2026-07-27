<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dish extends Model
{
    protected $fillable = ['admin_id', 'category_id', 'name', 'price', 'description', 'ingredients', 'image'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'ingredients' => 'array',
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(DishCategory::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
