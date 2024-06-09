<?php

namespace App\Http\Controllers;

use App\Models\Converte;
use App\Models\Depose;
use App\Models\DetailHistoriquesOperation;
use App\Models\Historiques_Operations;
use App\Models\Operation;
use App\Models\PendingAction;
use App\Models\Transfert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class HistoriqueOperationsController extends Controller
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
        if (array_key_exists('historique', $items) && in_array("tout", $items["historique"])) {
            return redirect()->route("403");
        } else {
            $historiques = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')->whereNotIn("client", $notclients)
                ->get();
            return view('historiques', compact('historiques'));
        }
    }
    public function searchbydate($datedebut = NULL, $datefin = NULL)
    {
        $notclients = Session::get('clients')->toArray();
        if ($datedebut == NULL || $datefin == NULL) {
            $historiques = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')->whereNotIn("client", $notclients)
                ->get();
        } else {
            $historiques = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')->whereNotIn("client", $notclients)
                ->whereBetween('datehistorique', [$datedebut, $datefin])
                ->get();
        }
        echo json_encode($historiques);
    }
    public function searchbycollonne($col = NULL, $val = NULL)
    {

        $notclients = Session::get('clients')->toArray();
        if ($val == NULL) {
            $historiques = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')->whereNotIn("client", $notclients)
                ->get();
        } else {
            $query = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client');
            if ($col === 'client')
                $query->where('clients.nom', 'REGEXP', $val);
            elseif ($col === 'commentaire')
                $query->where('historiques_operations.commentaire', 'REGEXP', $val);
            $historiques = $query->whereNotIn("client", $notclients)->get();
        }
        echo json_encode($historiques);
    }


    public function delete(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('historique', $items) && in_array("supprimer", $items["historique"])) {
            return redirect()->route("403");
        } else {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->route('historique.index')->with('error', 'Le mot de passe actuel est incorrect');
            }
            $historique = Historiques_Operations::all()->where("id", $request->id)->first();
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Supprimer',
                    "page" => "historique",
                    "model" => "HISTORIQUES_OPERATIONS",
                    'details' => json_encode(["id" =>  $request->id]),
                ]);
                return  redirect()->route('historique.index')->with('success', 'Attend la confirmation d`admin');
            } else {
                $historique->delete();
                return  redirect()->route('historique.index')->with('success', 'Historique a été supprimée avec succès.');
            }
        }
    }

    public function update(Request $request)
    {
        $items = Session::get('actions');
        if (array_key_exists('historique', $items) && in_array("modifier", $items["historique"])) {
            return redirect()->route("403");
        } else {
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Modifier',
                    "page" => "historique",
                    "model" => "HISTORIQUES_OPERATIONS",
                    'details' => json_encode([
                        "id" =>  $request->id,
                        'datehistorique' => $request->date,
                        'commentaire' => $request->commentaire,
                        'valeur' => $request->solde
                    ]),
                ]);
                return  redirect()->route('historique.index')->with('success', 'Attend la confirmation d`admin');
            } else {
                $historique = Historiques_Operations::find($request->id);
                $historique->datehistorique = $request->date;
                $historique->commentaire = $request->commentaire;
                $historique->valeur = $request->solde;
                $historique->save();
                return  redirect()->route('historique.index')->with('success', 'Historique a été sauvegardée avec succès.');
            }
        }
    }


    // Operations Historiques ************************************************************************************************************************************************************

    public function operations_add(Request $request)
    {
        $id_hist = date('YmdHis');
        if (auth()->user()->role == 'comptable') {
            // Store in PendingAction for admin approval
            PendingAction::create([
                'comptable' => auth()->id(),
                'action' => 'Ajouter',
                'model' => 'HISTORIQUES_OPERATIONS',
                "page" => "operations/" . $request->client . "/" . $request->devise,
                'details' => json_encode([
                    'id' => $id_hist,
                    'datehistorique' => $request->date,
                    'commentaire' => $request->commentaire,
                    'valeur' => $request->valeur,
                    'client' => $request->client,
                    'devise' => $request->devise,
                    'operations_check' => $request->operations_check,
                ]),
            ]);



            return response()->json(['success' => true, 'message' => 'Attendez la confirmation de l\'administrateur.']);
        } else {
            // Directly store in Historiques_Operations and DetailHistoriquesOperations for admin
            $historique = new Historiques_Operations;
            $historique->id = $id_hist;
            $historique->datehistorique = $request->date;
            $historique->commentaire = $request->commentaire;
            $historique->valeur = $request->valeur;
            $historique->client = $request->client;
            $historique->devise = $request->devise;
            $historique->save();

            foreach ($request->operations_check as $operation_id) {
                $operation = Operation::find($operation_id);
                if ($operation) {
                    DetailHistoriquesOperation::create([
                        'comments' => $operation->comments,
                        'date' => $operation->date,
                        'percentage' => $operation->percentage,
                        'total' => $operation->total,
                        'quantity' => $operation->quantity,
                        'type_operation' => $operation->type_operation,
                        'ville' => $operation->ville,
                        'prix' => $operation->prix,
                        'id_historique' => $id_hist,
                        'created_at' => $operation->created_at,
                        'updated_at' => $operation->updated_at,
                    ]);
                }
            }

            Operation::whereIn('id', $request->operations_check)->delete();
            return response()->json(['success' => true, 'message' => 'Historique créé avec succès. Opérations ajoutées avec succès à l\'historique et supprimées de la liste des opérations.']);
        }
    }

    public function operations_addexiste(Request $request)
    {

        $historique = Historiques_Operations::find($request->id_historique);
        $historique->valeur = $historique->valeur + $request->valeur;



        $operations_action = $request->operations_check;
        $id_hist = $request->id_historique;
        try {
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'model' => 'DETAIL_HISTORIQUES_OPERATIONS',
                    "page" => "operations/" . $historique->client . "/" . $historique->devise,
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    'details' => json_encode([
                        'operations_check' => $operations_action,
                        'id' =>  $id_hist,
                        'commentaire' => $historique->commentaire,
                        'valeur' => $historique->valeur,
                        'client' => $historique->client,
                        'devise' => $historique->devise,
                        'requestValeur' => $request->valeur,
                        'datehistorique' => $id_hist
                    ])
                ]);

                return response()->json(['success' => true, 'message' => 'Your action is pending approval from the admin.']);
            }
            foreach ($operations_action as $operation_id) {
                $operation = Operation::find($operation_id);
                if ($operation) {
                    DB::table('detail_historiques_operations')->insert([
                        'comments' => $operation->comments,
                        'date' => $operation->date,
                        'percentage' => $operation->percentage,
                        'total' => $operation->total,
                        'quantity' => $operation->quantity,
                        'type_operation' => $operation->type_operation,
                        'ville' => $operation->ville,
                        'prix' => $operation->prix,
                        'id_historique' => $id_hist,
                        'created_at' => $operation->created_at,
                        'updated_at' => $operation->updated_at,
                    ]);
                    $historique->save();
                }
            }
            Operation::whereIn('id', $operations_action)->delete();

            return response()->json(['success' => true, 'message' => 'Operations added to history and deleted from operations list.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function operations_show($id)
    {
        $items = Session::get('actions');
        if ((array_key_exists('historique', $items) && in_array("tout", $items["historique"])) || (array_key_exists('historique', $items) && in_array("detail", $items["historique"]))) {
            return redirect()->route("403");
        } else {
            $historique = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')
                ->where("historiques_operations.id", $id)
                ->first();

            $detail_historiques = DB::table('detail_historiques_operations')
                ->where("id_historique", $id)
                ->get();

            $total = DB::table('detail_historiques_operations')
                ->select(
                    DB::raw('SUM(CASE WHEN type_operation = "moi" THEN total ELSE 0 END) AS sommeTotalMoi'),
                    DB::raw('SUM(CASE WHEN type_operation = "toi" THEN total ELSE 0 END) AS sommeTotalToi')
                )
                ->where('id_historique', $id)
                ->first();
            $notclients = Session::get('clients')->toArray();
            if (in_array($historique->client, $notclients))
                return redirect()->route("403");
            return view('detail_historique_operations', compact('historique', 'detail_historiques', 'total'));
        }
    }

    public function operations_deletedetail(Request $request)
    {

        try {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password))
                return back()->with('error', 'Le mot de passe actuel est incorrect');

            $detail_historique = DB::table('detail_historiques_operations')->where("id", $request->id)->first();
            $idhist = $detail_historique->id_historique;
            $historique = Historiques_Operations::find($idhist);
            $historique->valeur =  $historique->valeur + $request->solde;


            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Supprimer',
                    'model' => 'DETAIL_HISTORIQUES_OPERATIONS',
                    'page' => 'historiquesoperations/' . $idhist,
                    'details' => json_encode([
                        'id' => $request->id,
                        'historique_id' => $idhist,
                        'valeur' =>  $historique->valeur

                    ]),
                ]);
                return back()->with('success', 'Your action is pending approval from the admin.');
            } else {
                $deleted = DB::table('detail_historiques_operations')
                    ->where('id', $request->id)
                    ->delete();
                $historique->save();
                if ($deleted)
                    return back()->with('success', 'Historique a été supprimée avec succès.');
                else
                    return back()->with('error', 'Erreur lors de la suppression de l\'historique.');
            }
        } catch (\Exception $e) {
            return back()->with('success', 'Historique a été supprimée avec succès.');
        }
    }

    public function operations_restoredetail(Request $request)
    {
        try {
            $detail_historique = DB::table('detail_historiques_operations')->where("id", $request->id)->first();
            $historique = Historiques_Operations::find($detail_historique->id_historique);
            $historique->valeur =  $historique->valeur + $request->solde;
            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    'model' => 'OPERATION',
                    'page' => 'historiquesoperations/' . $detail_historique->id_historique,
                    'details' => json_encode([
                        'hover' => $request->id,
                        'detail_historique_id' =>  $request->id,
                        'id' => $detail_historique->id_historique,
                        'from' => 'DETAIL_HISTORIQUE_OPERATION',
                        'comments' => $detail_historique->comments,
                        'date' => $detail_historique->date,
                        'valeur' =>  $historique->valeur,
                        'percentage' => $detail_historique->percentage,
                        'total' => $detail_historique->total,
                        'quantity' => $detail_historique->quantity,
                        'type_operation' => $detail_historique->type_operation,
                        'ville' => $detail_historique->ville,
                        'prix' => $detail_historique->prix,
                        'client' => $historique->client,
                        'devise' => $historique->devise,
                        'created_at' => $detail_historique->created_at,
                        'updated_at' => $detail_historique->updated_at,
                    ]),
                ]);
                return back()->with('success', 'Your action is pending approval from the admin.');
            } else {
                DB::table('operations')->insert([
                    'comments' => $detail_historique->comments,
                    'date' => $detail_historique->date,
                    'percentage' => $detail_historique->percentage,
                    'total' => $detail_historique->total,
                    'quantity' => $detail_historique->quantity,
                    'type_operation' => $detail_historique->type_operation,
                    'ville' => $detail_historique->ville,
                    'prix' => $detail_historique->prix,
                    'client' => $historique->client,
                    'devise' => $historique->devise,
                    'created_at' => $detail_historique->created_at,
                    'updated_at' => $detail_historique->updated_at,
                ]);
                $historique->save();
                $deleted = DB::table('detail_historiques_operations')->where('id', $request->id)->delete();
                if ($deleted)
                    return back()->with('success', 'Historique a été désarchivé avec succès.');
                else
                    return back()->with('error', 'Erreur lors de la désarchivage de l\'historique.');
            }
        } catch (\Exception $e) {
            return back()->with('success', 'Historique a été supprimée avec succès.');
        }
    }

    // Transferts Historiques ************************************************************************************************************************************************************

    public function transferts_add(Request $request)

    {
        $historique = new Historiques_Operations;
        $historique->id = $id_hist = date('YmdHis');
        if (auth()->user()->role == 'comptable') {
            // Store in PendingAction for admin approval
            PendingAction::create([
                'comptable' => auth()->id(),
                'action' => 'Ajouter',
                'model' => 'HISTORIQUES_OPERATIONS',
                'page' => "transferts/" . $request->client . "/" . $request->devise,
                'details' => json_encode([
                    'id' => $id_hist,
                    'datehistorique' => $request->date,
                    'commentaire' => $request->commentaire,
                    'valeur' => $request->valeur,
                    'client' => $request->client,
                    'devise' => $request->devise,
                    'transfert_action' => $request->transferts_check,
                ]),
            ]);
            return response()->json(['success' => true, 'message' => 'Attendez la confirmation de l\'administrateur.']);
        } else {
            $historique->datehistorique = $request->date;
            $historique->commentaire = $request->commentaire;
            $historique->valeur = $request->valeur;
            $historique->client = $request->client;
            $historique->devise = $request->devise;
            $historique->save();
            $transferts_action = $request->transferts_check;
            try {
                foreach ($transferts_action as $transfert_id) {
                    $transfert = Transfert::find($transfert_id);
                    if ($transfert) {
                        DB::table('detail_historiques_transferts')->insert([
                            'date' => $transfert->date,
                            'expediteur' => $transfert->expediteur,
                            'recepteur' => $transfert->recepteur,
                            'solde' => $transfert->solde,
                            'id_historique' => $id_hist,
                            'created_at' => $transfert->created_at,
                            'updated_at' => $transfert->updated_at,
                        ]);
                        Transfert::whereIn('id', $transferts_action)->delete();
                    } else {
                        throw new \Exception("L'opération avec l'ID $transfert_id n'existe pas.");
                    }
                }

                return response()->json(['success' => true, 'message' => 'Historique créé avec succès. Transferts ajoutées avec succès à l\'historique et supprimées de la liste des transferts.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }
    public function transferts_addexiste(Request $request)
    {
        $historique = Historiques_Operations::find($request->id_historique);
        $historique->valeur =  $historique->valeur + $request->valeur;

        $id_hist = $request->id_historique;
        $transferts_action = $request->transferts_check;
        try {
            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    'model' => 'DETAIL_HISTORIQUES_TRANSFERTS',
                    'page' =>  "transferts/" . $historique->client . "/" . $historique->devise,
                    'details' => json_encode([
                        'transfert_action' => $transferts_action,
                        'historique_id' => $id_hist,
                        'valeur' =>  $historique->valeur
                    ]),
                ]);
                return response()->json(['success' => true, 'message' => 'Attendez la confirmation de l\'administrateur.']);
            } else {
                foreach ($transferts_action as $transfert_id) {
                    $transfert = Transfert::find($transfert_id);
                    if ($transfert) {
                        DB::table('detail_historiques_transferts')->insert([
                            'date' => $transfert->date,
                            'expediteur' => $transfert->expediteur,
                            'recepteur' => $transfert->recepteur,
                            'solde' => $transfert->solde,
                            'id_historique' => $id_hist,
                            'created_at' => $transfert->created_at,
                            'updated_at' => $transfert->updated_at,
                        ]);
                        $historique->save();
                    } else {
                        throw new \Exception("L'opération avec l'ID $transfert_id n'existe pas.");
                    }
                }
                Transfert::whereIn('id', $transferts_action)->delete();
                return response()->json(['success' => true, 'message' => 'Transferts ajoutées avec succès à l\'historique et supprimées de la liste des transferts.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function transferts_show($id)
    {

        $items = Session::get('actions');
        if ((array_key_exists('historique', $items) && in_array("tout", $items["historique"])) || (array_key_exists('historique', $items) && in_array("detail", $items["historique"]))) {
            return redirect()->route("403");
        } else {
            $historique = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')
                ->where("historiques_operations.id", $id)
                ->first();

            $detail_historiques = DB::table('detail_historiques_transferts')
                ->where("id_historique", $id)
                ->get();

            $total = DB::table('detail_historiques_transferts')
                ->select(
                    DB::raw('SUM(CASE WHEN recepteur = "' . $historique->client . '" THEN solde ELSE 0 END) AS sommeTotalRecepteur'),
                    DB::raw('SUM(CASE WHEN expediteur = "' . $historique->client . '" THEN solde ELSE 0 END) AS sommeTotalExpediteur')
                )
                ->where('id_historique', $id)
                ->first();
            $notclients = Session::get('clients')->toArray();
            if (in_array($historique->client, $notclients))
                return redirect()->route("403");
            return view('detail_historique_transferts', compact('historique', 'detail_historiques', 'total'));
        }
    }

    public function transferts_deletedetail(Request $request)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Le mot de passe actuel est incorrect');
            }
            $detail_historique = DB::table('detail_historiques_transferts')->where("id", $request->id)->first();
            $idhist = $detail_historique->id_historique;
            $historique = Historiques_Operations::find($idhist);
            $historique->valeur =  $historique->valeur + $request->solde;
            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Supprimer',
                    'model' => 'DETAIL_HISTORIQUES_TRANSFERTS',
                    'page' => 'historiquestransferts/' . $idhist,
                    'details' => json_encode([
                        'id' => $request->id,
                        'historique_id' => $idhist,
                        'valeur' =>  $historique->valeur + $request->solde

                    ]),
                ]);
                return back()->with('success', 'Your action is pending approval from the admin.');
            } else {
                $deleted = DB::table('detail_historiques_transferts')
                    ->where('id', $request->id)
                    ->delete();
                $historique->save();
                if ($deleted)
                    return back()->with('success', 'Historique a été supprimée avec succès.');
                else
                    return back()->with('error', 'Erreur lors de la suppression de l\'historique.');
            }
        } catch (\Exception $e) {
            return back()->with('success', 'Historique a été supprimée avec succès.');
        }
    }

    public function transferts_restoredetail(Request $request)
    {
        try {
            $detail_historique = DB::table('detail_historiques_transferts')->where("id", $request->id)->first();
            $historique = Historiques_Operations::find($detail_historique->id_historique);
            $historique->valeur =  $historique->valeur + $request->solde;

            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    'model' => 'TRANSFERT',
                    'page' => 'historiquestransferts/' . $detail_historique->id_historique,
                    'details' => json_encode([
                        'id' => $request->id,
                        'from' => 'DETAIL_HISTORIQUES_TRANSFER',
                        'historique_id' => $detail_historique->id_historique,
                        'date' => $detail_historique->date,
                        'expediteur' => $detail_historique->expediteur,
                        'recepteur' => $detail_historique->recepteur,
                        'valeur' =>  $historique->valeur + $request->solde,
                        'solde' => $detail_historique->solde,
                        'devise' => $historique->devise,
                        'created_at' => $detail_historique->created_at,
                        'updated_at' => $detail_historique->updated_at,
                    ]),
                ]);
                return back()->with('success', 'Attendez la confirmation de l\'administrateur.');
            } else {
                DB::table('transferts')->insert([
                    'date' => $detail_historique->date,
                    'expediteur' => $detail_historique->expediteur,
                    'recepteur' => $detail_historique->recepteur,
                    'solde' => $detail_historique->solde,
                    'devise' => $historique->devise,
                    'created_at' => $detail_historique->created_at,
                    'updated_at' => $detail_historique->updated_at,
                ]);
                $deleted = DB::table('detail_historiques_transferts')->where('id', $request->id)->delete();
                $historique->save();
                if ($deleted)
                    return back()->with('success', 'Historique a été désarchivé avec succès.');
                else
                    return back()->with('error', 'Erreur lors de la désarchivage de l\'historique.');
            }
        } catch (\Exception $e) {
            return back()->with('success','Historique a été supprimée avec succès.');
        }
    }

    // CONVERTES

    public function convertes_add(Request $request)
    {
        $historique = new Historiques_Operations;
        $historique->id = $id_hist = date('YmdHis');
        $historique->datehistorique = $request->date;
        $historique->commentaire = $request->commentaire;
        $historique->valeur = $request->valeur;
        $historique->client = $request->client;
        $historique->devise = $request->devise;

        $transferts_action = $request->transferts_check;
        try {
            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    'model' => 'HISTORIQUES_OPERATIONS',
                    'page' => "converts/" . $request->client . "/" . $request->devise,
                    'details' => json_encode([
                        'id' => $id_hist,
                        'client' =>  $request->client,
                        'datehistorique' =>  $request->date,
                        'devise' => $request->devise,
                        'commentaire' => $request->commentaire,
                        'valeur' => $request->valeur,
                        'convertes_action' => $request->transferts_check,
                    ]),
                ]);
                return response()->json(['success' => true, 'message' => 'Attendez la confirmation de l\'administrateur.']);
            } else {
                foreach ($transferts_action as $transfert_id) {
                    $transfert = Converte::find($transfert_id);
                    if ($transfert) {
                        DB::table('detail_historiques_convertes')->insert([
                            'date' => $transfert->date,
                            'convertedSymbol' => $transfert->convertedSymbol,
                            'amount' => $transfert->amount,
                            "client_username" => $transfert->client_username,
                            'devise' => $transfert->devise,
                            'id_historique' => $id_hist,
                            "commentaire" => $transfert->commentaire,
                            'created_at' => $transfert->created_at,
                            'updated_at' => $transfert->updated_at,
                        ]);
                    } else {
                        throw new \Exception("L'opération avec l'ID $transfert_id n'existe pas. in convertes_add");
                    }
                }
                Converte::whereIn('id', $transferts_action)->delete();
                $historique->save();
                return response()->json(['success' => true, 'message' => 'Historique créé avec succès. Transferts ajoutées avec succès à l\'historique et supprimées de la liste des transferts.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function convertes_addexiste(Request $request)
    {
        $historique = Historiques_Operations::find($request->id_historique);
        $historique->valeur =  $historique->valeur + $request->valeur;
        $id_hist = $request->id_historique;
        $transferts_action = $request->transferts_check;
        try {
            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    'model' => 'DETAIL_HISTORIQUES_CONVRTES',
                    'page' =>  "converts/" . $historique->client . "/" . $historique->devise,
                    'details' => json_encode([
                        'convertes_action' => $transferts_action,
                        'historique_id' => $id_hist,
                        'valeur' =>  $historique->valeur
                    ]),
                ]);
                return response()->json(['success' => true, 'message' => 'Attendez la confirmation de l\'administrateur.']);
            } else {
                foreach ($transferts_action as $transfert_id) {
                    $transfert = Converte::find($transfert_id);
                    if ($transfert) {
                        DB::table('detail_historiques_convertes')->insert([
                            'date' => $transfert->date,
                            'convertedSymbol' => $transfert->convertedSymbol,
                            'amount' => $transfert->amount,
                            "client_username" => $transfert->client_username,
                            'devise' => $transfert->devise,
                            "commentaire" => $transfert->commentaire,
                            'id_historique' => $id_hist,
                            'created_at' => $transfert->created_at,
                            'updated_at' => $transfert->updated_at,
                        ]);
                    } else {
                        throw new \Exception("L'opération avec l'ID $transfert_id n'existe pas. in convertes_addexiste");
                    }
                }
                Converte::whereIn('id', $transferts_action)->delete();
                $historique->save();
                return response()->json(['success' => true, 'message' => 'Converte ajoutées avec succès à l\'historique et supprimées de la liste des transferts.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function convertes_show($id)
    {
        $items = Session::get('actions');
        if ((array_key_exists('historique', $items) && in_array("tout", $items["historique"])) || (array_key_exists('historique', $items) && in_array("detail", $items["historique"]))) {
            return redirect()->route("403");
        } else {
            // Retrieve the main historique record
            $historique = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')
                ->where("historiques_operations.id", $id)
                ->first();

            // Retrieve the detail historiques
            $detail_historiques = DB::table('detail_historiques_convertes')
                ->where("id_historique", $id)
                ->get();

            // Calculate the total received amount
            $totalReceived = DB::table('detail_historiques_convertes')
                ->where('id_historique', $id)
                ->where('convertedSymbol', $historique->devise)
                ->sum('amount');
            $totalConverted = DB::table('detail_historiques_convertes')
                ->where('id_historique', $id)
                ->where('devise', $historique->devise)
                ->sum('amount');

            $notclients = Session::get('clients')->toArray();
            if (in_array($historique->client, $notclients))
                return redirect()->route("403");
            return view('detail_historique_convertes', compact('historique', 'detail_historiques', 'totalReceived', 'totalConverted'));
        }
    }

    public function convertes_deletedetail(Request $request)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Le mot de passe actuel est incorrect');
            }
            $detail_historique = DB::table('detail_historiques_convertes')->where("id", $request->id)->first();
            $idhist = $detail_historique->id_historique;
            $historique = Historiques_Operations::find($idhist);
            $historique->valeur =  $historique->valeur + $request->solde;
            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Supprimer',
                    'model' => 'DETAIL_HISTORIQUES_CONVRTES',
                    'page' => 'historiquesconvertes/' . $idhist,
                    'details' => json_encode([
                        'id' => $request->id,
                        'historique_id' => $idhist,
                        'valeur' =>  $historique->valeur + $request->solde
                    ]),
                ]);
                return back()->with('success', 'Attendez la confirmation de l\'administrateur.');
            } else {
                $deleted = DB::table('detail_historiques_convertes')
                    ->where('id', $request->id)
                    ->delete();
                $historique->save();
                if ($deleted)
                    return back()->with('success', 'Historique a été supprimée avec succès.');
                else
                    return back()->with('error', 'Erreur lors de la suppression de l\'historique.');
            }
        } catch (\Exception $e) {
            return back()->with('success', 'Historique a été supprimée avec succès.');
        }
    }

    public function convertes_restoredetail(Request $request)
    {
        try {
            $detail_historique = DB::table('detail_historiques_convertes')->where("id", $request->id)->first();
            $historique = Historiques_Operations::find($detail_historique->id_historique);
            $historique->valeur =  $historique->valeur + $request->solde;
            if (auth()->user()->role == 'comptable') {
                // Store in PendingAction for admin approval
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    'model' => 'CONVERTE',
                    'page' => 'historiquesconvertes/' . $detail_historique->id_historique,
                    'details' => json_encode([
                        'id' =>  $request->id,
                        'from' => 'DETAIL_HISTORIQUES_CONVRTES',
                        'date' => $detail_historique->date,
                        'client_username' => $detail_historique->client_username,
                        'convertedSymbol' => $detail_historique->convertedSymbol,
                        'commentaire' => $detail_historique->commentaire,
                        'amount' => $detail_historique->amount,
                        'devise' => $detail_historique->devise,
                        'created_at' => $detail_historique->created_at,
                        'updated_at' => $detail_historique->updated_at,
                        'historique_id' => $detail_historique->id_historique,
                        'valeur' =>  $historique->valeur + $request->solde
                    ]),
                ]);
                return back()->with('success', 'Attendez la confirmation de l\'administrateur.');
            } else {
                DB::table('convertes')->insert([
                    'date' => $detail_historique->date,
                    'client_username' => $detail_historique->client_username,
                    'convertedSymbol' => $detail_historique->convertedSymbol,
                    'commentaire' => $detail_historique->commentaire,
                    'amount' => $detail_historique->amount,
                    'devise' => $detail_historique->devise,
                    'created_at' => $detail_historique->created_at,
                    'updated_at' => $detail_historique->updated_at,
                ]);
                $deleted = DB::table('detail_historiques_convertes')->where('id', $request->id)->delete();
                $historique->save();
                if ($deleted)
                    return back()->with('success', 'Historique a été désarchivé avec succès.');
                else
                    return back()->with('error', 'Erreur lors de la désarchivage de l\'historique.');
            }
        } catch (\Exception $e) {
            return back()->with('success', 'Historique a été supprimée avec succès.');
        };
    }

    // Deposers Historiques ************************************************************************************************************************************************************

    public function deposers_add(Request $request)
    {
        // Depose::where("client", $request->client)->where("devise", $request->devise)->delete();
        $historique = new Historiques_Operations;
        $historique->id = $id_hist = date('YmdHis');
        $historique->datehistorique = $request->date;
        $historique->commentaire = $request->commentaire;
        $historique->valeur = $request->valeur;
        $historique->client = $request->client;
        $historique->devise = $request->devise;
        $historique->save();
        $deposers_action = Depose::all()->where("client", $request->client)->where("devise", $request->devise);

        // $x = [];
        foreach ($deposers_action as $deposers) {
            DB::table('detail_historiques_deposers')->insert([
                'date_depose' => $deposers->date_depose,
                'type' => $deposers->type,
                'amount' => $deposers->amount,
                'commentaire' => $deposers->commentaire,
                'id_historique' => $id_hist,
                'created_at' => $deposers->created_at,
                'updated_at' => $deposers->updated_at,
            ]);
            // $x[] = $deposers->date_depose;
        }
        // return response()->json(['success' => true, 'message' => $x]);

        Depose::where("client", $request->client)->where("devise", $request->devise)->delete();
        return response()->json(['success' => true, 'message' => 'Historique créé avec succès. Transferts ajoutées avec succès à l\'historique et supprimées de la liste des transferts.']);
    }
    public function deposers_addexiste(Request $request)
    {

        $historique = Historiques_Operations::find($request->id_historique);
        $historique->valeur =  $historique->valeur - $request->valeur;
        $historique->save();
        $id_hist = $request->id_historique;
        $deposers_action = Depose::all()->where("client", $historique->client)->where("devise", $historique->devise);
        // return response()->json(['success' => true, 'message' => $deposers_action]);
        foreach ($deposers_action as $deposers) {
            DB::table('detail_historiques_deposers')->insert([
                'date_depose' => $deposers->date_depose,
                'type' => $deposers->type,
                'amount' => $deposers->amount,
                'commentaire' => $deposers->commentaire,
                'id_historique' => $id_hist,
                'created_at' => $deposers->created_at,
                'updated_at' => $deposers->updated_at,
            ]);
        }
        // return response()->json(['success' => true, 'message' => $deposers_action]);

        Depose::where("client", $historique->client)->where("devise", $historique->devise)->delete();
        return response()->json(['success' => true, 'message' => 'Historique créé avec succès. Transferts ajoutées avec succès à l\'historique et supprimées de la liste des transferts.']);
    }

    public function deposers_show($id)
    {
        $items = Session::get('actions');
        if ((array_key_exists('historique', $items) && in_array("tout", $items["historique"])) || (array_key_exists('historique', $items) && in_array("detail", $items["historique"]))) {
            return redirect()->route("403");
        } else {
            $historique = DB::table('historiques_operations')
                ->join('clients', 'historiques_operations.client', '=', 'clients.username')
                ->select('historiques_operations.*', 'clients.nom AS nom_client')
                ->where("historiques_operations.id", $id)
                ->first();

            $detail_historiques = DB::table('detail_historiques_deposers')
                ->where("id_historique", $id)
                ->get();

            $total = DB::table('detail_historiques_deposers')
                ->select(
                    DB::raw('SUM(CASE WHEN type = "DEPOSER" THEN amount ELSE 0 END) AS sommeTotalDeposer'),
                    DB::raw('SUM(CASE WHEN type = "RETRAIT" THEN amount ELSE 0 END) AS sommeTotalRetrait')
                )
                ->where('id_historique', $id)
                ->first();

            $notclients = Session::get('clients')->toArray();
            if (in_array($historique->client, $notclients))
                return redirect()->route("403");
            return view('detail_historique_deposers', compact('historique', 'detail_historiques', 'total'));
        }
    }

    public function deposers_deletedetail(Request $request)
    {
        try {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return back()->with('error', 'Le mot de passe actuel est incorrect');
            }
            $detail_historique = DB::table('detail_historiques_deposers')->where("id", $request->id)->first();
            $idhist = $detail_historique->id_historique;
            $historique = Historiques_Operations::find($idhist);
            $historique->valeur =  $historique->valeur + $request->solde;
            $historique->save();

            $total = DB::table('detail_historiques_deposers')
                ->select(
                    DB::raw('SUM(CASE WHEN type = "DEPOSER" THEN amount ELSE 0 END) AS sommeTotalDeposer'),
                    DB::raw('SUM(CASE WHEN type = "RETRAIT" THEN amount ELSE 0 END) AS sommeTotalRetrait')
                )
                ->where('id_historique', $idhist)
                ->first();

            $som = 0;
            $som = $detail_historique->type == "RETRAIT" ? $som - $detail_historique->amount : $som + $detail_historique->amount;
            if (($total->sommeTotalDeposer - $total->sommeTotalRetrait) - $som >= 0) {
                $deleted = DB::table('detail_historiques_deposers')
                    ->where('id', $request->id)
                    ->delete();
                if ($deleted)
                    return back()->with('success', 'Historique a été supprimée avec succès.');
                else
                    return back()->with('error', 'Erreur lors de la suppression de l\'historique.');
            } else
                return back()->with('error', 'Erreur de total de stock faut toujours positiv.');
        } catch (\Exception $e) {
            return back()->with('success', 'Historique a été supprimée avec succès.');
        }
    }

    public function deposers_restoredetail(Request $request)
    {
        // try {
        $historique = Historiques_Operations::find($request->id_hist);
        $historique->valeur =  $historique->valeur + $request->solde;
        $historique->save();

        $details_historiques = DB::table('detail_historiques_deposers')->where('id_historique', $historique->id)->get();
        // dd($details_historiques);
        foreach ($details_historiques as $deposer) {
            DB::table('deposes')->insert([
                'date_depose' => $deposer->date_depose,
                'type' => $deposer->type,
                'amount' => $deposer->amount,
                'commentaire' => $deposer->commentaire,
                'client' => $historique->client,
                'devise' => $historique->devise,
                'created_at' => $deposer->created_at,
                'updated_at' => $deposer->updated_at,
            ]);
        }

        $deleted = DB::table('detail_historiques_deposers')->where('id_historique', $request->id_hist)->delete();
        if ($deleted)
            return back()->with('success', 'Historique a été désarchivé avec succès.');
        else
            return back()->with('error', 'Erreur lors de la désarchivage de l\'historique.');
        // } catch (\Exception $e) {
        //     return back()->with('success', 'Historique a été supprimée avec succès.');
        // }
    }
}
