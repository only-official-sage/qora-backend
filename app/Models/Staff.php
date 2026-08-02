<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Staff extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';
     protected $table = 'staffs'; 
    protected $fillable = [
        'id',
        'admin_id',
        'fname',
        'lname',
        'email',
        'role',
        'schedule',
    ];

    protected $casts = [
        'schedule' => 'array',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($staff) {
    //         if (empty($staff->id)) {
    //             $staff->id = Str::uuid()->toString();
    //         }
    //     });
    // }


  

protected static function boot()
{
    parent::boot();
    static::creating(function ($staff) {
        if (empty($staff->id)) {
            $staff->id = Str::random(9); // 9-character random alphanumeric string
        }
    });
}




    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}






