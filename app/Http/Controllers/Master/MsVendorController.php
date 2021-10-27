<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\master\Vendor;
use App\Models\master\Asset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MsVendorController extends Controller
{
    public function editorVendor(Request $request)
    {
        $vendor = Vendor::find($request->vendor_id);
        return view('ms_vendor.editor', compact('vendor'));
    }

    public function index()
    {
        return view('ms_vendor.index');
    }

    public function getVendor()
    {
        $vendor = Vendor::orderBy('id', 'DESC')->get();
        return DataTables::of($vendor)
        ->addIndexColumn()
        ->editColumn('address', function ($vendor) {
            return $vendor->address ?? '-';
        })
        ->editColumn('contact', function ($vendor) {
            return $vendor->contact ?? '-';
        })
        ->addColumn('CreatedBy', function ($vendor) {
            return $vendor->vendorCreateBy->email;
        })
        ->addColumn('UpdatedBy', function ($vendor) {
            return $vendor->vendorUpdateBy->email;
        })
        ->addColumn('action', function ($vendor) {

            $action = '<div class="btn-group" role="group" aria-label="Basic example">  <button class="btn btn-primary btn-sm" data-id="'.$vendor['id'].'" id="edit" title="Edit"><i class="fas fa-edit"></i></button>';
            if (Asset::where('vendor_id', $vendor->id)->exists()) {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$vendor['id'].'" id="delete" title="Delete" disabled><i class="fas fa-trash"></i></button>';
            } else {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$vendor['id'].'" id="delete" title="Delete"><i class="fas fa-trash"></i></button>  </div>';
            }
            return $action;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function sendToDB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $vendor = new Vendor();
            $vendor->name = $request->name;
            $vendor->address = $request->address ?? null;
            $vendor->contact = $request->contact ?? null;
            $vendor->created_by = Auth::id();
            $vendor->updated_by = Auth::id();
            $query = $vendor->save();
            if (!$query) {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            } else {
                return response()->json(['code' => 1, 'msg' => 'New Vendor has been successfuly saved']);
            }
        }
    }

    public function editVendor(Request $request)
    {
        $vendor_id = $request->vendor_id;
        $vendorDetails = Vendor::find($vendor_id);
        return response()->json(['details' => $vendorDetails]);
    }

    public function updateVendor(Request $request)
    {
        $vendor_id = $request->id;
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $vendor = Vendor::find($vendor_id);
            $vendor->name = $request->name;
            $vendor->address = $request->address ?? null;
            $vendor->contact = $request->contact ?? null;
            $vendor->updated_by = Auth::id();
            $query = $vendor->save();

            if ($query) {
                return response()->json(['code' => 1, 'msg' => 'Vendor Has Been Updated']);
            } else {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            }
        }
    }

    public function deleteVendor(Request $request)
    {
        $vendor_id = $request->vendor_id;
        $query = Vendor::find($vendor_id)->delete();

        if($query) {
            return response()->json(['code' => 1, 'msg' => 'Vendor Has Been Deleted From Databases']);
        } else {
            return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
        }
    }
}
