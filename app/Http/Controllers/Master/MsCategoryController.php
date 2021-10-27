<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\master\Category;
use App\Models\master\Asset;
use App\Models\master\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MsCategoryController extends Controller
{
    public function editorCategory(Request $request)
    {
        $category = Category::find($request->category_id);
        $asset = Asset::where('category_id', $request->category_id)->first();
        return view('ms_category.editor', compact('category', 'asset'));
    }

    public function index()
    {
        return view('ms_category.index');
    }

    public function getCategory()
    {
        $category = Category::orderBy('id', 'DESC')->get();
        return DataTables::of($category)
        ->addIndexColumn()
        ->addColumn('createdBy', function ($category) {
            return $category->categoryCreateBy->email;
        })
        ->addColumn('UpdatedBy', function ($category) {
            return $category->categoryUpdateBy->email;
        })
        ->addColumn('action', function ($category) {
            $action = '<div class="btn-group" role="group" aria-label="Basic example">  <button class="btn btn-primary btn-sm" data-id="'.$category['id'].'" id="edit" title="Edit"><i class="fas fa-edit"></i></button>';
            if (Asset::where('category_id', $category->id)->exists()) {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$category['id'].'" id="delete" title="Delete" disabled><i class="fas fa-trash"></i></button>';
            } else {
                $action .= '<button class="btn btn-danger btn-sm" data-id="'.$category['id'].'" id="delete" title="Delete"><i class="fas fa-trash"></i></button>  </div>';
            }
            return $action;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function sendToDB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'code' => ['required', 'regex:/^[a-zA-Z]+$/u', 'max:4'],
        ]);
        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $category = new Category();
            $category->name = $request->name;
            $category->code = strtoupper($request->code);
            $category->created_by = Auth::id();
            $category->updated_by = Auth::id();
            $query = $category->save();
            if (!$query) {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            } else {
                return response()->json(['code' => 1, 'msg' => 'New Category has been successfuly saved']);
            }
        }
    }

    public function editCategory(Request $request)
    {
        $category_id = $request->category_id;
        $categoryDetails = Category::find($category_id);
        return response()->json(['details' => $categoryDetails]);
    }

    public function updateCategory(Request $request)
    {
        $category_id = $request->id;
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'code' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $category = Category::find($category_id);
            $category->name = $request->name;
            $category->code = strtoupper($request->code);
            $category->updated_by = Auth::id();
            $query = $category->save();

            if ($query) {
                return response()->json(['code' => 1, 'msg' => 'Category Has Been Updated']);
            } else {
                return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
            }
        }
    }

    public function deleteCategory(Request $request)
    {
        $category_id = $request->category_id;
        $query = Category::find($category_id)->delete();

        if($query) {
            return response()->json(['code' => 1, 'msg' => 'Category Has Been Deleted From Databases']);
        } else {
            return response()->json(['code' => 0, 'msg' => 'Something went wrong']);
        }
    }
}
