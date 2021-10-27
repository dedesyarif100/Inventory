<?php

namespace App\Observers;

use App\Models\master\Stockopname;
use App\Models\master\Stockopname_item;
use App\Models\master\Asset;

class StockopnameObserver
{
    /**
     * Handle the Stockopname "created" event.
     *
     * @param  \App\Models\master\Stockopname  $stockopname
     * @return void
     */
    public function created(Stockopname $stockopname)
    {
        $assets = Asset::all();
        $items = collect();
        foreach ($assets as $asset) {
            $items->push([
                'stockopname_id' => $stockopname->id,
                'asset_id' => $asset->id,
                'created_by' => $stockopname->created_by,
                'updated_by' => $stockopname->updated_by,
                'created_at' => $stockopname->created_at,
                'updated_at' => $stockopname->updated_at
            ]);
        }
        Stockopname_item::insert($items->toArray());
    }

    /**
     * Handle the Stockopname "updated" event.
     *
     * @param  \App\Models\master\Stockopname  $stockopname
     * @return void
     */
    public function updated(Stockopname $stockopname)
    {
        //
    }

    /**
     * Handle the Stockopname "deleted" event.
     *
     * @param  \App\Models\master\Stockopname  $stockopname
     * @return void
     */
    public function deleted(Stockopname $stockopname)
    {
        //
    }

    /**
     * Handle the Stockopname "restored" event.
     *
     * @param  \App\Models\master\Stockopname  $stockopname
     * @return void
     */
    public function restored(Stockopname $stockopname)
    {
        //
    }

    /**
     * Handle the Stockopname "force deleted" event.
     *
     * @param  \App\Models\master\Stockopname  $stockopname
     * @return void
     */
    public function forceDeleted(Stockopname $stockopname)
    {
        //
    }
}
