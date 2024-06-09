<?php

namespace App\Http\Controllers;

use App\Exports\ExportStock;
use App\Models\Converte;
use App\Models\Devise;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use TCPDF;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function index()
    {
        $notclients = Session::get('clients')->toArray();
        $items = Session::get('actions');
        if (array_key_exists('stock', $items) && in_array("tout", $items["stock"])) {
            return redirect()->route("403");
        } else {
            $entreprise = Entreprise::first();
            $devises = Devise::all();
            $deposesTypeDepose = DB::table("deposes")
                ->join("clients", "deposes.client", "=", "clients.username")
                ->select(
                    "deposes.devise",
                    "deposes.client",
                    "clients.nom as nom",
                    DB::raw("
            SUM(CASE WHEN deposes.type = 'DEPOSER' THEN deposes.amount ELSE 0 END) -
            SUM(CASE WHEN deposes.type = 'RETRAIT' THEN deposes.amount ELSE 0 END) as totalDifference
        ")
                )

                ->whereNotIn("deposes.client", $notclients)
                ->groupBy("deposes.client", "deposes.devise", "clients.nom")

                ->get();

            $deviseDeBase = Devise::where("symbol", $entreprise->base_devise)->first();
            $TotalBalance = [];
            foreach ($deposesTypeDepose as $dep)
                $dep->totalDifference = $dep->totalDifference * convertedSymbolBase($dep->devise) / $deviseDeBase->base;

            $groupedData = $deposesTypeDepose->groupBy('client')->map(function ($items, $client) {
                $totalDifference = $items->sum('totalDifference');
                $devise = $items->pluck('devise')->unique()->values();
                $nom = $items->first()->nom; // Ajoutez cette ligne pour inclure le nom du client
                return [
                    'client' => $client,
                    'nom' => $nom, // Ajoutez cette ligne pour inclure le nom du client dans le résultat
                    'totalDifference' => $totalDifference,
                    'devise' => $devise
                ];
            })->toArray();
            // dd($groupedData);
            Session::put('stock', [$groupedData]);
            Session::put('client', ["ADMIN", "mySmbol_demo", $entreprise->base_devise]);
            return view('stock', compact("groupedData", "entreprise", "devises"));
        }
    }

    public function excel()
    {
        $items = Session::get('actions');
        if (array_key_exists('stock', $items) && in_array("exporter", $items["stock"])) {
            return redirect()->route("403");
        } else {
            return Excel::download(new ExportStock, 'stock.xlsx');
        }
    }
    public function pdf()
    {
        $items = Session::get('actions');
        if (array_key_exists('stock', $items) && in_array("exporter", $items["stock"])) {
            return redirect()->route("403");
        } else {
            $donnees = Session::has('stock') ? Session::get('stock') : [];
            $informations = Session::has('client') ? Session::get('client') : [];
            $headers = ['Identifient client', 'Nom client', 'Balance'];
            $html = '<style>th,td{border: 1px solid black;text-align:center;font-size:9px;}th{font-size:17px;background-color:gold;}</style>';
            // Tcpdf ==========================================================================================================
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetTitle('Deposes');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetMargins(PDF_MARGIN_LEFT, 2, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->AddPage();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'u', 20);
            $pdf->Cell(70, 20, "", 0, 0, '', 1);
            $pdf->Cell(70, 20, "#STOCK", 0, 0, '', 1);
            $pdf->SetFont('helvetica', 'b', 11);
            $pdf->setXY(10, 15);
            $pdf->setXY(10, 25);
            $pdf->setXY(10, 35);
            $pdf->Cell(70, 10, 'Devise de base : ' . $informations[2], 0, 0, '', 1);
            $pdf->setXY(10, 50);
            // $html .= '<div><p>Mr. ' . $informations[0]["nom"] . '</p><p>Devise choisi : ' . $informations[1]["symbol"] . '</p><p>Devise de base : ' . $informations[2] . '</p></div>';
            $html .= '<table cellpadding="4" border="1px" style="width: 100%; border-collapse: collapse">';
            $html .= '<thead />';
            $html .= '<tr bgcolor="#f96332" style="color:white">';
            foreach ($headers as $header)
                $html .= '<th >' . $header . '</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            foreach ($donnees[0] as $operation) {

                $x = "- ";
                $html .= '<tr bgcolor="rgb(255, 255, 255)">';

                $x = "+ ";
                $html .= '<tr />';

                $html .= '<td align="center">' . $operation["client"] . '</td>';
                $html .= '<td align="center">' . $operation["nom"] . '</td>';
                $html .= '<td align="center">' . $operation["totalDifference"] . ' ' . $informations[2] . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');
            return $pdf->Output('mon_document.pdf', 'I');
        }
    }

    public function search($val = '')
    {
        $notclients = Session::get('clients')->toArray();
        $entreprise = Entreprise::first();
        $devises = Devise::all();
        if ($val == '') {
            $deposesTypeDepose = DB::table("deposes")
                ->join("clients", "deposes.client", "=", "clients.username")
                ->select(
                    "deposes.devise",
                    "deposes.client",
                    "clients.nom as nom",
                    DB::raw("
            SUM(CASE WHEN deposes.type = 'DEPOSER' THEN deposes.amount ELSE 0 END) -
            SUM(CASE WHEN deposes.type = 'RETRAIT' THEN deposes.amount ELSE 0 END) as totalDifference
        ")
                )->whereNotIn("deposes.client", $notclients)
                ->groupBy("deposes.client", "deposes.devise", "clients.nom")
                ->get();
        } else {
            $deposesTypeDepose = DB::table("deposes")
                ->join("clients", "deposes.client", "=", "clients.username")
                ->select(
                    "deposes.devise",
                    "deposes.client",
                    "clients.nom as nom",
                    DB::raw("
                        SUM(CASE WHEN deposes.type = 'DEPOSER' THEN deposes.amount ELSE 0 END) -
                        SUM(CASE WHEN deposes.type = 'RETRAIT' THEN deposes.amount ELSE 0 END) as totalDifference
                    ")
                )
                ->where(function ($query) use ($val) {
                    $query->where('clients.nom', 'REGEXP', $val)
                        ->orWhere('clients.username', 'REGEXP', $val);
                })->whereNotIn("deposes.client", $notclients)
                ->groupBy("deposes.client", "deposes.devise", "clients.nom")
                ->get();
        }
        $deviseDeBase = Devise::where("symbol", $entreprise->base_devise)->first();
        $TotalBalance = [];
        foreach ($deposesTypeDepose as $dep)
            $dep->totalDifference = $dep->totalDifference * convertedSymbolBase($dep->devise) / $deviseDeBase->base;

        $groupedData = $deposesTypeDepose->groupBy('client')->map(function ($items, $client) {
            $totalDifference = $items->sum('totalDifference');
            $devise = $items->pluck('devise')->unique()->values();
            $nom = $items->first()->nom;
            return [
                'client' => $client,
                'nom' => $nom,
                'totalDifference' => $totalDifference,
                'devise' => $devise
            ];
        })->toArray();
        Session::put('stock', [$groupedData]);
        Session::put('client', ["ADMIN", "mySmbol_demo", $entreprise->base_devise]);
        $response = [
            'groupedData' => $groupedData,
            'entreprise' => $entreprise
        ];

        return response()->json($response);
    }



    function DetailClientenStock($client)
    {

        $entreprise = Entreprise::first();
        $devises = DB::table('devises')
            ->select('devises.symbol', 'devises.description', 'devises.base')
            ->leftJoin('deposes', 'devises.symbol', '=', 'deposes.devise')
            ->selectRaw('SUM(CASE WHEN deposes.type = "DEPOSER" THEN deposes.amount ELSE 0 END) 
                                - SUM(CASE WHEN deposes.type = "RETRAIT" THEN deposes.amount ELSE 0 END) AS solde')
            ->where('deposes.client', $client)
            ->groupBy('devises.symbol', 'devises.description', 'devises.base')
            ->get();

        $result = [];
        foreach ($devises as $devise) {
            $result[] = [$devise->symbol, $devise->description, $devise->solde, $devise->base * $devise->solde];
        }
        $response = [
            'devises' => $result,
            'entreprise' => $entreprise
        ];

        return response()->json($response);
    }
}
