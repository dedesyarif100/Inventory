<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\master\Employee;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class MsEmployeeController extends Controller
{
    public function index()
    {
        return view('ms_employee.index');
    }

    public function getEmployee()
    {
        // $employee = new Employee;
        // $employee->setConnection('sqlsrv');
        $employee = DB::connection('sqlsrv')->table("dbo.EmployeeInformation")->get();

        // dd($employee);
        return DataTables::of($employee)
        ->addIndexColumn()
        ->make(true);
    }
}
