<?php

namespace App\Exports;

use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportConverts implements FromCollection, WithHeadings
{
    use Exportable;



    public function collection()
    {

        $info = Session::has('client') ? Session::get('client') : [];

        function CheckSold($sold)
        {
            return $sold;
        }
        $donnees = Session::has('converts') ? Session::get('converts') : [];
        $operations = [];

        foreach ($donnees[0] as $operation) {
            $som = 10;
            $formattedOperation = [
                'DATE' => $operation['date'],
                "DEVISE D'ORIGINE" => $operation['devise'],
                "DEVISE DESTINATION" => $operation['convertedSymbol'],
                'MONTANT' =>  number_format($operation['amount'],2) . ' ' . $operation['devise'],
                'MONTANT CONVERTI' =>  number_format($operation['amount'] * ConvertedSymbolBase($operation["devise"]) / ConvertedSymbolBase($operation["convertedSymbol"]),2) . ' ' . $operation['convertedSymbol'],
                'COMMENTAIRE' => $operation['commentaire'],
            ];

            $operations[] = $formattedOperation;


        }
        $operations[] = ["TOTAL RECU",  number_format($donnees[2] + $donnees[1],2) . ' ' . $info[1]["symbol"]];
        $operations[] = ["TOTAL CONVERTED", number_format($donnees[1],2) . ' ' . $info[1]["symbol"]];
        $operations[] = ["TOTAL",  number_format($donnees[2],2) . ' ' . $info[1]["symbol"]];

        return collect($operations);
    }


    public function headings(): array
    {
        $info = Session::has('client') ? Session::get('client') : [];
        return [
            ["client", $info[0]["nom"], "devise selection", $info[1]["symbol"], "devise de base", $info[2]],
            ["DATE", "DEVISE D'ORIGINE", "DEVISE DESTINATION", "MONTANT", "MONTANT CONVERTI", "COMMENTAIRE"]
        ];
    }
}