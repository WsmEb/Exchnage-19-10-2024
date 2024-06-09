<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Converte;
use App\Models\Depose;
use App\Models\DetailHistoriquesConverte;
use App\Models\DetailHistoriquesOperation;
use App\Models\DetailHistoriquesTransfert;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\Historiques_Operations;
use App\Models\Operation;
use App\Models\PendingAction;
use App\Models\Transfert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PendingActionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    public function index(Request $request)
    {
        $query = PendingAction::where('status', 'pending');

        if ($request->has('comptable') && $request->comptable) {
            $query->where('comptable', $request->comptable);
        }

        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('model') && $request->model) {
            $query->where('model', $request->model);
        }

        $pendingActions = $query->get();

        $comptables = PendingAction::select('comptable')->distinct()->pluck('comptable');
        $actions = PendingAction::select('action')->distinct()->pluck('action');
        $pages = PendingAction::select('model')->distinct()->pluck('model');

        return view('pending_actions', compact('pendingActions', 'comptables', 'actions', 'pages'));
    }



    public function approve($id)
    {
        $pendingAction = PendingAction::findOrFail($id);
        $details = json_decode($pendingAction->details, true);

        if ($pendingAction->model == 'HISTORIQUES_OPERATIONS' && $pendingAction->action == 'Ajouter') {
            // Create Historiques_Operations
            $historique = new Historiques_Operations;
            $historique->id = $details['id'];
            $historique->datehistorique = $details['datehistorique'];
            $historique->commentaire = $details['commentaire'];
            $historique->valeur = $details['valeur'];
            $historique->client = $details['client'];
            $historique->devise = $details['devise'];
            $historique->save();

            // Create DetailHistoriquesOperations
            $detail_detect = isset($details['operations_check']) ? $details['operations_check']  : (isset($details['transfert_action']) ? $details['transfert_action'] : (isset($details['convertes_action']) ? $details['convertes_action'] : null));
            foreach ($detail_detect as $operation_id) {
                $operation = Operation::find($operation_id);
                $transfert = Transfert::find($operation_id);
                $converte = Converte::find($operation_id);
                if ($operation && isset($details['operations_check'])) {
                    DetailHistoriquesOperation::create([
                        'comments' => $operation->comments,
                        'date' => $operation->date,
                        'percentage' => $operation->percentage,
                        'total' => $operation->total,
                        'quantity' => $operation->quantity,
                        'type_operation' => $operation->type_operation,
                        'ville' => $operation->ville,
                        'prix' => $operation->prix,
                        'id_historique' => $details['id'],
                        'created_at' => $operation->created_at,
                        'updated_at' => $operation->updated_at,
                    ]);
                    $operation->delete();
                } elseif ($transfert && isset($details['transferts_check'])) {
                    DB::table('detail_historiques_transferts')->insert([
                        'date' => $transfert->date,
                        'expediteur' => $transfert->expediteur,
                        'recepteur' => $transfert->recepteur,
                        'solde' => $transfert->solde,
                        'id_historique' => $details['id'],
                        'created_at' => $transfert->created_at,
                        'updated_at' => $transfert->updated_at,
                    ]);
                    $transfert->delete();
                } else if ($converte && isset($details['convertes_action'])) {
                    DB::table('detail_historiques_convertes')->insert([
                        'date' => $converte->date,
                        'convertedSymbol' => $converte->convertedSymbol,
                        'amount' => $converte->amount,
                        "client_username" => $converte->client_username,
                        'devise' => $converte->devise,
                        'id_historique' => $details['id'],
                        "commentaire" => $converte->commentaire,
                        'created_at' => $converte->created_at,
                        'updated_at' => $converte->updated_at,
                    ]);
                    $converte->delete();
                }
            }
        } else if ($pendingAction->model == 'DETAIL_HISTORIQUES_OPERATIONS' && $pendingAction->action == 'Ajouter') {
            $historique = Historiques_Operations::findOrFail($details['id']);
            $historique->update(['valeur' => $details['valeur']]);
            // Create DetailHistoriquesOperations
            foreach ($details['operations_check'] as $operation_id) {
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
                        'id_historique' => $details['id'],
                        'created_at' => $operation->created_at,
                        'updated_at' => $operation->updated_at,
                    ]);
                    $operation->delete();
                }
            }
        } else if ($pendingAction->model == 'OPERATION' && $pendingAction->action == 'Ajouter' && isset($details['from'])) {
            $historique = Historiques_Operations::findOrFail($details['id']);
            $historique->update(['valeur' => $details['valeur']]);
            if ($details['from'] == 'DETAIL_HISTORIQUE_OPERATION') {
                DB::table('operations')->insert([
                    'comments' => $details['comments'],
                    'date' => $details['date'],
                    'percentage' => $details['percentage'],
                    'total' => $details['total'],
                    'quantity' => $details['quantity'],
                    'type_operation' => $details['type_operation'],
                    'ville' => $details['ville'],
                    'prix' => $details['prix'],
                    'client' => $details['client'],
                    'devise' => $details['devise'],
                    'created_at' => $details['created_at'],
                    'updated_at' => $details['updated_at'],
                ]);
                DB::table('detail_historiques_operations')->where('id', $details['detail_historique_id'])->delete();
            }
        } else if ($pendingAction->model == 'DETAIL_HISTORIQUES_OPERATIONS' && $pendingAction->action == 'Supprimer' && isset($details['id'])) {
            $historique = Historiques_Operations::findOrFail($details['historique_id']);
            $historique->update(['valeur' => $details['valeur']]);
            DB::table('detail_historiques_operations')
                ->where('id', $details['id'])
                ->delete();
        } else if ($pendingAction->model == 'DETAIL_HISTORIQUES_TRANSFERTS' && $pendingAction->action == 'Ajouter' && isset($details['transfert_action'])) {
            $historique = Historiques_Operations::findOrFail($details['historique_id']);
            $historique->update(['valeur' => $details['valeur']]);
            foreach ($details['transfert_action'] as $transfert_id) {
                $transfert = Transfert::find($transfert_id);
                if ($transfert) {
                    DB::table('detail_historiques_transferts')->insert([
                        'date' => $transfert->date,
                        'expediteur' => $transfert->expediteur,
                        'recepteur' => $transfert->recepteur,
                        'solde' => $transfert->solde,
                        'id_historique' => $details['historique_id'],
                        'created_at' => $transfert->created_at,
                        'updated_at' => $transfert->updated_at,
                    ]);
                    $historique->save();
                } else {
                    throw new \Exception("L'opération avec l'ID $transfert_id n'existe pas.");
                }
            }
            Transfert::whereIn('id', $details['transfert_action'])->delete();
        } else if ($pendingAction->model == 'DETAIL_HISTORIQUES_TRANSFERTS' && $pendingAction->action == 'Supprimer') {
            $historique = Historiques_Operations::findOrFail($details['historique_id']);
            $historique->update(['valeur' => $details['valeur']]);
            DB::table('detail_historiques_transferts')
                ->where('id', $details['id'])
                ->delete();
        } else if ($pendingAction->model == 'TRANSFERT' && $pendingAction->action == 'Ajouter'  && isset($details['from'])) {
            $historique = Historiques_Operations::findOrFail($details['historique_id']);
            $historique->update(['valeur' => $details['valeur']]);
            DB::table('transferts')->insert([
                'date' => $details['date'],
                'expediteur' => $details['expediteur'],
                'recepteur' => $details['recepteur'],
                'solde' => $details['solde'],
                'devise' => $details['devise'],
                'created_at' => $details['created_at'],
                'updated_at' => $details['updated_at'],
            ]);
            DB::table('detail_historiques_transferts')->where('id', $details['id'])->delete();
        } else if ($pendingAction->model == 'DETAIL_HISTORIQUES_CONVRTES' && $pendingAction->action == 'Supprimer') {
            $historique = Historiques_Operations::findOrFail($details['historique_id']);
            $historique->update(['valeur' => $details['valeur']]);
            DB::table('detail_historiques_convertes')
                ->where('id', $details['id'])
                ->delete();
        } else if ($pendingAction->model == 'CONVERTE' && $pendingAction->action == 'Ajouter'  && isset($details['from'])) {
            $historique = Historiques_Operations::findOrFail($details['historique_id']);
            $historique->update(['valeur' => $details['valeur']]);
            DB::table('convertes')->insert([
                'date' => $details['date'],
                'client_username' => $details['client_username'],
                'convertedSymbol' => $details['convertedSymbol'],
                'commentaire' => $details['commentaire'],
                'amount' => $details['amount'],
                'devise' => $details['devise'],
                'created_at' => $details['created_at'],
                'updated_at' => $details['updated_at'],
            ]);
            DB::table('detail_historiques_convertes')->where('id', $details['id'])->delete();
        } else if ($pendingAction->model == 'DETAIL_HISTORIQUES_CONVRTES' && $pendingAction->action == 'Ajouter') {
            $historique = Historiques_Operations::findOrFail($details['historique_id']);
            $historique->update(['valeur' => $details['valeur']]);
            // Create DetailHistoriquesOperations
            foreach ($details['convertes_action'] as $operation_id) {
                $convertes = Converte::find($operation_id);
                if ($convertes) {
                    DetailHistoriquesConverte::create([
                        'date' => $convertes->date,
                        'convertedSymbol' => $convertes->convertedSymbol,
                        'amount' => $convertes->amount,
                        "client_username" => $convertes->client_username,
                        'devise' => $convertes->devise,
                        "commentaire" => $convertes->commentaire,
                        'id_historique' => $details['historique_id'],
                        'created_at' => $convertes->created_at,
                        'updated_at' => $convertes->updated_at,
                    ]);
                    $convertes->delete();
                }
            }
        } else {
            // Handle other pending actions
            $this->handleAction($pendingAction, $details);
        }

        $pendingAction->status = 'approved';
        $pendingAction->delete();

        return redirect()->back()->with('status', 'Action accepter avec success!');
    }


    public function reject($id)
    {
        $pendingAction = PendingAction::findOrFail($id);
        $pendingAction->status = 'rejected';
        $pendingAction->delete();

        return redirect()->back()->with('statusEror', 'Action est Refusee!');
    }

    protected function handleAction($pendingAction, $details)
    {
        $modelClass = $this->getModelClass($pendingAction->model);
        $action = $pendingAction->action;

        if (class_exists($modelClass)) {
            switch ($action) {
                case 'Ajouter':
                    $this->createRecord($modelClass, $details);
                    break;
                case 'Modifier':
                    $this->updateRecord($modelClass, $details);
                    break;
                case 'Supprimer':
                    $this->deleteRecord($modelClass, $details);
                    break;
                default:
                    throw new \Exception("Unsupported action: $action");
            }
        } else {
            throw new \Exception("Model class $modelClass does not exist");
        }
    }

    protected function getModelClass($page)
    {
        $models = [
            'Devise' => Devise::class,
            'CONVERTE' => Converte::class,
            'CLIENTS' => Client::class,
            'DEPOSE' => Depose::class,
            'OPERATION' => Operation::class,
            'TRANSFERT' => Transfert::class,
            "ENTREPRISE" => Entreprise::class,
            "HISTORIQUES_OPERATIONS" => Historiques_Operations::class,
            "DETAIL_HISTORIQUES_OPERATIONS" => DetailHistoriquesOperation::class,
            "DETAIL_HISTORIQUES_TRANSFERTS" => DetailHistoriquesTransfert::class,
            "DETAIL_HISTORIQUES_CONVRTES" => DetailHistoriquesConverte::class
        ];

        return $models[$page] ?? null;
    }
    protected function createRecord($modelClass, $details)
    {
        $fillableAttributes = (new $modelClass)->getFillable();
        $data = array_intersect_key($details, array_flip($fillableAttributes));
        return $modelClass::create($data);
    }

    protected function updateRecord($modelClass, $details)
    {

        if ($modelClass == Entreprise::class && isset($details['id'])) {
            $record = $modelClass::first();
            $record->base_devise = $details['base_devise'];
        } else {
            $record = $modelClass::findOrFail($details['id']);
        }
        $fillableAttributes = $record->getFillable();
        $data = array_intersect_key($details, array_flip($fillableAttributes));
        $record->update($data);
    }

    protected function deleteRecord($modelClass, $details)
    {
        if ($modelClass == Client::class && isset($details['username'])) {
            $record = $modelClass::where('username', $details['username'])->firstOrFail();
        } elseif ($modelClass == Devise::class && isset($details['id'])) {
            $record = $modelClass::where('symbol', $details['id'])->firstOrFail();
        } else {
            $record = $modelClass::findOrFail($details['id']);
        }
        $record->delete();
    }

    public function hover($id)
    {
        $pendingAction = PendingAction::findOrFail($id);

        $details = json_decode($pendingAction->details, true);
        if (key_exists("operations_check", $details))
            $ids =  $details['operations_check'];

        elseif (key_exists("transfert_action", $details))
            $ids =  $details['transfert_action'];
        elseif (key_exists("convertes_action", $details))
            $ids =  $details['convertes_action'];
        elseif (key_exists("hover", $details))
            $ids =  [$details["hover"]];
        else
            $ids = [reset($details)];
        Session::put('hover', $ids);
        // dd($ids);
        return redirect('/' . $pendingAction->page);
    }
}
