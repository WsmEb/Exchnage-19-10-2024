<?php

namespace App\Http\Controllers;

use App\Exports\ExportData;
use App\Models\Client;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\PendingAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use TCPDF;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function index(Request $request)
    {

        // dd(Session::get('data'));
        $items = Session::get('actions');
        $notclients = Session::get('clients')->toArray();

        // dd($notclients);
        if (array_key_exists('client', $items) && in_array("tout", $items["client"])) {
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
            $devises = Devise::all();
            $clients = Client::select('username', 'nom', 'localisation', 'commentaire')
                ->whereNotIn('username', $notclients)
                ->get()
                ->map(function ($client) {
                    $client->solde = number_format($this->SoldeClientByBase($client->username), 2);
                    return $client;
                });

            $dataexport = Client::select('username', 'nom', 'localisation', 'commentaire')->whereNot("username", $notclients)->get()->map(function ($client) {
                $client->solde = number_format($this->SoldeClientByBase($client->username), 2);
                return $client;
            });
            $entreprise = Entreprise::first();

            // dd($dataexport->toArray());
            Session::put('data', $clients->toArray());
            Session::put('header', ['Username', 'Nom', 'Localisation', 'Commentaire', 'solde']);
            Session::put('title', "Liste des clients");
            return view('clients', compact('clients', 'devises', 'entreprise', "items", "villes"));
        }
    }
    public function add(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('client', $items) && in_array("ajouter", $items["client"])) {
            return redirect()->route("403");
        } else {
            $existingClient = Client::where('username', $request->username)->first();
            if ($existingClient) {
                return redirect()->route("clients.index")->with('error', 'Le client avec cette identifient existe déjà.');
            }

            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    "page" => "clients",
                    "model" => "CLIENTS",
                    'details' => json_encode([
                        'username' => $request->username,
                        'nom' => $request->nom,
                        'localisation' => $request->localisation,
                        'commentaire' => $request->commentaire,
                        'password' => Hash::make($request->username),
                        'bloque' => 'non',
                    ]),
                ]);
                return redirect()->route("clients.index")->with('pending', 'Attendes La Confirmation d`admin ');
            } else {
                $client = Client::create([
                    'username' => $request->username,
                    'nom' => $request->nom,
                    'localisation' => $request->localisation,
                    'commentaire' => $request->commentaire,
                    'password' => Hash::make($request->username),
                    'bloque' => 'non',
                ]);
                return redirect()->route("clients.index")->with('success', 'Le client a été créée avec succès.');
            }
        }
    }
    public function delete(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('client', $items) && in_array("supprimer", $items["client"])) {
            return redirect()->route("403");
        } else {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->route("clients.index")->with('error', 'Le mot de passe actuel est incorrect');
            }
            $Client = Client::where('username', $request->username)->first();
            if ($Client) {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Supprimer',
                        "model" => "CLIENTS",
                        "page" => "clients",
                        'details' => json_encode(["id" => $Client->username]),
                    ]);
                    return redirect()->route("clients.index")->with('success', 'Attendes La Confirmation d`admin ');
                }
                $Client->delete();
                return redirect()->route("clients.index")->with('success', 'Le client a été supprimé avec succès.');
            }
        }
    }
    public function update(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('client', $items) && in_array("modifier", $items["client"])) {
            return redirect()->route("403");
        } else {
            $Client = Client::where('username', $request->username)->first();
            if ($Client) {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Modifier',
                        "page" => "clients",
                        "model" => "CLIENTS",
                        'details' => json_encode(['id' => $Client->username, 'nom' => $request->nom, 'localisation' => $request->localisation, 'commentaire' => $request->commentaire]),
                    ]);
                    return redirect()->route("clients.index")->with('success', 'Attendes La Confirmation d`admin ');
                } else {
                    $Client->nom = $request->nom;
                    $Client->localisation = $request->localisation;
                    $Client->commentaire = $request->commentaire;
                    $Client->save();
                    return redirect()->route("clients.index")->with('success', 'Le client a été modifié avec succès.');
                }
            }
        }
    }
    public function update_password(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('client', $items) && in_array("motpasse", $items["client"])) {
            return redirect()->route("403");
        } else {
            if ($request->password === $request->password_confirmation) {
                $Client = Client::where('username', $request->username)->first();
                if ($Client) {
                    if (auth()->user()->role == 'comptable') {
                        PendingAction::create([
                            'comptable' => auth()->id(),
                            'action' => 'Modifier',
                            "page" => "clients",
                            "model" => "CLIENTS",
                            'details' => json_encode(['id' => $Client->username, 'password' => Hash::make($request->password)]),
                        ]);
                        return redirect()->route("clients.index")->with('success', 'Attendes La Confirmation d`admin');
                    } else {
                        $Client->password = Hash::make($request->password);
                        $Client->save();
                        return redirect()->route("clients.index")->with('success', 'Le client a été modifié le mot de passe avec succès.');
                    }
                }
            } else
                return redirect()->route("clients.index")->with('error', 'Confirmer le mot de passe');
        }
    }
    public function update_verrouiller(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('client', $items) && in_array("verrouiller", $items["client"])) {
            return redirect()->route("403");
        } else {
            $Client = Client::where('username', $request->username)->first();
            if ($Client) {
                $value = $Client->bloque;
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Modifier',
                        "page" => "clients",
                        "model" => "CLIENTS",
                        'details' => json_encode(['id' => $Client->username, 'nom' => $Client->nom, 'localisation' => $Client->localisation, 'commentaire' => $Client->commentaire, 'bloque' => $value === "non" ? "oui" : "non"]),
                    ]);
                    return redirect()->route("clients.index")->with('success', 'Attendes La Confirmation d`admin ');
                } else {
                    $Client->bloque = $value === "non" ? "oui" : "non";
                    $Client->save();
                    $message =  $value === "non" ? "bloqué" : "débloqué";
                    return redirect()->route("clients.index")->with('success', 'Le client a été modifié ' . $message . ' avec succès.');
                }
            }
        }
    }

    public function search($client = NULL)
    {
        $notclients = Session::get('clients')->toArray();
        if ($client == NULL) {
            $clients = Client::select('username', 'nom', 'localisation', 'commentaire')
                ->whereNotIn('username', $notclients)
                ->get();
        } else {
            $clients = Client::select('username', 'nom', 'localisation', 'commentaire')
                ->where(function ($query) use ($client) {
                    $query->where('username', 'REGEXP', $client)
                        ->orWhere('nom', 'REGEXP', $client)
                        ->orWhere('localisation', 'REGEXP', $client)
                        ->orWhere('commentaire', 'REGEXP', $client);
                })->whereNotIn('username', $notclients)->get();
        }
        $clients = $clients->map(function ($client) {
            $client->solde = number_format($this->SoldeClientByBase($client->username), 2);
            return $client;
        });

        $entreprise = Entreprise::first();
        Session::put('data', $clients->toArray());
        Session::put('header', ['Username', 'Nom', 'Localisation', 'Commentaire', "solde"]);
        Session::put('title', "Liste des clients");
        return response()->json([
            'clients' => $clients,
            'entreprise' => $entreprise
        ]);
    }

    public function operation(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('client', $items) && in_array("detail", $items["client"])) {
            return redirect()->route("403");
        } else {
            $client = $request->client;
            $devise = $request->typedevise;
            return redirect()->route($request->page . '.index', ['client' => $client, 'devise' => $devise]);
        }
    }
    public function export()
    {
        $items = Session::get('actions');
        if (array_key_exists('client', $items) && in_array("exporter", $items["client"])) {
            return redirect()->route("403");
        } else {
            return Excel::download(new ExportData, 'clients.xlsx');
        }
    }


    public function SoldeClientByDevise($client, $devise)
    {
        // total operation
        $resultat = DB::table('operations')
            ->select(DB::raw('(SUM(CASE WHEN type_operation = "moi" THEN total ELSE 0 END) - SUM(CASE WHEN type_operation = "toi" THEN total ELSE 0 END)) as total_operations'))
            ->where('client', $client)
            ->where('devise', $devise)
            ->first();
        $totaloperations = $resultat->total_operations;

        $sum_recepteur = DB::table('transferts')->where('recepteur', $client)->where('devise', $devise)->sum('solde');
        $sum_expediteur = DB::table('transferts')->where('expediteur', $client)->where('devise', $devise)->sum('solde');
        $totalTransferts = $sum_recepteur - $sum_expediteur;


        $getConvertedAmountTotal = DB::table('convertes')->where("client_username", $client)->where("devise", $devise)->get();
        $getRecevedAmountTotal = DB::table('convertes')->where("client_username", $client)->where("convertedSymbol", $devise)->get();
        $ConvertedAmountQueue = [];
        $RecevedAmountQueue = [];
        foreach ($getRecevedAmountTotal as $convert)
            $RecevedAmountQueue[$convert->id] = $convert->amount * ConvertedSymbolBase($convert->devise) / ConvertedSymbolBase($convert->convertedSymbol);
        $totalRecevedAmount = array_sum($RecevedAmountQueue);
        foreach ($getConvertedAmountTotal as $convert)
            $ConvertedAmountQueue[$convert->id] = $convert->amount;
        $totalConvertedAmount = array_sum($ConvertedAmountQueue);
        $totalconvertir = $totalRecevedAmount - $totalConvertedAmount;


        $result = DB::table('deposes')
            ->select(DB::raw('
            SUM(CASE WHEN type = "deposer" THEN amount ELSE 0 END) -
            SUM(CASE WHEN type = "retrait" THEN amount ELSE 0 END) as total_amount
        '))
            ->where('client', $client)
            ->where('devise', $devise)
            ->first();

        $totalstock =  $result->total_amount;


        $total = $totaloperations + $totalTransferts + $totalconvertir - $totalstock;
        return $total;
        // echo $totaloperations ."<br>". $totalTransferts ."<br>". $totalconvertir  ."<br>". $totalstock."<br>". $total;
    }

    function SoldeClientByBase($client)
    {
        $total = 0;
        $devises = Devise::all();
        foreach ($devises as $devise)
            $total += $this->SoldeClientByDevise($client, $devise->symbol) * $devise->base;
        return $total;
    }
    function SoldeDevisesByClients($client)
    {
        $soldes_devises = [];
        $devises = Devise::all();
        $entreprise = Entreprise::first();
        foreach ($devises as $devise)
            $soldes_devises[] = [$devise->symbol, $this->SoldeClientByDevise($client, $devise->symbol), $this->SoldeClientByDevise($client, $devise->symbol) * $devise->base];
        return response()->json([
            'soldes_devises' => $soldes_devises,
            'entreprise' => $entreprise
        ]);
    }


    public function exportPDF()
    {
        $items = Session::get('actions');

        if (array_key_exists('client', $items) && in_array("exporter", $items["client"])) {
            return redirect()->route("403");
        } else {
            $donnees = Session::has('data') ? Session::get('data') : [];
            $headers = Session::has('header') ? Session::get('header') : [];
            $titre = Session::has('title') ? Session::get('title') : "";

            // dd($:donnees);
            $html = '<style>th,td{border: 1px solid black;text-align:center;}th{font-size:17px;background-color:gold;}</style><h1 style="text-align:center;">' . $titre .  '</h1>';
            $html .= '<table cellpadding="4" style="width: 100%; border-collapse: collapse;">';
            $html .= '<thead>';
            $html .= '<tr>';
            foreach ($headers as $header)
                $html .= '<th >' . $header . '</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($donnees as $donnee) {
                // print_r($donnee["solde"]);
                $bg = $donnee["solde"] >= 0 ? 'green' : 'red';
                $html .= '<tr  bgcolor="' . $bg . '" >';
                foreach ($donnee as $d)
                    $html .= '<td>' . $d . '</td>';
                $html .= '</tr>';
            }


            $html .= '</tbody>';
            $html .= '</table>';

            // Tcpdf ==========================================================================================================
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetTitle('Mon premier document PDF');
            // $pdf->SetHeaderData(false, false, "dsfds" . ' 023', false);

            // set header and footer fonts
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 2, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->AddPage();

            $pdf->writeHTML($html, true, false, true, false, '');
            return $pdf->Output('mon_document.pdf', 'I');
        }
    }


    public function searchbySolde($type = NULL)
    {
        $notclients = Session::get('clients')->toArray();
        $clients1 = Client::select('username', 'nom', 'localisation', 'commentaire')
            ->whereNotIn('username', $notclients)
            ->get()
            ->map(function ($client) {
                $client->solde = number_format($this->SoldeClientByBase($client->username), 2);
                return $client;
            });
        
        $clients = [];

        switch ($type) {
            case "egal":
                foreach ($clients1 as $client)
                    if ($client->solde == 0)
                        $clients[] = $client->toArray();
                break;
            case "plus":
                foreach ($clients1 as $client)
                    if ($client->solde > 0)
                        $clients[] = $client->toArray();
                break;
            case "mois":
                foreach ($clients1 as $client)
                    if ($client->solde < 0)
                        $clients[] = $client->toArray();
                break;
            default:
                break;
        }


        $entreprise = Entreprise::first();
        Session::put('data', $clients);
        Session::put('header', ['Username', 'Nom', 'Localisation', 'Commentaire', "solde"]);
        Session::put('title', "Liste des clients");
        return response()->json([
            'clients' => $clients,
            'entreprise' => $entreprise
        ]);
    }
}
