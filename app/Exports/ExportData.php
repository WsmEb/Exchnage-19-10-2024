<?php

namespace App\Exports;

use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportData implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        if (Session::has('data'))
            return collect([Session::get('data')]);
        else
            return collect(["non data"]);
    }


    public function headings(): array
    {
        if (Session::has('header'))
            return Session::get('header');
        else
            return [];
    }
}
