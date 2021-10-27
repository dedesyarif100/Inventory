<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\master\Asset;
use Illuminate\Http\Request;

class API_AssetController extends Controller
{
    public function getAsset(Request $request)
    {
        $getAsset = Asset::where('code', $request->code)->first();
        if ($getAsset == null) {
            return response()->json([
                "error" => false,
                "message" => "Code asset tidak terdefinisi",
            ]);
        } else {
            return response()->json([
                "success" => true,
                "message" => "Request berhasil",
                "data" => $getAsset,
            ]);
        }
    }
}
