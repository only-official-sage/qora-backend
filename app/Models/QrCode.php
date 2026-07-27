<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'section_id', 'table_id', 'code', 'path'];

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($qr) {
            if (empty($qr->id)) {
                $qr->id = (string) Str::uuid();
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

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
