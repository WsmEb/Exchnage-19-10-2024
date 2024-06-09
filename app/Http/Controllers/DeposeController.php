<?php

namespace App\Http\Controllers;

use App\Exports\ExportDeposes;
use App\Models\Client;
use App\Models\Depose;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\Historiques_Operations;
use App\Models\PendingAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use TCPDF;

class DeposeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.is.connected');
    }
    function index($client, $devise)
    {
        $notclients = Session::get('clients')->toArray();
        if (in_array($client, $notclients))
            return redirect()->route("403");

        $items = Session::get('actions');
        if ((array_key_exists('deposer', $items) && in_array("tout", $items["deposer"])) || (array_key_exists('client', $items) && in_array("detail", $items["client"]))) {
            return redirect()->route("403");
        } else {
            $deposes = Depose::all()->where("client", $client)->where("devise", $devise);
            $deposesActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "DEPOSER")->get();
            $retraitActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "RETRAIT")->get();
            $devise = Devise::all()->where("symbol", $devise)->first();
            $entreprise = Entreprise::first();
            $devises = Devise::all();
            $historiques =  Historiques_Operations::where('client', $client)->where('devise', $devise->symbol)->get();
            $ConvertAmountToDeviseBase = Devise::all()->where("symbol", $devise->symbol)->first();

            $client = Client::all()->where("username", $client)->first();
            // $totalSoldAmount = [];
            // foreach ($deposes as $depose) {
            //     $totalSoldAmount[$depose->id] = $depose->amount;
            // }
            // $totalDepose = array_reduce($totalSoldAmount, function ($carry, $item) {
            //     return $carry + $item;
            // }, 0);
            $totalDeposesActionsLists = [];
            foreach ($deposesActions as $depose) {
                $totalDeposesActionsLists[$depose->id] = $depose->amount;
            }
            $totalDeposeAction = array_reduce($totalDeposesActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $totalRetraitActionsLists = [];
            foreach ($retraitActions as $depose) {
                $totalRetraitActionsLists[$depose->id] = $depose->amount;
            }
            $totalRetraitAction = array_reduce($totalRetraitActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            $Total = $totalDeposeAction - $totalRetraitAction;
            $DeviseBaseValue = Devise::where("symbol", $entreprise->base_devise)->first();
            // $totalDeviseBase = $devise->symbol != $entreprise->base_devise ? $totalDepose * $ConvertAmountToDeviseBase->base : $totalDepose;
            Session::put('deposes', [$deposes->toArray(), $totalDeposeAction, $totalRetraitAction, $Total, $entreprise->base_devise]);
            session::put('client', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);
            return view("depose", compact("client", "DeviseBaseValue", "devise", "Total", "deposes", "devises", "totalDeposeAction", "entreprise", "totalRetraitAction", "ConvertAmountToDeviseBase", "historiques"));
        }
    }

    public function add(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('deposer', $items) && in_array("ajouter", $items["deposer"]))) {
            return redirect()->route("403");
        } else {
            $deposesActions = Depose::where("client", $request->client)->where("devise", $request->devise)->where("type", "DEPOSER")->get();
            $retraitActions = Depose::where("client", $request->client)->where("devise", $request->devise)->where("type", "RETRAIT")->get();

            $totalDeposesActionsLists = [];
            foreach ($deposesActions as $depose) {
                $totalDeposesActionsLists[$depose->id] = $depose->amount;
            }
            $totalDeposeAction = array_reduce($totalDeposesActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $totalRetraitActionsLists = [];
            foreach ($retraitActions as $depose) {
                $totalRetraitActionsLists[$depose->id] = $depose->amount;
            }
            $totalRetraitAction = array_reduce($totalRetraitActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            $Total = $totalDeposeAction - $totalRetraitAction;
            if ($Total < $request->amount && $request->action == "RETRAIT") {
                return redirect()->route("deposes.index", ['client' => $request->client, 'devise' => $request->devise])->with('errorTotal', 'Le Montant est Superieur de Total ');
            } else {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Ajouter',
                        'model' => 'DEPOSE',
                        "page" => "deposes/".$request->client."/".$request->devise,
                        'details' => json_encode([
                            'date_depose' => $request->date_depose,
                            'client' => $request->client,
                            'amount' => $request->amount,
                            "devise" => $request->devise,
                            'commentaire' => $request->commentaire,
                            'type' => $request->action,
                        ]),
                    ]);
                    return redirect()->route('deposes.index', ['client' => $request->client, 'devise' => $request->devise])->with('success', 'Attends L`ACCEPTATION DE ADMIN');
                }else {
                    $depose = Depose::create([
                        'date_depose' => $request->date_depose,
                        'client' => $request->client,
                        'amount' => $request->amount,
                        "devise" => $request->devise,
                        'commentaire' => $request->commentaire,
                        'type' => $request->action,
                    ]);
                    return redirect()->route('deposes.index', ['client' => $request->client, 'devise' => $request->devise])->with('success', 'Convert a été créée avec succès.');
                }
            }
        }
    }

    public function update(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('deposer', $items) && in_array("modifier", $items["deposer"]))) {
            return redirect()->route("403");
        } else {
            $deposesActions = Depose::where("client", $request->client)->where("devise", $request->devise)->where("type", "DEPOSER")->get();
            $retraitActions = Depose::where("client", $request->client)->where("devise", $request->devise)->where("type", "RETRAIT")->get();

            $totalDeposesActionsLists = [];
            foreach ($deposesActions as $depose) {
                $totalDeposesActionsLists[$depose->id] = $depose->amount;
            }
            $totalDeposeAction = array_reduce($totalDeposesActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $totalRetraitActionsLists = [];
            foreach ($retraitActions as $depose) {
                $totalRetraitActionsLists[$depose->id] = $depose->amount;
            }
            $totalRetraitAction = array_reduce($totalRetraitActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $Depose = Depose::find($request->id);
            $Total = $totalDeposeAction - $totalRetraitAction;

            // if($request->amount - ($Total + $Depose->amount) < 0 &&  $request->action == "DEPOSER" ) {
            //     return redirect()->route("deposes.index", ['client' => $request->client, 'devise' => $request->devise])->with('errorTotal', 'ne peut pas modifier le montant inferieur');
            // }

            if ((($Total + $Depose->amount) - ($request->amount) < 0) && ($request->action == "RETRAIT") || (($Total - ($Depose->amount + $request->amount) < 0) && ($Depose->type == "DEPOSER" && $request->action == "RETRAIT"))) {
                return redirect()->route("deposes.index", ['client' => $request->client, 'devise' => $request->devise])->with('errorTotal', 'Le Montant est Superieur de Total ');
            } else {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Modifier',
                        'model' => 'DEPOSE',
                        "page" => "deposes/".$request->client."/".$request->devise,
                        'details' => json_encode([
                            'id' => $request->id,
                            'date_depose' => $request->date_depose,
                            'amount' => $request->amount,
                            'commentaire' => $request->commentaire,
                            'type' => $request->action,
                        ]),
                    ]);
                    return redirect()->route('deposes.index', ['client' => $request->client, 'devise' => $request->devise])->with('success', 'Attends L`ACCEPTATION DE ADMIN');
                }else {
                    $Depose = Depose::find($request->id);
                    $Depose->date_depose = $request->date_depose;
                    $Depose->amount = $request->amount;
                    $Depose->type = $request->action;
                    $Depose->commentaire = $request->commentaire;
                    $Depose->save();
                    return redirect()->route('deposes.index', ['client' => $request->client, 'devise' => $request->devise])->with('success', 'Convert a été sauvegardée avec succès.');
                
                }
            }
        }
    }

    public function delete(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('deposer', $items) && in_array("supprimer", $items["deposer"]))) {
            return redirect()->route("403");
        } else {
            $deposesAmount = $request->session()->get("deposes");
            $donnees = Session::has('deposes') ? Session::get('deposes') : [];
            // dd($donnees[3]);
            // dd($request->transfert);
            // dd($deposesAmount);
            $AmountSpesific = 0;
            $TypeSpesific = "";
            $filtredDepose = array_filter($deposesAmount[0], function ($item) use ($request, &$AmountSpesific, &$TypeSpesific) {
                if ($item['id'] == $request->transfert && $item['type'] == "DEPOSER") {
                    $AmountSpesific = $item['amount'];
                    $TypeSpesific = $item['type'];
                };
            });

            // dd($AmountSpesific);
            // // $AmountSpesific =  $filtredDepose && $filtredDepose[0] ? $filtredDepose[0]['amount'] : 0;
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->route('deposes.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('error', 'Le mot de passe actuel est incorrect');
            }
            $transfert = Depose::all()->where("id", $request->transfert)->first();
            if (($donnees[3] >= $AmountSpesific && $TypeSpesific == "DEPOSER") || $TypeSpesific == "") {
                if (auth()->user()->role == 'comptable') {
                    PendingAction::create([
                        'comptable' => auth()->id(),
                        'action' => 'Supprimer',
                        'model' => 'DEPOSE',
                        "page" => "deposes/".$request->client_principale."/".$request->devise,
                        'details' => json_encode([
                            'id' => $request->transfert,
                        ]),
                    ]);
                    return redirect()->route('deposes.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Attends L`ACCEPTATION DE ADMIN');
                }else {
                    $transfert->delete();
                    return redirect()->route('deposes.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Transfert a été supprimée avec succès.');
                
                }

            } else {
                return redirect()->route('deposes.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('deleteError', 'pas supprimer car le total est inferieur a le montant selectionner.');
            }
        }
    }

    public function excel()
    {
        $items = Session::get('actions');
        if ((array_key_exists('deposer', $items) && in_array("exporter", $items["deposer"]))) {
            return redirect()->route("403");
        } else {
            return Excel::download(new ExportDeposes, 'deposes.xlsx');
        }
    }

    public function pdf()
    {

        $items = Session::get('actions');
        if ((array_key_exists('deposer', $items) && in_array("exporter", $items["deposer"]))) {
            return redirect()->route("403");
        } else {
            $donnees = Session::has('deposes') ? Session::get('deposes') : [];
            $informations = Session::has('client') ? Session::get('client') : [];
            $headers = ['DATE DEPOSE', 'MONTANT', 'TYPE', "Commentaire"];

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

            // $xc = 120;
            // $yc = 25;
            // $r = 15;
            // $total = $donnees[2][1] + $donnees[1][1];

            // $pdf->setFillColor(255, 0, 0);
            // $pdf->PieSector($xc, $yc, $r, 0, 360 / $total * $donnees[2][1], 'FD', false, 0, 2);

            // $pdf->setFillColor(0, 200, 0);
            // $pdf->PieSector($xc, $yc, $r, 360 / $total * $donnees[2][1], 360, 'FD', false, 0, 2);


            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'u', 20);
            $pdf->Cell(70, 20, "", 0, 0, '', 1);
            $pdf->Cell(70, 20, "Deposes", 0, 0, '', 1);
            $pdf->SetFont('helvetica', 'b', 11);
            $pdf->setXY(10, 15);
            $pdf->Cell(70, 10, 'Mr. ' . $informations[0]["nom"], 0, 0, '', 1);
            $pdf->setXY(10, 25);
            $pdf->Cell(70, 10, 'Devise sélection : ' . $informations[1]["symbol"], 0, 0, '', 1);
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


            function CheckSold($sold)
            {
                return $sold < 0 ? $sold : "+" . $sold;
            }


            foreach ($donnees[0] as $key => $operation) {

                // dd($donnees[0][$key]);
                $x = "- ";
                $html .= '<tr bgcolor="rgb(255, 255, 255)">';

                $x = "+ ";
                $html .= '<tr />';

                $html .= '<td align="center">' . $donnees[0][$key]["date_depose"] . '</td>';
                $html .= '<td align="center">' . $donnees[0][$key]["amount"] . ' ' . $informations[1]["symbol"] . '</td>';
                $html .= '<td align="center">' . $donnees[0][$key]["type"] . '</td>';
                $html .= '<td align="center">' . $donnees[0][$key]["commentaire"] . '</td>';
                $html .= '</tr>';
            }



            $html .= '<tr  >
        <td colspan="2" align="center" ><h4>TOTAL DEPOSE : </h4></td>
        <td colspan="2"  >' . "<h4>" . CheckSold($donnees[1]) . ' ' . $informations[1]["symbol"] . "</h4>" . '</td>
        </tr>';
            $html .= '<tr  >
        <td colspan="2" align="center" ><h4>TOTAL RETRAIT : </h4></td>
        <td colspan="2"  >' . "<h4>" . CheckSold($donnees[2]) . ' ' . $informations[1]["symbol"] . "</h4>" . '</td>
        </tr>';
            $html .= '<tr  >
        <td colspan="2" align="center" ><h4>TOTAL: </h4></td>
        <td colspan="2"  >' . "<h4>" . CheckSold($donnees[3]) . ' ' . $informations[1]["symbol"] . "</h4>" . '</td>
        </tr>';
            $html .= '</tbody>';
            $html .= '</table>';

            $pdf->writeHTML($html, true, false, true, false, '');
            return $pdf->Output('mon_document.pdf', 'I');
        }
    }

    public function search($client, $devise, $col = NULL, $val = NULL)
    {
        $entreprise = Entreprise::first();
        $ConvertAmountToDeviseBase = Devise::all()->where("symbol", $devise)->first();
        $deposesActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "DEPOSER")->get();
        $retraitActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "RETRAIT")->get();
        if ($val == NULL) {
            $deposes = Depose::where('client', $client)->where('devise', $devise)->get();
            $totalDeposesActionsLists = [];
            foreach ($deposesActions as $depose) {
                $totalDeposesActionsLists[$depose->id] = $depose->amount;
            }
            $totalDeposeAction = array_reduce($totalDeposesActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $totalRetraitActionsLists = [];
            foreach ($retraitActions as $depose) {
                $totalRetraitActionsLists[$depose->id] = $depose->amount;
            }
            $totalRetraitAction = array_reduce($totalRetraitActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            $Total = $totalDeposeAction - $totalRetraitAction;
        } else {
            $deposes = Depose::where('client', $client)->where('devise', $devise)->where($col, 'REGEXP', $val)->get();
            $deposesActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "DEPOSER")->where($col, 'REGEXP', $val)->get();
            $retraitActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "RETRAIT")->where($col, 'REGEXP', $val)->get();
            // $totalSoldAmount = [];
            // foreach ($deposes as $depose) {
            //     $totalSoldAmount[$depose->id] = $depose->amount;
            // }
            // $totalDepose = array_reduce($totalSoldAmount, function ($carry, $item) {
            //     return $carry + $item;
            // }, 0);
            $totalDeposesActionsLists = [];
            foreach ($deposesActions as $depose) {
                $totalDeposesActionsLists[$depose->id] = $depose->amount;
            }
            $totalDeposeAction = array_reduce($totalDeposesActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $totalRetraitActionsLists = [];
            foreach ($retraitActions as $depose) {
                $totalRetraitActionsLists[$depose->id] = $depose->amount;
            }
            $totalRetraitAction = array_reduce($totalRetraitActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            $Total = $totalDeposeAction - $totalRetraitAction;
        }

        $client = Client::all()->where("username", $client)->first();
        $devise = Devise::all()->where("symbol", $devise)->first();
        // $totalDeviseBase = $devise->symbol != $entreprise->base_devise ? $totalDepose * $ConvertAmountToDeviseBase->base : $totalDepose;
        Session::put('deposes', [$deposes->toArray(), $totalDeposeAction, $totalRetraitAction, $Total, $entreprise->base_devise]);
        session::put('client', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);


        echo json_encode([$deposes, $totalDeposeAction . ' ' . $devise->symbol, $totalRetraitAction . ' ' . $devise->symbol, $Total . ' ' . $devise->symbol, $entreprise->base_devise]);
    }

    public function searchdate($client, $devise, $date1 = NULL, $date2 = NULL)
    {
        $entreprise = Entreprise::first();
        $ConvertAmountToDeviseBase = Devise::all()->where("symbol", $devise)->first();
        if ($date1 == NULL || $date2 == NULL) {
            $deposes = Depose::where('client', $client)->where('devise', $devise)->get();
            $deposesActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "DEPOSER")->get();
            $retraitActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "RETRAIT")->get();
            $totalDeposesActionsLists = [];
            foreach ($deposesActions as $depose) {
                $totalDeposesActionsLists[$depose->id] = $depose->amount;
            }
            $totalDeposeAction = array_reduce($totalDeposesActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $totalRetraitActionsLists = [];
            foreach ($retraitActions as $depose) {
                $totalRetraitActionsLists[$depose->id] = $depose->amount;
            }
            $totalRetraitAction = array_reduce($totalRetraitActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            $Total = $totalDeposeAction - $totalRetraitAction;
        } else {
            // $deposes = Depose::where('client', $client)->where('devise', $devise)->whereBetween('date_depose', [$date1, $date2])->get();
            // $totalSoldAmount = [];
            // foreach ($deposes as $depose) {
            //     $totalSoldAmount[$depose->id] = $depose->amount;
            // }
            // $totalDepose = array_reduce($totalSoldAmount, function ($carry, $item) {
            //     return $carry + $item;
            // }, 0);
            $deposes = Depose::where('client', $client)->where('devise', $devise)->whereBetween('date_depose', [$date1, $date2])->get();
            $deposesActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "DEPOSER")->whereBetween('date_depose', [$date1, $date2])->get();
            $retraitActions = Depose::where("client", $client)->where("devise", $devise)->where("type", "RETRAIT")->whereBetween('date_depose', [$date1, $date2])->get();
            $totalDeposesActionsLists = [];
            foreach ($deposesActions as $depose) {
                $totalDeposesActionsLists[$depose->id] = $depose->amount;
            }
            $totalDeposeAction = array_reduce($totalDeposesActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $totalRetraitActionsLists = [];
            foreach ($retraitActions as $depose) {
                $totalRetraitActionsLists[$depose->id] = $depose->amount;
            }
            $totalRetraitAction = array_reduce($totalRetraitActionsLists, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            $Total = $totalDeposeAction - $totalRetraitAction;
        }
        $client = Client::all()->where("username", $client)->first();
        $devise = Devise::all()->where("symbol", $devise)->first();
        Session::put('deposes', [$deposes->toArray(), $totalDeposeAction, $totalRetraitAction, $Total, $entreprise->base_devise]);
        session::put('client', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);
        // $totalDeviseBase = $devise->symbol != $entreprise->base_devise ? $totalDepose * $ConvertAmountToDeviseBase->base : $totalDepose;
        // Session::put('deposes', [$deposes->toArray(), $totalDepose . $devise->symbol, $totalDeviseBase . $entreprise->base_devise, $ConvertAmountToDeviseBase, $entreprise->base_devise]);
        // session::put('client', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);
        echo json_encode([$deposes, $totalDeposeAction . ' ' . $devise->symbol, $totalRetraitAction . ' ' . $devise->symbol, $Total . ' ' . $devise->symbol, $entreprise->base_devise]);
    }
}
