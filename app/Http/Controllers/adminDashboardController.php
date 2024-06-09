<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Converte;
use App\Models\Depose;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\Operation;
use App\Models\Transfert;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class adminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function index()
    {

        $items = Session::get('actions');
        if (array_key_exists('dashboard', $items) && in_array("tout", $items["dashboard"])) {
            return redirect()->route("403");
        } else {
            $getOperationDate = Operation::select("date", DB::raw("COUNT(*) as operation_count"))
                ->groupBy("date")
                ->orderBy("operation_count", "DESC")
                ->get();
            $aggregatedData = [];

            // Iterate over the operations to aggregate counts by month
            foreach ($getOperationDate as $operation) {
                // Parse the operation date
                $date = Carbon::parse($operation->date);

                // Get the month abbreviation
                $monthAbbreviation = $date->format("M");

                // Aggregate the count for each month
                if (!isset($aggregatedData[$monthAbbreviation])) {
                    $aggregatedData[$monthAbbreviation] = 0;
                }
                $aggregatedData[$monthAbbreviation] += $operation->operation_count;
            }

            // Convert the associative array to the required format
            $chartData = [];
            foreach ($aggregatedData as $month => $count) {
                $chartData[] = [$month => $count];
            }
            $entreprise = Entreprise::first();
            $MVPClient = Operation::select('client', DB::raw('COUNT(*) as property_count'))
                ->groupBy('client')
                ->orderBy('property_count', 'DESC')
                ->first();
            $MVPClients = Operation::select('client', DB::raw('COUNT(*) as property_count'))
                ->groupBy('client')
                ->limit(5)
                ->orderBy('property_count', 'DESC')
                ->get();
            $MVPDevise = Operation::select('devise', DB::raw('COUNT(*) as devise_count'))
                ->groupBy('devise')
                ->orderBy('devise_count', 'DESC')
                ->first();
            //  dd($MVPDevise);
            // dd(!isset($MVPClient));
            $clients = Client::count();
            $devises = Devise::count();
            $Alldevises = Devise::all();
            $comptables = User::whereNot("role", "admin")->count();
            $operations = Operation::count();
            $transferts = Transfert::count();
            $convertes = Converte::count();
            $deposers = Depose::count();
            $DeposerAction = Depose::where("type", "DEPOSER")->count();
            $RetraitAction = Depose::where("type", "RETRAIT")->count();
            $Total_transactions = $operations + $transferts + $convertes + $deposers;
            $lastDeposes = Depose::latest()->take(5)->get();
            $lastConvertes = Converte::latest()->take(5)->get();
            $lastTransfers = Transfert::latest()->take(5)->get();
            $lastOperations = Operation::latest()->take(5)->get();
            $operationss = Operation::count();


            function SoldeClientByDevise($client, $devise)
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
                    $total += SoldeClientByDevise($client, $devise->symbol) * $devise->base;
                return $total;
            }
            function SoldeDevisesByClients($client)
            {
                $soldes_devises = [];
                $devises = Devise::all();
                $entreprise = Entreprise::first();
                foreach ($devises as $devise)
                    $soldes_devises[] = [$devise->symbol, SoldeClientByDevise($client, $devise->symbol), SoldeClientByDevise($client, $devise->symbol) * $devise->base];
                return response()->json([
                    'soldes_devises' => $soldes_devises,
                    'entreprise' => $entreprise
                ]);
            }
            $sumTotalFromAllClients = 0;
            $clients_total_amount = Client::all()->map(function ($client) use (&$sumTotalFromAllClients) {
                $client->solde = number_format(SoldeClientByBase($client->username), 2);
                $sumTotalFromAllClients += (float) str_replace(',', '', $client->solde);;
            });

            return view('adminDashbord', compact('operationss','sumTotalFromAllClients', "clients", "lastTransfers", "lastOperations", "lastDeposes", "lastConvertes", "MVPClients", "Alldevises", "entreprise", "chartData", "devises", "MVPDevise", "comptables", "DeposerAction", "RetraitAction", "Total_transactions", "operations", "transferts", "convertes", "MVPClient"));
        }
    }
}
