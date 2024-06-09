<?php

namespace App\Exports;

use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportTransfert implements FromCollection, WithHeadings
{
        use Exportable;

    public function collection()
    {
        $donnees = Session::has('transferts') ? Session::get('transferts') : [];
        $info = Session::has('info_transfert') ? Session::get('info_transfert') : [];
        $transferts = [];
        foreach ($donnees[0] as $tr) {
            $formattedOperation = [$tr[1],$tr[5],$tr[6],$tr[4],$tr[4]* $info[1]["base"]];

            $transferts[] = $formattedOperation;
        }
        $transferts[] = ["Total Recepteur", $donnees[1][0]];
        $transferts[] = ["Total Expéditeur", $donnees[2][0]];
        $transferts[] = ["Total",$donnees[1][0] - $donnees[2][0]];

        return collect($transferts);
    }


    public function headings(): array
    {
        $info = Session::has('info_transfert') ? Session::get('info_transfert') : [];
        return [
            ["client", $info[0]["nom"], "devise selection", $info[1]["symbol"]],
            ["date", "Expéditeur", "Recepteur", "Solde (".$info[1]["symbol"].")","Solde de base (".$info[2].")"]
        ];
    }
}
