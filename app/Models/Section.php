<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Section extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'admin_id',
        'name',
        'type',
        'icon',
        'status',
    ];

    protected $casts = [
        'icon' => 'json',
        'status' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($section) {
            if (empty($section->id)) {
                $section->id = Str::uuid()->toString();
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
