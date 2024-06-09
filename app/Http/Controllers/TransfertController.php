<?php

namespace App\Http\Controllers;

use App\Exports\ExportTransfert;
use App\Models\Client;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\Historiques_Operations;
use App\Models\PendingAction;
use App\Models\Transfert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use TCPDF;

class TransfertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function index($client, $devise)
    {
        $notclients = Session::get('clients')->toArray();
        if (in_array($client, $notclients))
            return redirect()->route("403");

        $items = Session::get('actions');
        if ((array_key_exists('transfert', $items) && in_array("tout", $items["transfert"])) || (array_key_exists('client', $items) && in_array("detail", $items["client"]))) {
            return redirect()->route("403");
        } else {
            $client = Client::all()->where("username", $client)->first();
            $devise = Devise::all()->where("symbol", $devise)->first();
            $clients = Client::all();
            $devises = Devise::all();
            $entreprise = Entreprise::first();
            $historiques =  Historiques_Operations::where('client', $client->username)->where('devise', $devise->symbol)->get();
            $transferts = Transfert::where('devise', $devise->symbol)->where(function ($query) use ($client) {
                $query->where('expediteur', $client->username)->orWhere('recepteur', $client->username);
            })->get();

            $transferts_session = [];
            foreach ($transferts as $tr)
                $transferts_session[] = [$tr["id"], $tr["date"], $tr["recepteur"], $tr["expediteur"], $tr["solde"], $tr->info_recepteur->nom, $tr->info_expediteur->nom];
            $statistique_recepteur = $this->getTotalByType('recepteur', $devise->symbol, $client->username);
            $statistique_expediteur = $this->getTotalByType('expediteur', $devise->symbol, $client->username);

            Session::put('transferts', [$transferts_session, $statistique_recepteur, $statistique_expediteur]);
            Session::put('info_transfert', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);
            return view('transfert', compact("client", "clients", "devise", "devises", "transferts", "statistique_recepteur", "statistique_expediteur", "entreprise", "historiques"));
        }
    }
    public function add(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('transfert', $items) && in_array("ajouter", $items["transfert"]))) {
            return redirect()->route("403");
        } else {
            $expediteur = $request->type == "expediteur" ? $request->client : $request->client_principale;
            $recepteur =  $request->type == "recepteur" ? $request->client : $request->client_principale;
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    "model" => "TRANSFERT",
                     "page" => "transferts/".$request->client_principale."/".$request->devise,
                    'details' => json_encode([
                        'date' => $request->date,
                        'expediteur' => $expediteur,
                        'recepteur' => $recepteur,
                        'devise' => $request->devise,
                        'client' =>  $request->client_principale,
                        'solde' => $request->solde,
                    ]),
                ]);
                return  redirect()->route('transfert.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Attend la confirmation de admin');
            }else {

                $transfert = Transfert::create([
                    'date' => $request->date,
                    'expediteur' => $expediteur,
                    'recepteur' => $recepteur,
                    'devise' => $request->devise,
                    'client' =>  $request->client_principale,
                    'solde' => $request->solde,
                ]);
                return  redirect()->route('transfert.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Transfert a été créée avec succès.');
            }
        }
    }
    public function update(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('transfert', $items) && in_array("modifier", $items["transfert"]))) {
            return redirect()->route("403");
        } else {
            $expediteur = $request->type == "expediteur" ? $request->client : $request->client_principale;
            $recepteur =  $request->type == "recepteur" ? $request->client : $request->client_principale;
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Modifier',
                    "model" => "TRANSFERT",
                     "page" => "transferts/".$request->client_principale."/".$request->devise,
                    'details' => json_encode([
                        'date' => $request->date,
                        'expediteur' => $expediteur,
                        'recepteur' => $recepteur,
                        'devise' => $request->devise,
                        'client' =>  $request->client_principale,
                        'solde' => $request->solde,
                        'id' => $request->id,
                    ]),
                ]);
                return  redirect()->route('transfert.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Attend la confirmation de admin');
            }else {
                $transfert = Transfert::find($request->id);
                $transfert->date = $request->date;
                $transfert->expediteur =  $expediteur;
                $transfert->recepteur =  $recepteur;
                $transfert->solde = $request->solde;
                $transfert->save();
                return  redirect()->route('transfert.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Transfert a été sauvegardée avec succès.');
            
            }
        }
    }
    public function delete(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('transfert', $items) && in_array("supprimer", $items["transfert"]))) {
            return redirect()->route("403");
        } else {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->route('transfert.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('error', 'Le mot de passe actuel est incorrect');
            }

            $transfert = Transfert::all()->where("id", $request->transfert)->first();
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Supprimer',
                    "model" => "TRANSFERT",
                     "page" => "transferts/".$request->client_principale."/".$request->devise,
                    'details' => json_encode(["id" => $request->transfert]),
                ]);
                return  redirect()->route('transfert.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Attend la confirmation de admin');
            }else {
                $transfert->delete();
                // dd($transfert);
                return  redirect()->route('transfert.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Transfert a été supprimée avec succès.');
            }

        }
    }


    public function search($client, $devise, $col = NULL)
    {
        if ($col == NULL) {
            $transferts_db = Transfert::where('devise', $devise)->where(function ($query) use ($client) {
                $query->where('expediteur', $client)->orWhere('recepteur', $client);
            })->get();
            $statistique_recepteur = $this->getTotalByType('recepteur', $devise, $client);
            $statistique_expediteur = $this->getTotalByType('expediteur', $devise, $client);
        } else {
            $transferts_db = Transfert::where('devise', $devise)->where($col, $client)->get();
            $statistique_recepteur = $col == "recepteur" ? $this->getTotalByType('recepteur', $devise, $client) : [0, 0];
            $statistique_expediteur = $col == "expediteur" ? $this->getTotalByType('expediteur', $devise, $client) : [0, 0];
        }
        $transferts = [];
        foreach ($transferts_db as $tr)
            $transferts[] = [$tr["id"], $tr["date"], $tr["recepteur"], $tr["expediteur"], $tr["solde"], $tr->info_recepteur->nom, $tr->info_expediteur->nom];

        Session::put('transferts', [$transferts, $statistique_recepteur, $statistique_expediteur]);
        echo json_encode([$transferts, $statistique_recepteur, $statistique_expediteur, $statistique_recepteur[0] - $statistique_expediteur[0]]);
    }

    public function searchdate($client, $devise, $date1 = NULL, $date2 = NULL)
    {
        if ($date1 == NULL || $date2 == NULL) {
            $transferts_db = Transfert::where('devise', $devise)->where(function ($query) use ($client) {
                $query->where('expediteur', $client)->orWhere('recepteur', $client);
            })->get();
            $statistique_recepteur = $this->getTotalByType('recepteur', $devise, $client);
            $statistique_expediteur = $this->getTotalByType('expediteur', $devise, $client);
        } else {
            $transferts_db = Transfert::where('devise', $devise)->whereBetween('date', [$date1, $date2])->where(function ($query) use ($client) {
                $query->where('expediteur', $client)->orWhere('recepteur', $client);
            })->get();
            $statistique_recepteur = $this->getTotalByTypeAndDate('recepteur', $devise, $client, $date1, $date2);
            $statistique_expediteur = $this->getTotalByTypeAndDate('expediteur', $devise, $client, $date1, $date2);
        }
        $transferts = [];
        foreach ($transferts_db as $tr)
            $transferts[] = [$tr["id"], $tr["date"], $tr["recepteur"], $tr["expediteur"], $tr["solde"], $tr->info_recepteur->nom, $tr->info_expediteur->nom];

        Session::put('transferts', [$transferts, $statistique_recepteur, $statistique_expediteur]);
        echo json_encode([$transferts, $statistique_recepteur, $statistique_expediteur, $statistique_recepteur[0] - $statistique_expediteur[0]]);
    }


    public function getTotalByType($type, $devise, $client)
    {
        $totalByType = Transfert::select(DB::raw('SUM(solde) as total,count(*) as nb'))
            ->where('devise', $devise)->where($type, $client)
            ->groupBy($type)
            ->get();
        return count($totalByType) == 0 ? [0, 0] : [$totalByType[0]->total, $totalByType[0]->nb];
    }
    public function getTotalByTypeAndDate($type, $devise, $client, $date1, $date2)
    {
        $totalByType = Transfert::select(DB::raw('SUM(solde) as total,count(*) as nb'))
            ->where('devise', $devise)->where($type, $client)->whereBetween('date', [$date1, $date2])
            ->groupBy($type)
            ->get();
        return count($totalByType) == 0 ? [0, 0] : [$totalByType[0]->total, $totalByType[0]->nb];
    }
    public function pdf()
    {

        $items = Session::get('actions');
        if ((array_key_exists('transfert', $items) && in_array("exporter", $items["transfert"]))) {
            return redirect()->route("403");
        } else {
            $donnees = Session::has('transferts') ? Session::get('transferts') : [];
            $informations = Session::has('info_transfert') ? Session::get('info_transfert') : [];
            $headers = ["date", "Expéditeur", "Recepteur", "Solde (" . $informations[1]["symbol"] . ")", "Solde de base (" . $informations[2] . ")"];

            $html = '<style>th,td{border: 1px solid black;text-align:center;font-size:9px;}th{font-size:17px;background-color:gold;}</style>';


            // Tcpdf ==========================================================================================================
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetTitle('Transferts');
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
            $pdf->Cell(10, 5, 'Nombre Expéditeur : ' . $donnees[2][1], 0, 0, '', 1);

            $pdf->setXY(150, 30);
            $pdf->setFillColor(0, 200, 0);
            $pdf->Cell(10, 5, '', 1, 0, '', 1);
            $pdf->setFillColor(255, 255, 255);
            $pdf->Cell(10, 5, 'Nombre Recepteur : ' . $donnees[1][1], 0, 0, '', 1);
            $pdf->SetFont('helvetica', '', 9);

            $pdf->setXY(10, 50);
            $html .= '<table cellpadding="4" style="width: 100%; border-collapse: collapse;">';
            $html .= '<thead>';
            $html .= '<tr>';
            foreach ($headers as $header)
                $html .= '<th >' . $header . '</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            foreach ($donnees[0] as $tr) {
                if ($tr[3] === $informations[0]["username"])
                    $html .= '<tr bgcolor="rgb(255, 0, 0)">';
                else
                    $html .= '<tr bgcolor="rgb(0, 200, 0)">';

                // $html .= '<tr>';
                $html .= '<td align="center">' . $tr[1] . '</td>';
                $html .= '<td align="center">' . $tr[6] . '</td>';
                $html .= '<td align="center">' . $tr[5] . '</td>';
                $html .= '<td align="right">' . number_format($tr[4], 2) . '</td>';
                $html .= '<td align="right">' . number_format($tr[4] * $informations[1]["base"], 2)  . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr>
        <td colspan="3" align="right">Total moi</td>
        <td  align="right">' . "+ " . number_format($donnees[1][0], 2) . '</td>
        <td  align="right">' . "+ " . number_format($donnees[1][0] * $informations[1]["base"], 2) . '</td>
        </tr>';

            $html .= '<tr>
        <td colspan="3" align="right">Total toi</td>
        <td align="right">' . "- " . number_format($donnees[2][0], 2) . '</td>
        <td align="right">' . "- " . number_format($donnees[2][0] * $informations[1]["base"], 2) . '</td>
        </tr>';

            $html .= '<tr>
        <td colspan="3" align="right">Total</td>
        <td align="right">' . number_format($donnees[1][0] - $donnees[2][0], 2) . '</td>
        <td align="right">' . number_format(($donnees[1][0] - $donnees[2][0]) * $informations[1]["base"], 2) . '</td>
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
        if ((array_key_exists('transfert', $items) && in_array("exporter", $items["transfert"]))) {
            return redirect()->route("403");
        } else {
            return Excel::download(new ExportTransfert, 'Transfert.xlsx');
        }
    }
}
