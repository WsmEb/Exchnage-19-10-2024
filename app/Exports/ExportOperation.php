<?php

namespace App\Exports;

use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportOperation implements FromCollection, WithHeadings
{
    use Exportable;

    public function collection()
    {
        $donnees = Session::has('operations') ? Session::get('operations') : [];
        $operations = [];
        $som = 0;

        foreach ($donnees[0] as $operation) {
            $som = $operation['type_operation'] == "moi" ? $som + $operation['total'] : $som - $operation['total'];
            $formattedOperation = [
                'Date' => $operation['date'],
                'Commentaire' => $operation['comments'],
                'type' => $operation['type_operation'],
                'Ville' => $operation['ville'] ?? '',
                'Quantite' => $operation['quantity'],
                'Prix' => $operation['prix'],
                '%' => $operation['percentage'],
                'Total' => $operation['total'],
                'Solde' => $som,
            ];
    
            $operations[] = $formattedOperation;
        }
        $operations[] = ["Total moi",$donnees[1][0]];
        $operations[] = ["Total toi",$donnees[2][0]];
        $operations[] = ["Total",$som];

        return collect($operations);
    }


    public function headings(): array
    {
        $info = Session::has('client') ? Session::get('client') : [];
        return [
            ["client", $info[0]["nom"], "devise selection", $info[1]["symbol"], "devise de base", $info[2]],
            ["date", "Commentaire", "Type", "Ville", "Quantité", "Prix","Porcentage", "Total", "Solde"]
        ];
    }
}
