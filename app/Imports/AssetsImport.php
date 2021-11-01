<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use App\Models\master\Asset;
use App\Models\master\Asset_item;
use App\Models\master\Category;
use App\Models\master\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Throwable;

class AssetsImport implements ToCollection, WithHeadingRow, SkipsOnError, WithValidation, SkipsEmptyRows
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        // dd(Carbon::parse($row['buy_at'])->toDateTimeString(), $row, \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['buy_at']));
        // Log Assets >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        // dd(Category::where('code', $row['category_code'])->first());
        // dd( $rows );
        // $tgl = $rows[0]['buy_at'];
        // $tgl_array = date("Y", $tgl);
        // dd($tgl_array);

        // dd( Carbon::create(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows[0]['buy_at'])) );
        for ($val = 0; $val < count($rows); $val++) {
            // dd (  );
            if ($rows[$val]['category_code'] == null) {
                return false;
            }

            $cekCategory = Category::where('code', $rows[$val]['category_code'])->first();
            if ($cekCategory != null) {
                $code_assets = Asset::where('category_id', $cekCategory->id)->orderBy('code', 'DESC')->first();
            }
            $getYear = Carbon::create(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows[$val]['buy_at']));
            $getNow = now();

            if ( empty($cekCategory) || empty($code_assets) ) {
                $convert = $rows[$val]['category_code'];
                $save = 0;
                $generate = explode('.', $convert);
                $generate[1] = $getNow->year;
                // if ($rows[$val]['buy_at'] == null) {
                //     $generate[1] = $getNow->year;
                // } else {
                //     $generate[1] = $getYear->year;
                // }
                $save++;
                array_push($generate, Str::padLeft($save, 4, '0'));
                $generate[2] = Str::padLeft($save, 4, '0');
            } else {
                $convert = $code_assets->code;
                $generate = explode('.', $convert);
                // if ($rows[$val]['buy_at'] == null) {
                //     $generate[1] = $getNow->year;
                // } else {
                //     $generate[1] = $getYear->year;
                // }
                $save = intval($generate[2]) + 1;
                $generate[2] = Str::padLeft($save, 4, '0');
            }

            // if (!is_numeric($rows[$val]['quantity'])) {
            //     return back()->withStatus('Failed Import, Quantity must be integer');
            // }

            if ( $cekCategory == true ) {
                // dd('true');
                $asset = Asset::create([
                    'category_id' => $cekCategory->id,
                    'code' => implode('.', $generate),
                    'name' => $rows[$val]['name'],
                    'vendor_id' => 1,
                    'quantity' => $rows[$val]['quantity'],
                    'buy_at' => $rows[$val]['buy_at'] == null ? now() : Carbon::create(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows[$val]['buy_at'])),
                    'employee_id' => 1,
                    'type' => 10,
                    'status' => 1,
                    'notes' => $rows[$val]['notes'] ?? null,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $category = [
                    'name' => $rows[$val]['category_code'],
                    'code' => $rows[$val]['category_code'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                // dd($category);
                Category::insert($category);
                // dd(Category::count());

                $asset = Asset::create([
                    'category_id' => Category::count(),
                    'code' => implode('.', $generate),
                    'name' => $rows[$val]['name'],
                    'vendor_id' => 1,
                    'quantity' => $rows[$val]['quantity'],
                    'buy_at' => $rows[$val]['buy_at'] == null ? now() : Carbon::create(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows[$val]['buy_at'])),
                    'employee_id' => 1,
                    'type' => 10,
                    'status' => 1,
                    'notes' => $rows[$val]['notes'] ?? null,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                // dd($asset);
            }

            // After import, Auto Create Asset_item
            for ($qty = 0; $qty < $rows[$val]['quantity']; $qty++) {
                $asset_items = [
                    [
                        'date' => $rows[$val]['buy_at'] == null ? now() : Carbon::create(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows[$val]['buy_at'])),
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

            // After import, Auto Create Log Assets
            $log_asset = [
                [
                    'date' => $rows[$val]['buy_at'] == null ? now() : Carbon::create(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rows[$val]['buy_at'])),
                    'qrcode' => uniqid(),
                    'asset_id' => $asset['id'],
                    'type' => 10,
                    'employee_id' => null,
                    'qty_in' => $asset['quantity'],
                    'qty_out' => 0,
                    'notes' => 'stock awal',
                    'created_at' => $asset['created_at'],
                    'updated_at' => $asset['updated_at']
                ],
            ];
            // dd($asset, $log_asset);
            Log::insert($log_asset);
        }
        return null;
    }

    public function onError(Throwable $error)
    {

    }

    public function rules(): array
    {
        return [
            'category_code' => 'required',
            'name' => 'required',
            'quantity' => 'required|integer',
        ];
    }
}
