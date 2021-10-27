<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect();
    }

    public function headings(): array
    {
        $columns = [
            'category_code',
            'name',
            'quantity',
            'buy_at',
            'notes'
        ];
        return $columns;
    }
}
