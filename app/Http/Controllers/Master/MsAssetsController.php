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
use App\Models\master\Asset_item;

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
        $log = Log::where('asset_id', 2)->get()->toArray();
        // dd($log);
        return view('ms_assets.index');
    }

    public function getAssets()
    {
        $assets = Asset::orderBy('id', 'DESC')->get();
        // dd($assets->toArray());
        return DataTables::of($assets)
        ->addIndexColumn()
        ->editColumn('vendor', function ($assets) {
            return $assets->vendor->name;
        })
        ->editColumn('quantity', function ($assets) {
            if ($assets->quantity == 0) {
                return '<div style="background-color: #ff9999;"> '.$assets->quantity.' </div>';
            } else {
                return $assets->quantity;
            }
        })
        ->editColumn('status', function ($assets) {
            $asset_items = Asset_item::where('asset_id', $assets->id)->orderBy('id', 'DESC')->get();
            // dd($asset_items);
            $saveHelper = '';
            $saveTotalType = '';
            for ($a = 0; $a < count($asset_items); $a ++) {
                if ($asset_items[$a]->type == FunctionHelper::DIKEMBALIKAN) {
                    $saveHelper = '<span class="name badge bg-success" style="color: white;"> Dikembalikan </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::DIPINJAMKAN) {
                    $saveHelper = '<span class="name badge bg-primary" style="color: white;"> Dipinjamkan </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::SERVICE) {
                    $saveHelper = '<span class="name badge bg-warning" style="color: white;"> Services </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::RUSAK) {
                    $saveHelper = '<span class="name badge bg-danger" style="color: white;"> Rusak </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::HILANG) {
                    $saveHelper = '<span class="name badge bg-danger" style="color: white;"> Hilang </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::KELUAR) {
                    $saveHelper = '<span class="name badge bg-info" style="color: white;"> Keluar </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::HIBAH) {
                    $saveHelper = '<span class="name badge bg-secondary" style="color: white;"> Hibah </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::BELI) {
                    $saveHelper = '<span class="name badge bg-success" style="color: white;"> Beli </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::JUAL) {
                    $saveHelper = '<span class="name badge bg-primary" style="color: white;"> Jual </span>';
                } else if ($asset_items[$a]->type == FunctionHelper::STOCK_AWAL) {
                    $saveHelper = '<span class="name badge bg-success" style="color: white;"> Stock Awal </span>';
                }
                $saveHtml[$a] = $saveHelper;
            }
            $save = array_count_values($saveHtml);
            foreach ($save as $key => $value) {
                $cek[] = '<span class="name badge bg-white" style="color: black;"> '.$value.' </span>'.''.$key;
            }

            // dd($cek);
            // $collect = collect($saveHtml);
            // $counted = $collect->countBy();
            // dd($counted->toArray());
            // $cek = implode($counted->toArray());
            // $save = ;
            // implode('<br>', array_unique($saveHtml)).''.implode($counted->toArray());

            return implode('<br>', array_unique($cek));
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
            $action .= '<a href="'.route('detail.asset', $assets['id']).'" class="btn btn-info btn-sm" id="detail" title="detail"> <i class="fas fa-history"></i> </a> </div>';
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
                            'type' => 10,
                            'status' => 1,
                            'notes' => $request->notes ?? null,
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                            'created_at' => now(),
                            'updated_at' => now()
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
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ];
                    Log::insert($log_asset);

                    $asset_items = [
                        [
                            'date' => $request->buy_at,
                            'code' => implode('.', $generate),
                            'asset_id' => Asset::all()->last()->id,
                            'quantity' => 1,
                            'type' => 10,
                            'employee_id' => null,
                            'notes' => 'stock awal',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    ];
                    Asset_item::insert($asset_items);
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
                    'type' => 10,
                    'status' => 1,
                    'notes' => $request->notes ?? null,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];
                Log::insert($log_asset);

                for ($val = 1; $val <= $request->quantity; $val++) {
                    $asset_items = [
                        [
                            'date' => $request->buy_at,
                            'code' => implode('.', $generate),
                            'asset_id' => Asset::all()->last()->id,
                            'quantity' => 1,
                            'type' => 10,
                            'employee_id' => null,
                            'notes' => 'stock awal',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    ];
                    Asset_item::insert($asset_items);
                }
            }

            if (!$query) {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            } else {
                return response()->json(['code' => 1, 'msg' => 'New Assets has been successfuly saved']);
            }
        }
    }

    public function detailAsset(Request $request, $id)
    {
        $asset = Asset::where('id', $request->id)->first();
        return view('ms_assets.detail_assets', compact('asset'));
    }

    public function showDetailAsset(Request $request, $id)
    {
        $asset_items = Asset_item::where('asset_id', $request->id)->orderBy('id', 'DESC')->get();
        // dd($asset_items->toArray());
        return DataTables::of($asset_items)
        ->addIndexColumn()
        ->editColumn('code', function($asset_items) {
            return $asset_items->asset->code;
        })
        ->editColumn('asset', function($asset_items) {
            return $asset_items->asset->name;
        })
        ->editColumn('employee', function($asset_items) {
            if ($asset_items->employee_id == null) {
                return '-';
            } else {
                return $asset_items->employee->Name;
            }
        })
        ->editColumn('type', function($asset_items) {
            if (FunctionHelper::DIKEMBALIKAN == $asset_items->type) {
                return '<span class="name badge bg-success" style="color: white;"> DIKEMBALIKAN </span>';
            } else if (FunctionHelper::DIPINJAMKAN == $asset_items->type) {
                return '<span class="name badge bg-primary" style="color: white;"> DIPINJAMKAN </span>';
            } else if (FunctionHelper::SERVICE == $asset_items->type) {
                return '<span class="name badge bg-warning" style="color: white;"> SERVICE </span>';
            } else if (FunctionHelper::RUSAK == $asset_items->type) {
                return '<span class="name badge bg-danger" style="color: white;"> RUSAK </span>';
            } else if (FunctionHelper::HILANG == $asset_items->type) {
                return '<span class="name badge bg-danger" style="color: white;"> HILANG </span>';
            } else if (FunctionHelper::KELUAR == $asset_items->type) {
                return '<span class="name badge bg-info" style="color: white;"> KELUAR </span>';
            } else if (FunctionHelper::HIBAH == $asset_items->type) {
                return '<span class="name badge bg-secondary" style="color: white;"> HIBAH </span>';
            } else if (FunctionHelper::BELI == $asset_items->type) {
                return '<span class="name badge bg-success" style="color: white;"> BELI </span>';
            } else if (FunctionHelper::JUAL == $asset_items->type) {
                return '<span class="name badge bg-primary" style="color: white;"> JUAL </span>';
            } else if (FunctionHelper::STOCK_AWAL == $asset_items->type) {
                return '<span class="name badge bg-success" style="color: white;"> STOCK AWAL </span>';
            }
        })
        ->rawColumns(['code', 'asset', 'employee', 'type'])
        ->make(true);
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
                    'updated_at' => now(),
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
                    'updated_at' => now(),
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
                    'updated_at' => now(),
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
