<?php

namespace App\Exports;

use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportStock implements FromCollection, WithHeadings
{
  use Exportable;

  public function collection()
  {
    $donnees = Session::has('stock') ? Session::get('stock') : [];
    $operations = [];
    $info = Session::has('client') ? Session::get('client') : [];


    foreach ($donnees[0] as $operation) {
      $formattedOperation = [
        'id' => $operation['client'],
        'Nom' => $operation['client'],
        'BALANCE ' => $operation['totalDifference'] . ' ' . $info[2],
      ];

      $operations[] = $formattedOperation;
    }
    return collect($operations);
  }


  public function headings(): array
  {
    $info = Session::has('client') ? Session::get('client') : [];
    return [
      ["devise de base", $info[2]],
      ['Identifient client','Nom client', 'Balance']
    ];
  }
}