<?php

namespace App\Http\Controllers\Master;

use App\Exports\AssetsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Category;
use App\Models\Master\Vendor;
use App\Models\Master\Asset;
use App\Models\Master\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Imports\AssetsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\FunctionHelper;

class MsAssetsController extends Controller
{
    public function editorAssets(Request $request)
    {
        $assets = Asset::find($request->assets_id);
        $logs = Log::where('asset_id', $request->assets_id)->count();
        $categories = Category::all();
        $vendors = Vendor::all();

        return view('ms_assets.editor', compact('assets', 'logs', 'categories', 'vendors'));
    }

    public function index()
    {
        return view('ms_assets.index');
    }

    public function getAssets()
    {
        $assets = Asset::orderBy('id', 'DESC')->get();
        return DataTables::of($assets)
        ->addIndexColumn()
        ->editColumn('vendor', function ($assets) {
            return $assets->vendor->name;
        })
        // ->editColumn('quantity', function ($assets) {
        //     $log =  Log::where('asset_id', 1)->orderBy('id', 'DESC')->get();
        //     $assetQuantity = Asset::where('id', $log)->get();
        //     dd($assetQuantity);
        //     if ($log->type == FunctionHelper::DIKEMBALIKAN || $log->type == FunctionHelper::BELI || $log->type == FunctionHelper::HIBAH) {
        //         return '<div style="background-color: #bfdeff;"> '.$assetQuantity->quantity.' </div>';
        //     } else if ($log->type == FunctionHelper::STOCK_AWAL) {
        //         return '<div style="background-color: green;"> '.$assets->quantity.' </div>';
        //     }
        // })
        ->editColumn('quantity', function ($assets) {
            if ($assets->quantity == 0) {
                return '<div style="background-color: #ff9999;"> '.$assets->quantity.' </div>';
            } else {
                return $assets->quantity;
            }
        })
        ->editColumn('status', function ($assets) {
            if ($assets->status == 1) {
                return '<span class="name badge bg-success" style="color: white;"> Normal </span>';
            } else {
                return '<span class="name badge bg-danger" style="color: white;"> Rusak </span>';
            }
        })
        ->editColumn('notes', function ($assets) {
            return $assets->notes ?? '--';
        })
        ->addColumn('action', function ($assets) {
            $logs = Log::where('asset_id', $assets['id'])->count();
            // dd($logs);
            $action = '<div class="btn-group" role="group" aria-label="Basic example">  <a href="'.route('getResponseShowCode.assets', $assets['id']).'" class="btn btn-info btn-sm" id="show" title="show"> <i class="fas fa-eye"></i> </a>';
            $action .= '<button class="btn btn-primary btn-sm" data-id="'.$assets['id'].'" id="edit" title="Edit"><i class="fas fa-edit"></i></button>';
            if ($logs > 1) {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$assets['id'].'" id="delete" title="Delete" disabled><i class="fas fa-trash"></i></button>';
            } else {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$assets['id'].'" id="delete" title="Delete"><i class="fas fa-trash"></i></button>';
            }
            // $action .= '<button class="btn btn-primary btn-sm" data-id="'.$assets['id'].'" id="log" title="Log"><i class="fa fa-history"></i></button> </div>';
            return $action;
        })
        ->rawColumns(['quantity', 'status', 'action'])
        ->make(true);
    }

