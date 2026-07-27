<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BeverageCategory extends Model
{
    protected $fillable = ['admin_id', 'name'];

    public $incrementing = false;

    protected $keyType = 'string';

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function beverages()
    {
        return $this->hasMany(Beverage::class);
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
