<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockopname_item extends Model
{
    use HasFactory;
    protected $table = 'stockopname_items';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function stockopname()
    {
        return $this->belongsTo(Stockopname::class, 'stockopname_id', 'id');
    }

    /**
     * Get all of the assets for the Stockopname_item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }
}
