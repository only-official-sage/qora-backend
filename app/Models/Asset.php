<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Asset extends Model
{
    protected $fillable = ['admin_id', 'type', 'path', 'mime_type', 'size'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (empty($asset->id)) {
                $asset->id = (string) Str::uuid();
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
