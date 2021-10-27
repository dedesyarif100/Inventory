<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    // protected $fillable = ['name'];

    public function categoryCreateBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    public function categoryUpdateBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
