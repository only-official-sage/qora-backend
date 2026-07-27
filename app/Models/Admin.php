<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'email',
        'password',
        'business_name',
        'business_type',
        'logo',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($admin) {
            if (empty($admin->id)) {
                $admin->id = Str::uuid()->toString();
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'api_token', // Used for token-based authentication
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the admin's address.
     */
    public function address()
    {
        return $this->hasOne(AdminAddress::class);
    }

    /**
     * Get the admin's payment information.
     */
    public function payment()
    {
        return $this->hasOne(AdminPayment::class);
    }

    public function staffs()
    {
        return $this->hasMany(Staff::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function dishCategories()
    {
        return $this->hasMany(DishCategory::class);
    }

    public function beverages()
    {
        return $this->hasMany(Beverage::class);
    }

    public function beverageCategories()
    {
        return $this->hasMany(BeverageCategory::class);
    }
}
