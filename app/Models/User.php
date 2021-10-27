<?php

namespace App\Models;

use App\Models\master\Assets;
use App\Models\master\Employee;
use App\Models\master\Stockopname;
use App\Models\master\Category;
use App\Models\master\Vendor;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function category()
    {
        return $this->hasMany(Category::class);
    }

    public function assets()
    {
        return $this->hasMany(Assets::class);
    }

    public function stockopname()
    {
        return $this->hasMany(Stockopname::class);
    }

    public function vendor()
    {
        return $this->hasMany(Vendor::class);
    }

    public function employee()
    {
        return $this->hasMany(Employee::class);
    }
}
