<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\master\Stockopname;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MsStockopnameController extends Controller
{
    public function editorStockopname(Request $request)
    {
        $stockopname = Stockopname::find($request->stockopname_id);
        return view('ms_stockopname.editor', compact('stockopname'));
    }

    public function index()
    {
        $stockopname = Stockopname::where('status', 1)->exists();
        return view('ms_stockopname.index', compact('stockopname'));
    }

    public function getStockopname()
    {
        $stockopname = Stockopname::orderBy('date', 'DESC')->get();
        return DataTables::of($stockopname)
        ->addIndexColumn()
        ->editColumn('note', function ($stockopname) {
            return $stockopname->note ?? '-';
        })
        ->addColumn('CreatedBy', function ($stockopname) {
            return $stockopname->stockopnameCreateBy->email;
        })
        ->addColumn('UpdatedBy', function ($stockopname) {
            return $stockopname->stockopnameUpdateBy->email;
        })
        ->editColumn('status', function ($stockopname) {
            if ($stockopname->status == 1) {
                return '<span class="name badge bg-success"> Aktif </span>';
            } else {
                return '<span class="name badge bg-danger"> Tidak Aktif </span>';
            }
        })
        ->addColumn('action', function ($stockopname) {
            $action = '<div class="btn-group" role="group" aria-label="Basic example">  <button class="btn btn-info btn-sm" data-id="'.$stockopname['code'].'" id="show" title="Show"><i class="fas fa-eye"></i></button>';
            $action .= '<button class="btn btn-primary btn-sm" data-id="'.$stockopname['id'].' - '.$stockopname['date'].'" id="edit" title="Edit"><i class="fas fa-edit"></i></button>';
            if ($stockopname->status == 0) {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$stockopname['id'].'" id="delete" title="Delete" disabled><i class="fas fa-trash"></i></button>';
            } else {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$stockopname['id'].'" id="delete" title="Delete"><i class="fas fa-trash"></i></button>  </div>';
            }
            return $action;
        })
        ->rawColumns(['status', 'action'])
        ->make(true);
    }

    public function sendToDB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required'],
        ]);
        if($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $time = now()->format('His');
            $stockopname = new Stockopname();
            $stockopname->date = $request->date;
            $stockopname->code = 'SO'.Carbon::create($request->date)->format('Ymd').$time;
            $stockopname->note = $request->note ?? null;
            $stockopname->status = 1;
            $stockopname->created_by = Auth::id();
            $stockopname->updated_by = Auth::id();
            $query = $stockopname->save();
            if (!$query) {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            } else {
                return response()->json(['code' => 1, 'msg' => 'New Stockopname has been successfuly saved']);
            }
        }
    }

    public function getOldData($id)
    {
        $stockopname = Stockopname::find($id);
        dd($stockopname);
        return view('ms_stockopname.edit', compact('stockopname'));
    }

    public function editStockopname(Request $request)
    {
        $stockopname = Stockopname::find($request->stockopname_id);

        return view('ms_stockopname.edit', compact('stockopname'));
    }

    public function updateStockopname(Request $request, $stockopname_id)
    {
        $stockopname_id = $request->id;
        $validator = Validator::make($request->all(), [
            // 'note' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $query = Stockopname::where('id', $stockopname_id)->update([
                'note' => $request->note ?? null,
                'updated_by' => Auth::id()
            ]);

            if ($query) {
                return response()->json(['code' => 1, 'msg' => 'Stockopname Has Been Updated']);
            } else {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            }
        }
    }

    public function deleteStockopname(Request $request)
    {
        $stockopname_id = $request->stockopname_id;
        $query = Stockopname::find($stockopname_id)->delete();

        if($query) {
            return response()->json(['code' => 1, 'msg' => 'Stockopname Has Been Deleted From Databases']);
        } else {
            return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
        }
    }
}
