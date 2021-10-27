<?php

use App\Http\Controllers\Master\MsAssetsController;
use App\Http\Controllers\Master\LogAssetsController;
use App\Http\Controllers\Master\MsCategoryController;
use App\Http\Controllers\Master\MsEmployeeController;
use App\Http\Controllers\master\MsLogController;
use App\Http\Controllers\Master\MsStockopnameController;
use App\Http\Controllers\Master\MsUsersController;
use App\Http\Controllers\Master\MsVendorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';


Route::middleware(['auth'])->get('/', function () {
    return view('main');
});

Route::middleware(['auth'])->prefix('master')->group( function () {

    Route::get('coba', function () {
        try {
            DB::connection()->getPdo();
            echo "tes";
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }
    });

    // Route::get('dashboard', function() {
    //     return view('dashboard.index');
    // });

    Route::get('editorusers/{id?}', [MsUsersController::class, 'editorUsers'])->name('editor.users');
    Route::get('masteruser', [MsUsersController::class, 'index'])->name('index.users');
    Route::get('getdatauser', [MsUsersController::class, 'getUsers'])->name('getdata.users');
    Route::post('postuser', [MsUsersController::class, 'sendToDB'])->name('sendtodatabase.user');
    Route::post('editdatauser', [MsUsersController::class, 'editUser'])->name('edit.user');
    Route::patch('/updatedatauser/{id}', [MsUsersController::class, 'updateUser'])->name('update.user');
    Route::post('deleteuser', [MsUsersController::class, 'deleteUser'])->name('delete.user');


    Route::get('masteremployee', [MsEmployeeController::class, 'index']);
    Route::get('getdataemployee', [MsEmployeeController::class, 'getEmployee'])->name('getdata.employee');


    Route::get('editorassets/{id?}', [MsAssetsController::class, 'editorAssets'])->name('editor.assets');
    Route::get('masterassets', [MsAssetsController::class, 'index'])->name('index.assets');
    Route::get('getdataassets', [MsAssetsController::class, 'getAssets'])->name('getdata.assets');
    Route::post('postassets', [MsAssetsController::class, 'sendToDB'])->name('sendtodatabase.assets');
    Route::patch('/updatedataassets/{id}', [MsAssetsController::class, 'updateAssets'])->name('update.assets');
    Route::get('showcode/{id}', [MsAssetsController::class, 'getResponseShowCode'])->name('getResponseShowCode.assets');
    Route::post('deleteassets', [MsAssetsController::class, 'deleteAssets'])->name('delete.assets');
    Route::get('pageprint_qrcode', [MsAssetsController::class, 'pageprint_qrcode'])->name('pageprint.qrcode');
    Route::get('printqrcode', [MsAssetsController::class, 'getAllQRCode'])->name('print.qrcode');
    // Route::get('importassets', [MsAssetsController::class, 'show'])->name('view.assets');
    Route::get('exporttemplate', [MsAssetsController::class, 'exportTemplate'])->name('export.template');
    Route::post('importassets', [MsAssetsController::class, 'importAssets'])->name('import.assets');


    Route::get('editorlog/{id?}', [MsLogController::class, 'editorLog'])->name('editor.log');
    Route::get('masterlog', [MsLogController::class, 'index'])->name('index.log');
    Route::get('getdatalog', [MsLogController::class, 'getLog'])->name('getdata.log');
    Route::post('postlog', [MsLogController::class, 'sendToDB'])->name('sendtodatabase.log');



    Route::get('ms_logasset', [LogAssetsController::class, 'index'])->name('log.asset');
    Route::get('getlogasset', [LogAssetsController::class, 'getLogAsset'])->name('get.log_asset');


    Route::get('editorcategory/{id?}', [MsCategoryController::class, 'editorCategory'])->name('editor.category');
    Route::get('mastercategory', [MsCategoryController::class, 'index'])->name('index.category');
    Route::get('getdatacategory', [MsCategoryController::class, 'getCategory'])->name('getdata.category');
    Route::post('createcategory', [MsCategoryController::class, 'createCategory'])->name('create.category');
    Route::post('postcategory', [MsCategoryController::class, 'sendToDB'])->name('sendtodatabase.category');
    Route::post('editcategory', [MsCategoryController::class, 'editCategory'])->name('edit.category');
    Route::patch('updatedatacategory/{id}', [MsCategoryController::class, 'updateCategory'])->name('update.category');
    Route::post('deletecategory', [MsCategoryController::class, 'deleteCategory'])->name('delete.category');

    Route::get('editorvendor/{id?}', [MsVendorController::class, 'editorVendor'])->name('editor.vendor');
    Route::get('mastervendor', [MsVendorController::class, 'index'])->name('index.vendor');
    Route::get('getdatavendor', [MsVendorController::class, 'getVendor'])->name('getdata.vendor');
    Route::post('createvendor', [MsVendorController::class, 'createVendor'])->name('create.vendor');
    Route::post('postvendor', [MsVendorController::class, 'sendToDB'])->name('sendtodatabase.vendor');
    Route::post('editdatavendor', [MsVendorController::class, 'editVendor'])->name('edit.vendor');
    Route::patch('updatedatavendor/{id}', [MsVendorController::class, 'updateVendor'])->name('update.vendor');
    Route::post('deletevendor', [MsVendorController::class, 'deleteVendor'])->name('delete.vendor');

    Route::get('editorstockopname/{id?}', [MsStockopnameController::class, 'editorStockopname'])->name('editor.stockopname');
    Route::get('masterstockopname', [MsStockopnameController::class, 'index'])->name('index.stockopname');
    Route::get('getdatastockopname', [MsStockopnameController::class, 'getStockopname'])->name('getdata.stockopname');
    Route::post('poststockopname', [MsStockopnameController::class, 'sendToDB'])->name('sendtodatabase.stockopname');
    Route::get('getolddata/{id}', [MsStockopnameController::class, 'getOldData'])->name('get.olddata');
    Route::post('editdatastockopname', [MsStockopnameController::class, 'editStockopname'])->name('edit.stockopname');
    Route::patch('updatedatastockopname/{id}', [MsStockopnameController::class, 'updateStockopname'])->name('update.stockopname');
    Route::post('deletestockopname', [MsStockopnameController::class, 'deleteStockopname'])->name('delete.stockopname');
});
