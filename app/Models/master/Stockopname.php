<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stockopname extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'stockopnames';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function stockopnameCreateBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }

    public function stockopnameUpdateBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'id');
    }

    public function stockopname_item()
    {
        return $this->hasMany(Stockopname_item::class);
    }
}
