<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerRequest extends Model
{
    protected $table = 'requests';
    
    protected $fillable = ['admin_id', 'table_id', 'type', 'status', 'note', 'payload'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
        'payload' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->id)) {
                $request->id = (string) Str::uuid();
            }
        });
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
