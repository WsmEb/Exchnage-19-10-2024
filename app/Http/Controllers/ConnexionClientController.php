<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Converte;
use App\Models\Depose;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\Operation;
use App\Models\Transfert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ConnexionClientController extends Controller
{

    public function dashboard($client)
    {
        if (Auth::guard('client')->check() && Auth::guard('client')->user()->username == $client ){
            $totalOperations = Operation::where('client',$client)->count();
            $totalTransferts = Transfert::where('client',$client)->count();
            $totalConvertes = Converte::where('client_username',$client)->count();
            $totalDeposes = Depose::where('client',$client)->count();
            $lastDeposes = Depose::latest()->take(5)->get()->where('client',$client);
            $lastConvertes = Converte::latest()->take(5)->get()->where('client_username',$client);
            $lastTransfers = Transfert::latest()->take(5)->get()->where('client',$client);
            $lastOperations = Operation::latest()->take(5)->get()->where('client',$client);
            $entreprise = Entreprise::first();

            $operations = Operation::where('client',Auth::guard('client')->user()->username)->count();
            $transferts = Transfert::where('client',Auth::guard('client')->user()->username)->count();
            $convertes = Converte::where('client_username',Auth::guard('client')->user()->username)->count();
            // $deposers = Depose::count();
            $deposes = Depose::where('client',Auth::guard('client')->user()->username)->count();
            // $deposes = Depose::where('client',)->count();
            $convertes = Converte::where('client_username',Auth::guard('client')->user()->username)->count();
            $operations = Operation::where('client',Auth::guard('client')->user()->username)->count();
            $transferts = Transfert::where('client',Auth::guard('client')->user()->username)->count();
            $DeposerAction = Depose::where('client',Auth::guard('client')->user()->username)->where("type", "DEPOSER")->count();
            $RetraitAction = Depose::where('client',Auth::guard('client')->user()->username)->where("type", "RETRAIT")->count();
            $sumTotalFromAllClients = 0;
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
                $clientSold = number_format(SoldeClientByBase(Auth::guard('client')->user()->username), 2);
                $sumTotalFromAllClients = (float) str_replace(',', '',  $clientSold);
                $sumTotalFromAllClients = $sumTotalFromAllClients > 0 ? - $sumTotalFromAllClients : $sumTotalFromAllClients; 
            return view('clientdashboard',compact('transferts','sumTotalFromAllClients','operations','convertes','deposes','RetraitAction','DeposerAction','convertes','transferts','operations','entreprise','lastOperations','lastTransfers','lastConvertes','lastDeposes','totalOperations', 'totalTransferts', 'totalConvertes','totalDeposes'));
        }
        return view('login_client');
    }
    public function connexion()
    {
        return view('login_client');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('client')->attempt($credentials)) {
            $client = Client::where('username', $credentials['username'])->first();

            if (Auth::guard('client')->user()->bloque === 'oui')
                return redirect()->back()->with('error', 'Votre compte est bloqué.')->withInput();
            return redirect()->route('client.dashboard',['client' => $client->username]);
            //   return redirect()->intended('/client/dashboard');
        } else {
            return redirect()->back()->with('error', 'Nom d\'utilisateur ou mot de passe incorrect.')->withInput();
        }
    }


    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect('/client/login');
    }
}
