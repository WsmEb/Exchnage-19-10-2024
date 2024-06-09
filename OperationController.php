<?php

namespace App\Http\Controllers;

use App\Exports\ExportOperation;
use App\Models\Client;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\Historiques_Operations;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use TCPDF;

use function GuzzleHttp\json_encode;

class OperationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($client, $devise)
    {
        $items = Session::get('actions');

        $notclients = Session::get('clients')->toArray();
        if (in_array($client, $notclients))
            return redirect()->route("403");

        if ((array_key_exists('operation', $items) && in_array("tout", $items["operation"])) || (array_key_exists('client', $items) && in_array("detail", $items["client"]))) {
            return redirect()->route("403");
        } else {
            $villes = DB::select("
            SELECT DISTINCT ville AS ville
            FROM detail_historiques_operations
            UNION
            SELECT DISTINCT ville
            FROM operations
            UNION
            SELECT DISTINCT localisation AS ville
            FROM clients
        ");
            $client = Client::all()->where("username", $client)->first();
            $devise = Devise::all()->where("symbol", $devise)->first();
            $devises = Devise::all();
            $entreprise = Entreprise::first();
            $historiques =  Historiques_Operations::where('client', $client->username)->where('devise', $devise->symbol)->get();
            $operations = Operation::where('client', $client->username)->where('devise', $devise->symbol)->get();
            $statistique_moi = $this->getTotalByType('moi', $devise->symbol, $client->username);
            $statistique_toi = $this->getTotalByType('toi', $devise->symbol, $client->username);
            Session::put('operations', [$operations->toArray(), $statistique_moi, $statistique_toi]);
            Session::put('client', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);
            return view('operation', compact("client", "devise", "devises", "operations", "statistique_moi", "statistique_toi", "entreprise", "historiques","villes"));
        }
    }
    public function add(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('operation', $items) && in_array("ajouter", $items["operation"]))) {
            return redirect()->route("403");
        } else {
            $operation = Operation::create([
                'date' => $request->date,
                'client' => $request->client,
                'comments' => $request->commentaire,
                'total' => $request->total,
                'quantity' => $request->quantite,
                'percentage' => $request->operation2add . $request->porcentage,
                'devise' => $request->devise,
                'type_operation' => $request->type,
                'ville' => $request->ville,
                'prix' => $request->prix,
            ]);

            return  redirect()->route('operation.index', ['client' => $request->client, 'devise' => $request->devise])->with('success', 'L\'opération a été créée avec succès.');
        }
    }
    public function update(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('operation', $items) && in_array("modifier", $items["operation"]))) {
            return redirect()->route("403");
        } else {
            $operation = Operation::find($request->id);
            $operation->date = $request->date;
            $operation->comments = $request->commentaire;
            $operation->total = $request->total;
            $operation->quantity = $request->quantite;
            $operation->percentage = $request->operation2update . $request->porcentage;
            $operation->type_operation = $request->type;
            $operation->ville = $request->ville;
            $operation->prix = $request->prix;
            $operation->save();
            return  redirect()->route('operation.index', ['client' => $request->client, 'devise' => $request->devise])->with('success', 'L\'opération a été sauvegardée avec succès.');
        }
    }

    public function delete(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('operation', $items) && in_array("supprimer", $items["operation"]))) {
            return redirect()->route("403");
        } else {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->route('operation.index', ['client' => $request->client, 'devise' => $request->devise])->with('error', 'Le mot de passe actuel est incorrect');
            }
            $operation = Operation::all()->where("id", $request->operation)->first();
            $operation->delete();
            return  redirect()->route('operation.index', ['client' => $request->client, 'devise' => $request->devise])->with('success', 'L\'opération a été supprimée avec succès.');
        }
    }

    public function search($client, $devise, $col = NULL, $val = NULL)
    {
        if ($val == NULL) {
            $operations = Operation::where('client', $client)->where('devise', $devise)->get();
            $statistique_moi = $this->getTotalByType('moi', $devise, $client);
            $statistique_toi = $this->getTotalByType('toi', $devise, $client);
        } else {
            $operations = Operation::where('client', $client)->where('devise', $devise)->where($col, 'REGEXP', $val)->get();
            $statistique_moi = $this->getTotalByTypeByFind('moi', $devise, $client, $col, $val);
            $statistique_toi = $this->getTotalByTypeByFind('toi', $devise, $client, $col, $val);
        }
        Session::put('operations', [$operations->toArray(), $statistique_moi, $statistique_toi]);
        echo json_encode([$operations, $statistique_moi, $statistique_toi, $statistique_moi[0] - $statistique_toi[0]]);
    }
    public function searchdate($client, $devise, $date1 = NULL, $date2 = NULL)
    {
        if ($date1 == NULL || $date2 == NULL) {
            $operations = Operation::where('client', $client)->where('devise', $devise)->get();
            $statistique_moi = $this->getTotalByType('moi', $devise, $client);
            $statistique_toi = $this->getTotalByType('toi', $devise, $client);
        } else {
            $operations = Operation::where('client', $client)->where('devise', $devise)->whereBetween('date', [$date1, $date2])->get();
            $statistique_moi = $this->getTotalByTypeByDate('moi', $devise, $client, $date1, $date2);
            $statistique_toi = $this->getTotalByTypeByDate('toi', $devise, $client, $date1, $date2);
        }
        Session::put('operations', [$operations->toArray(), $statistique_moi, $statistique_toi]);
        echo json_encode([$operations, $statistique_moi, $statistique_toi, $statistique_moi[0] - $statistique_toi[0]]);
    }



    public function getTotalByType($type, $devise, $client)
    {
        $totalByType = Operation::select(DB::raw('SUM(total) as total,count(*) as nb'))
            ->where('client', $client)->where('devise', $devise)->where('type_operation', $type)
            ->groupBy('type_operation')
            ->get();
        return count($totalByType) == 0 ? [0, 0] : [$totalByType[0]->total, $totalByType[0]->nb];
    }

    public function getTotalByTypeByFind($type, $devise, $client, $col, $val)
    {
        $totalByType = Operation::select(DB::raw('SUM(total) as total,count(*) as nb'))
            ->where('client', $client)->where('devise', $devise)->where('type_operation', $type)->where($col, 'REGEXP', $val)
            ->groupBy('type_operation')
            ->get();
        return count($totalByType) == 0 ? [0, 0] : [$totalByType[0]->total, $totalByType[0]->nb];
    }
    public function getTotalByTypeByDate($type, $devise, $client, $date1, $date2)
    {
        $totalByType = Operation::select(DB::raw('SUM(total) as total,count(*) as nb'))
            ->where('client', $client)->where('devise', $devise)->where('type_operation', $type)->whereBetween('date', [$date1, $date2])
            ->groupBy('type_operation')
            ->get();
        return count($totalByType) == 0 ? [0, 0] : [$totalByType[0]->total, $totalByType[0]->nb];
    }

    public function pdf()
    {

        $items = Session::get('actions');
        if ((array_key_exists('operation', $items) && in_array("exporter", $items["operation"]))) {
            return redirect()->route("403");
        } else {
            $donnees = Session::has('operations') ? Session::get('operations') : [];
            $informations = Session::has('client') ? Session::get('client') : [];
            $headers = ['Date', 'Commentaire', 'Ville', 'Quantite', 'Prix', '%', 'Total', 'Solde'];

            $html = '<style>th,td{border: 1px solid black;text-align:center;font-size:9px;}th{font-size:17px;background-color:gold;}</style>';


            // Tcpdf ==========================================================================================================
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetTitle('Opérations');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            $pdf->SetMargins(PDF_MARGIN_LEFT, 2, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->AddPage();

            $xc = 120;
            $yc = 25;
            $r = 15;
            $total = $donnees[2][1] + $donnees[1][1];

            $pdf->setFillColor(255, 0, 0);
            $pdf->PieSector($xc, $yc, $r, 0, 360 / $total * $donnees[2][1], 'FD', false, 0, 2);

            $pdf->setFillColor(0, 200, 0);
            $pdf->PieSector($xc, $yc, $r, 360 / $total * $donnees[2][1], 360, 'FD', false, 0, 2);


            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', '', 12);
            $pdf->setXY(10, 15);
            $pdf->Cell(70, 10, 'Mr. ' . $informations[0]["nom"], 0, 0, '', 1);
            $pdf->setXY(10, 25);
            $pdf->Cell(70, 10, 'Devise sélection : ' . $informations[1]["symbol"], 0, 0, '', 1);
            $pdf->setXY(10, 35);
            $pdf->Cell(70, 10, 'Devise de base : ' . $informations[2], 0, 0, '', 1);


            $pdf->setFont('helvetica', 'B', 8);
            $pdf->setXY(150, 20);
            $pdf->setFillColor(255, 0, 0);
            $pdf->Cell(10, 5, '', 1, 0, '', 1);

            $pdf->setFillColor(255, 255, 255);
            $pdf->Cell(10, 5, 'Nombre toi : ' . $donnees[2][1], 0, 0, '', 1);

            $pdf->setXY(150, 30);
            $pdf->setFillColor(0, 200, 0);
            $pdf->Cell(10, 5, '', 1, 0, '', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->Cell(10, 5, 'Nombre moi : ' . $donnees[1][1], 0, 0, '', 1);
            $pdf->SetFont('helvetica', '', 9);

            $pdf->setXY(10, 50);
            // $html .= '<div><p>Mr. ' . $informations[0]["nom"] . '</p><p>Devise choisi : ' . $informations[1]["symbol"] . '</p><p>Devise de base : ' . $informations[2] . '</p></div>';
            $html .= '<table cellpadding="4" style="width: 100%; border-collapse: collapse;">';
            $html .= '<thead>';
            $html .= '<tr>';
            foreach ($headers as $header)
                $html .= '<th >' . $header . '</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $som = 0;
            foreach ($donnees[0] as $operation) {
                $x = "";
                if ($operation["type_operation"] === 'toi') {
                    $som -= $operation["total"];
                    $x = "- ";
                    $html .= '<tr bgcolor="rgb(255, 0, 0)">';
                } else {
                    $som += $operation["total"];
                    $x = "+ ";
                    $html .= '<tr bgcolor="rgb(0, 200, 0)">';
                }
                $html .= '<td align="center">' . $operation["date"] . '</td>';
                $html .= '<td align="center">' . $operation["comments"] . '</td>';
                $html .= '<td align="center">' . $operation["ville"] . '</td>';
                $html .= '<td align="right">' . $operation["quantity"] . '</td>';
                $html .= '<td align="right">' . $operation["prix"] . '</td>';
                $html .= '<td align="right">' . $operation["percentage"] . '</td>';
                $html .= '<td align="right">' . $x . number_format($operation["total"], 2) . '</td>';
                if ($som > 0)
                    $html .= '<td align="right">' . "+" . number_format($som, 2) . '</td>';
                else
                    $html .= '<td align="right">' . number_format($som, 2) . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr>
        <td colspan="4" align="right">Total moi</td>
        <td colspan="4"  align="right">' . "+ " . number_format($donnees[1][0], 2) . '</td>
        </tr>';

            $html .= '<tr>
        <td colspan="4" align="right">Total toi</td>
        <td colspan="4" align="right">' . "- " . number_format($donnees[2][0], 2) . '</td>
        </tr>';

            $html .= '<tr>
        <td colspan="4" align="right">Total</td>
        <td colspan="4" align="right">' . number_format($donnees[1][0] - $donnees[2][0], 2) . '</td>
        </tr>';

            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');
            return $pdf->Output('mon_document.pdf', 'I');
        }
    }
    public function excel()
    {
        $items = Session::get('actions');
        if ((array_key_exists('operation', $items) && in_array("exporter", $items["operation"]))) {
            return redirect()->route("403");
        } else {
            return Excel::download(new ExportOperation, 'Opération.xlsx');
        }
    }
}
