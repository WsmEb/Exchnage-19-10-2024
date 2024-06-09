<?php

namespace App\Http\Controllers;

use App\Exports\ExportData;
use App\Models\Devise;
use App\Models\Entreprise; // Added for getBaseDevise method
use App\Models\PendingAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class DeviseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function index(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('devise', $items) && in_array("tout", $items["devise"])) {
            return redirect()->route("403");
        } else {
            $devise = Devise::all();
            $symbols = $this->getSymbols();
            $entreprise = Entreprise::first(); // Assuming you want to fetch the first entreprise record
            $dataexport = Devise::select('symbol', 'description', 'base')->get();
            Session::put('data', $dataexport->toArray());
            Session::put('header', ['symbole', 'Description', 'Devise de base']);
            Session::put('title', "Liste Devise (devise de base : " . $entreprise->base_devise . ")");
            $highlightId = $request->query('highlight_id');
            return view('devise', compact('devise', 'symbols', 'entreprise','highlightId'));
        }
    }


    public function add(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('devise', $items) && in_array("ajouter", $items["devise"])) {
            return redirect()->route("403");
        } else {
            $existingDevise = Devise::where('symbol', $request->symbol)->first();

            if ($existingDevise) {
                return redirect()->route("devise.index")->with('error', 'Le Devise avec cette identifient existe déjà.');
            }
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    "page" => "Devise",
                    'model' => "Devise",
                    'details' => json_encode(['symbol' =>  $request->symbol,'description' => $request->description , 'base' => $request->base]),
                ]);
            }else{
                $devise = Devise::create([
                    'symbol' => $request->symbol,
                    'description' => $request->description,
                    'base' => $request->base,
                ]);
            }


            return redirect()->route("devise.index")->with('success', 'Le devise a été créée avec succès.');
        }
    }

    public function delete(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('devise', $items) && in_array("supprimer", $items["devise"])) {
            return redirect()->route("403");
        } else {
            $entreprise = Entreprise::all()->first();

            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->route("devise.index")->with('error', 'Le mot de passe actuel est incorrect');
            }
            $devise = Devise::where('symbol', $request->symbol)->first();

            if ($devise && $devise->symbol != $entreprise->base_devise) {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Supprimer',
                        "page" => "Devise",
                        'model' => "Devise",
                        'details' => json_encode(['symbol' =>  $devise->symbol,'id' => $devise->symbol]),
                    ]);
                    return redirect()->route("devise.index")->with('success', 'Attend La Confirmation D`Admin.');
                }else{
                    $devise->delete();
                    return redirect()->route("devise.index")->with('success', 'Le Devise a été supprimé avec succès.');
                }

            } else {
                return redirect()->route("devise.index")->with('error', 'vous ne pouvez pas supprimer ce Devise car le Devise de base.');
            }
        }
    }

    public function update(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('devise', $items) && in_array("modifier", $items["devise"])) {
            return redirect()->route("403");
        } else {
            $devise = Devise::where('symbol', $request->symbol)->first();

            if ($devise) {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Modifier',
                        "page" => "Devise",
                        'model' => "Devise",
                        'details' => json_encode(['symbol' =>$request->symbol,'id' => $devise->symbol,'description' =>  $request->description,'base' => $request->base]),
                    ]);
                    return redirect()->route("devise.index")->with('success', 'Attend La Confirmation D`Admin.');
                }else{
                    $devise->symbol = $request->symbol;
                    $devise->description = $request->description;
                    $devise->base = $request->base;
                    $devise->save();
                    return redirect()->route("devise.index")->with('success', 'Le Devise a été modifié avec succès.');
                }

            } else {
                return redirect()->route("devise.index")->with('error', 'Le Devise n\'a pas été trouvé.');
            }
        }
    }

    /* 
    public function search($devise = NULL)
    {
        if ($devise == NULL) {
            $devise = Devise::all();
        } else {
            $devise = Devise::where('symbol', 'LIKE', '%' . $devise . '%')
                            ->orWhere('description', 'LIKE', '%' . $devise . '%')
                            ->get();
        }
        return $devise;
    }
    */

    public function getSymbols()
    {
        $symbols = Devise::pluck('symbol');
        return $symbols;
    }

    public function getBaseDevise($symbol)
    {
        $devise = Devise::where('symbol', $symbol)->first();
        if ($devise) {
            return $devise->base_devise;
        } else {
            return "Base devise not found for the selected symbol.";
        }
    }


    public function updateBaseDevise(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('devise', $items) && in_array("base", $items["devise"])) {
            return redirect()->route("403");
        } else {
            $symbol = $request->input('symbol');
            $entreprise = Entreprise::first();
            if ($entreprise) {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Modifier',
                        "page" => "Devise",
                        "model" => "ENTREPRISE",
                        'details' => json_encode(['id' => $entreprise->titre,'base_devise' => $symbol]),
                    ]);
                    return response()->json(['success' => true]);
                }else {
                    $entreprise->base_devise = $symbol;
                    $entreprise->save();
                    return response()->json(['success' => true]);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Entreprise not found']);
            }
        }
    }
    public function export()
    {
        $items = Session::get('actions');
        if (array_key_exists('devise', $items) && in_array("exporter", $items["devise"])) {
            return redirect()->route("403");
        } else {
            return Excel::download(new ExportData, 'devises.xlsx');
        }
    }
}
