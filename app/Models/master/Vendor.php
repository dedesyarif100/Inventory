<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;
    public function vendorCreateBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    public function vendorUpdateBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }

    public function category()
    {
        return $this->hasMany(Category::class);
    }
}
