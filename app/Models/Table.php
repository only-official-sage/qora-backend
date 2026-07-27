<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Table extends Model
{
    protected $fillable = ['admin_id', 'section_id', 'name', 'status', 'capacity'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'status' => 'string',
        'capacity' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            if (empty($table->id)) {
                $table->id = (string) Str::uuid();
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function requests()
    {
        return $this->hasMany(CustomerRequest::class);
    }
}
