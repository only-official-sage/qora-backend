<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Admin;

class Order extends Model
{
    protected $fillable = ['admin_id', 'staff_id', 'table_id', 'order', 'status', 'report_status', 'report_reason'];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'order' => 'array',
        'status' => 'integer',
        'report_status' => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
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