    public function sendToDB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => ['required'],
            'name' => ['required'],
            'vendor_id' => ['required'],
            'quantity' => ['required'],
            'buy_at' => ['required'],
        ]);

        if($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            if ($request->same_qrcode == 0) {
                $request->same_qrcode = false;
            } else {
                $request->same_qrcode = true;
            }

            if ($request->same_qrcode == false) {
                // dd($request->same_qrcode);

                // Jika same_qrcode = false, maka akan otomatis membuat asset dengan qrcode yang berbeda-beda
                for ($val = 1; $val <= $request->quantity; $val++) {
                    $x = Asset::where('category_id', $request->category_id)->orderBy('code', 'DESC')->first();
                    if (empty($x)) {
                        $convert = $request->code;
                        $save = 0;
                        $generate = explode('.', $convert);
                        $save++;
                        array_push($generate, Str::padLeft($save, 4, '0'));
                        $generate[2] = Str::padLeft($save, 4, '0');
                    } else {
                        $convert = $x->code;
                        $generate = explode('.', $convert);
                        $save = intval($generate[2]) + 1;
                        $generate[2] = Str::padLeft($save, 4, '0');
                    }

                    $data = [
                        [
                            'category_id' => $request->category_id,
                            'code' => implode('.', $generate),
                            'name' => $request->name,
                            'vendor_id' => $request->vendor_id,
                            'quantity' => $request->quantity / $request->quantity,
                            'buy_at' => $request->buy_at,
                            'employee_id' => 20,
                            'status' => 1,
                            'notes' => $request->notes ?? null,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                        ],
                    ];
                    $query[] = Asset::insert($data);

                    // Log Assets >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
                    $log_asset = [
                        [
                            'date' => $request->buy_at,
                            'qrcode' => uniqid(),
                            'asset_id' => Asset::all()->last()->id,
                            'type' => 10,
                            'employee_id' => null,
                            'qty_in' => $request->quantity / $request->quantity,
                            'qty_out' => 0,
                            'notes' => 'stock awal',
                            'created_at' => $request->created_at,
                            'updated_at' => $request->updated_at
                        ],
                    ];
                    Log::insert($log_asset);
                }

            } else {
                // dd($request->same_qrcode);

                // Jika same_qrcode = true, maka akan otomatis membuat asset dengan qrcode yang sama
                $x = Asset::where('category_id', $request->category_id)->orderBy('code', 'DESC')->first();
                if (empty($x)) {
                    $convert = $request->code;
                    $save = 0;
                    $generate = explode('.', $convert);
                    $save++;
                    array_push($generate, Str::padLeft($save, 4, '0'));
                    $generate[2] = Str::padLeft($save, 4, '0');
                } else {
                    $convert = $x->code;
                    $generate = explode('.', $convert);
                    // dd($convert);
                    $save = intval($generate[2]) + 1;
                    $generate[2] = Str::padLeft($save, 4, '0');
                }

                $data = [
                    'category_id' => $request->category_id,
                    'code' => implode('.', $generate),
                    'name' => $request->name,
                    'vendor_id' => $request->vendor_id,
                    'quantity' => $request->quantity,
                    'buy_at' => $request->buy_at,
                    'employee_id' => 1,
                    'status' => 1,
                    'notes' => $request->notes ?? null,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ];
                $query[] = Asset::insert($data);

                // dd(Asset::all()->last()->id);
                // dd($query['created_at']);
                // Log Assets >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
                $log_asset = [
                    [
                        'date' => $request->buy_at,
                        'qrcode' => uniqid(),
                        'asset_id' => Asset::all()->last()->id,
                        'type' => 10,
                        'employee_id' => null,
                        'qty_in' => $request->quantity,
                        'qty_out' => 0,
                        'notes' => 'stock awal',
                        'created_at' => $request->created_at,
                        'updated_at' => $request->updated_at
                    ],
                ];
                Log::insert($log_asset);
            }

            if (!$query) {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            } else {
                return response()->json(['code' => 1, 'msg' => 'New Assets has been successfuly saved']);
            }
        }
    }

    public function updateAssets(Request $request, int $assets_id)
    {
        // dd($request->category_id);
        $assets_id = $request->id;
        $validator = Validator::make($request->all(), [
            'category_id' => ['required'],
            'name' => ['required'],
            'vendor_id' => ['required'],
            'buy_at' => ['required'],
        ]);
        // dd($request->code);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $x = Asset::where('category_id', $request->category_id)->orderBy('code', 'DESC')->first();
            // $logs = Log::where('asset_id', $request->id)->count();
            if ($x == null) {
                // dd($x);
                $convert = $request->code;
                $generate = explode('.', $convert);
                $update = Asset::where('category_id', $request->category_id)->count();
                $update++;
                $generate[2] = Str::padLeft($update, 4, '0');
                $query = Asset::where('id', $assets_id)->update([
                    'category_id' => $request->category_id,
                    'code' => implode('.', $generate),
                    'name' => $request->name,
                    'vendor_id' => $request->vendor_id,
                    'quantity' => $request->quantity,
                    'buy_at' => $request->buy_at,
                    'notes' => $request->notes ?? null,
                    'updated_by' => Auth::id(),
                ]);
            } else {
                // dd($request->category_update);
                // dd (Asset::where('category_id', $request->category_id)->first());
                // dd($generate);
                if ($request->category_id == $request->category_update) {
                    $convert = $request->code;
                    $generate = explode('.', $convert);
                    $save = intval($generate[2]);
                } else {
                    $convert = $x->code;
                    $generate = explode('.', $convert);
                    $save = intval($generate[2]) + 1;
                }
                $generate[2] = Str::padLeft($save, 4, '0');
                $query = Asset::where('id', $assets_id)->update([
                    'category_id' => $request->category_id,
                    'code' => implode('.', $generate),
                    'name' => $request->name,
                    'vendor_id' => $request->vendor_id,
                    'quantity' => $request->quantity,
                    'buy_at' => $request->buy_at,
                    'notes' => $request->notes ?? null,
                    'updated_by' => Auth::id(),
                ]);
                // Log::where('asset_id', $request->assets_id)->count() > 0 ? '' :
                // dd($query);

                // Log Assets >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
                Log::where('id', $assets_id)->update([
                    'date' => $request->buy_at,
                    'asset_id' => $assets_id,
                    'qty_in' => $request->quantity,
                    'qty_out' => 0,
                    'notes' => $request->quantity,
                    'updated_at' => $request->updated_at
                ]);
                // $log_asset = [
                //     [
                //         'date' => $request->buy_at,
                //         'qrcode' => uniqid(),
                //         'asset_id' => Asset::all()->last()->id,
                //         'type' => 10,
                //         'employee_id' => null,
                //         'qty_in' => $request->quantity,
                //         'qty_out' => 0,
                //         'notes' => 'stock awal',
                //         'created_at' => $request->created_at,
                //         'updated_at' => $request->updated_at
                //     ],
                // ];
                // Log::insert($log_asset);

                // $log_asset = [
                //     [
                //         'type' => 1,
                //         'qty_in' => $request->quantity,
                //         'qty_out' => 0,
                //         'employee_id' => 1,
                //         'notes' => 'tet',
                //         'created_by' => Auth::id(),
                //         'updated_by' => Auth::id(),
                //     ],
                // ];
                // $query_log_asset = LogAsset::updateAssets($log_asset);
            }

            if ($query) {
                return response()->json(['code' => 1, 'msg' => 'Assets Has Been Updated']);
            } else {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            }
        }
    }

    public function getResponseShowCode(Request $request, $id)
    {
        $assets = Asset::find($request->id);
        return view('ms_assets.viewbarcode', compact('assets'));
    }

    public function deleteAssets(Request $request)
    {
        $assets_id = $request->assets_id;
        $query = Asset::find($assets_id)->delete();

        if($query) {
            Log::where('id', $assets_id)->delete();
            return response()->json(['code' => 1, 'msg' => 'Assets Has Been Deleted From Databases']);
        } else {
            return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
        }
    }

    public function logAsset(Request $request)
    {

    }

    public function pageprint_qrcode()
    {
        $loop = 0;
        $assets = Asset::all();
        return view('ms_assets.print_qrcode', compact('loop', 'assets'));
    }

    public function getAllQRCode()
    {
        $assets = Asset::all();
        return $assets;
    }

    public function exportTemplate()
    {
        return Excel::download(new AssetsExport, 'assets.xlsx');
    }

    public function importAssets(Request $request)
    {
        // $import = new AssetsImport;
        // dd($import->getRowCount());
        $validator = Validator::make(
            [
                'file'      => $request->file,
                'extension' => strtolower($request->file->getClientOriginalExtension()),
            ],
            [
                'file'          => 'required',
                'extension'      => 'required|in:xlsx, xls',
            ]
        );

        if ($validator->fails()) {
            // dd($validator);
            return back()->withStatus('Failed Import, Format must be xlsx / xls');
        } else {
            $file = $request->file('file');
            Excel::import(new AssetsImport, $file);
            // $cek = Excel::toCollection(new AssetsImport, $file);
            // dd($cek);
            if ($request->file('file') == null) {
                return back()->withStatus('Failed Import');
            } else {
                return back()->withStatus('Success Import');
            }
        }
    }
}
