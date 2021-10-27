<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\master\Stockopname_item;
use App\Models\master\Stockopname;
use App\Models\master\Log;
use App\Models\master\Asset;
use App\Models\master\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\FunctionHelper;
use Carbon\Carbon;

class API_StockopnameController extends Controller
{
    public function getStockopname(Request $request)
    {
        $stockopname = Stockopname::where('code', $request->code)->first();
        if ($stockopname != null) {
            $items = Stockopname_item::with('assets')->where('stockopname_id', $stockopname->id)->get();
            return response()->json([
                "success" => true,
                "message" => "Request berhasil",
                "data" => $items
            ], 200);
        } else {
            return response()->json([
                "error" => false,
                "message" => "Kode Stockopname tidak terdefinisi",
            ], 400);
        }
    }

    public function postStockopname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => ['required'],
            'employee_id' => $request->type == FunctionHelper::DIKEMBALIKAN || $request->type == FunctionHelper::DIPINJAMKAN ? ['required'] : '',
        ]);

        if ($request->type == 1) {
            $cek = 'Dikembalikan';
        } else if ($request->type == 2) {
            $cek = 'Dipinjamkan';
        } else if ($request->type == 3) {
            $cek = 'Service';
        } else if ($request->type == 4) {
            $cek = 'Rusak';
        } else if ($request->type == 5) {
            $cek = 'Hilang';
        } else if ($request->type == 6) {
            $cek = 'Keluar';
        } else if ($request->type == 7) {
            $cek = 'Hibah';
        } else if ($request->type == 8) {
            $cek = 'Beli';
        } else if ($request->type == 9) {
            $cek = 'Jual';
        } else if ($request->type == 10) {
            $cek = 'Stock Awal';
        } else if ($request->type == 11 || $request->type == null) {
            $cek = 'Normal';
        }

        $asset = Asset::find($request->asset_id);

        if($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $getAsset = Asset::where('id', $request->asset_id)->first();
            $getEmployee = Employee::where('Id', $request->employee_id)->first();
            if ( $getAsset == null ) {
                return response()->json([
                    "error" => false,
                    "message" => "Asset Tidak terdefinisi",
                ], 400);
            }
            if (Employee::where('Id', $request->employee_id)->doesntExist()) {
                return response()->json([
                    "error" => false,
                    "message" => "Employee tidak terdaftar",
                ]);
            }

            $log = new Log();
            $log->date = Carbon::now();
            $log->qrcode = uniqid();
            $log->asset_id = $request->asset_id;
            $log->type = $request->type == null ? 11 : $request->type;
            $log->notes = $request->notes == null ? '' : $request->notes;

            if ( ($request->employee_id == null) && ($request->quantity == null) && ($request->type == null) ) {
                Stockopname_item::where('asset_id', $request->asset_id)->update([
                    'checked' => 1,
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "Asset Berhasil di keep",
                    "data" => [
                        "asset code" => $getAsset->code,
                        "asset name" => $getAsset->name,
                        // "employee" => $getEmployee->name ?? null,
                        "type" => $cek,
                    ]
                ], 200);
            }

            else if ($request->type == FunctionHelper::DIKEMBALIKAN || $request->type == FunctionHelper::HIBAH || $request->type == FunctionHelper::BELI || $request->type == null) {
                if ($request->type == FunctionHelper::DIKEMBALIKAN) {
                    $log->employee_id = $request->employee_id;
                }

                // if ($asset->quantity < $request->quantity) {
                //     return response()->json([
                //         "error" => false,
                //         "message" => "Barang tidak cukup",
                //         "data" => [
                //             "asset" => $getAsset->name,
                //             "employee" => $getEmployee->name ?? null,
                //             "already quantity" => $asset->quantity,
                //             "type" => $cek,
                //         ]
                //     ], 400);
                // }
                if ($request->quantity == 0) {
                    return response()->json([
                        "error" => false,
                        "message" => "Quantity tidak boleh di input angka 0",
                        "data" => [
                            "asset code" => $getAsset->code,
                            "asset name" => $getAsset->name,
                        ]
                    ], 400);
                }

                $log->qty_in = $request->quantity;
                $log->qty_out = 0;

                Stockopname_item::where('asset_id', $request->asset_id)->update([
                    'checked' => 1,
                ]);

                Asset::where('id', $request->asset_id)->update([
                    'quantity' => $asset->quantity + $request->quantity,
                ]);
            } else {
                if ($request->type == FunctionHelper::DIPINJAMKAN) {
                    $log->employee_id = $request->employee_id;
                }

                if ($asset->quantity < $request->quantity) {
                    return response()->json([
                        "error" => false,
                        "message" => "Barang tidak cukup",
                        "data" => [
                            "asset code" => $getAsset->code,
                            "asset name" => $getAsset->name,
                            "request quantity" => $request->quantity,
                            "already quantity" => $asset->quantity,
                        ]
                    ], 400);
                } else if ($request->quantity == 0) {
                    return response()->json([
                        "error" => false,
                        "message" => "Quantity tidak boleh di input angka 0",
                        "data" => [
                            "asset code" => $getAsset->code,
                            "asset name" => $getAsset->name,
                        ]
                    ], 400);
                }
                $log->qty_in = 0;
                $log->qty_out = $request->quantity;

                Stockopname_item::where('asset_id', $request->asset_id)->update([
                    'checked' => 1,
                ]);

                Asset::where('id', $request->asset_id)->update([
                    'quantity' => $asset->quantity - $request->quantity,
                ]);
            }
        }

        $query = $log->save();
        if ($query) {
            return response()->json([
                "success" => true,
                "message" => "Request berhasil ditambahkan",
                "data" => [
                    "asset code" => $getAsset->code,
                    "asset name" => $getAsset->name,
                    "employee" => $getEmployee->name ?? null,
                    "request quantity" => $request->quantity,
                    "type" => $cek,
                ]
            ], 200);
        }
    }

    public function nonaktifStockopname(Request $request)
    {
        if (Stockopname::where('code', $request->code)->doesntExist()) {
            return response()->json([
                "error" => false,
                "message" => "code stockopname tidak terdefinisi",
                "data" => [
                    "code stockopname" => $request->code,
                ]
            ], 400);
        }

        if ( !is_numeric($request->status) ) {
            return response()->json([
                "error" => false,
                "message" => "Inputan harus integer",
                "data" => [
                    "code stockopname" => $request->code,
                ]
            ], 400);
        }

        if ($request->status == 0 || $request->status == 1) {
            $query = Stockopname::where('code', $request->code)->update([
                'status' => $request->status,
            ]);
            if ($request->status == 0) {
                $request->status = 'Nonaktif';
            } else {
                $request->status = 'Aktif';
            }

            if ($query) {
                return response()->json([
                    "success" => true,
                    "message" => "Status berhasil diubah",
                    "data" => [
                        "code stockopname" => $request->code,
                        "status" => $request->status,
                    ]
                ], 200);
            }
        } else {
            return response()->json([
                "error" => false,
                "message" => "Inputan harus angka 0 atau 1",
                "data" => [
                    "code stockopname" => $request->code,
                ]
            ], 400);
        }
    }
}
