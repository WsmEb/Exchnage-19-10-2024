<?php

namespace App\Exports;

use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportDeposes implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        $donnees = Session::has('deposes') ? Session::get('deposes') : [];
        $operations = [];
        $info = Session::has('client') ? Session::get('client') : [];


        foreach ($donnees[0] as $operation) {
            $formattedOperation = [
                'DATE DEPOSE' => $operation['date_depose'],
                'MONTANT' => $operation['amount'] . ' ' . $info[1]["symbol"],
                'TYPE' => $operation['type'],
                'COMMENTAIRE' => $operation['commentaire'],
            ];

            $operations[] = $formattedOperation;
        }
        $operations[] = ["TOTAL DEPOSE", $donnees[1] < 0 ? $donnees[1]. ' ' . $info[1]["symbol"] : "+" . $donnees[1]. ' ' . $info[1]["symbol"]];
        $operations[] = ["TOTAL RETRAIT", $donnees[2] < 0 ? $donnees[2]. ' ' . $info[1]["symbol"] : "+" . $donnees[2]. ' ' . $info[1]["symbol"]];
        $operations[] = ["TOTAL", $donnees[3] < 0 ? $donnees[3] . ' ' . $info[1]["symbol"]: "+" . $donnees[3] .' '. $info[1]["symbol"]];

        return collect($operations);
    }


    public function headings(): array
    {
        $info = Session::has('client') ? Session::get('client') : [];
        return [
            ["client", $info[0]["nom"], "devise selection", $info[1]["symbol"], "devise de base", $info[2]],
            ["DATE DEPOSE", "MONTANT", "TYPE", "COMMENTAIRE"]
        ];
    }
}
