<?php

namespace App\Http\Controllers;


use App\Exports\ExportConverts;
use App\Models\Client;
use App\Models\Converte;
use App\Models\Devise;
use App\Models\Entreprise;
use App\Models\Historiques_Operations;
use App\Models\Operation;
use App\Models\PendingAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use TCPDF;

class ConvertsController extends Controller
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
        if ((array_key_exists('convertir', $items) && in_array("tout", $items["convertir"])) || (array_key_exists('client', $items) && in_array("detail", $items["client"]))) {
            return redirect()->route("403");
        } else {
            $converts = Converte::where("client_username", $client)->where(function ($query) use ($devise) {
                $query->where('convertedSymbol', $devise)->orWhere('devise', $devise);
            })->get();
            $getConvertedAmountTotal = Converte::all()->where("client_username", $client)->Where("devise", $devise);
            $getRecevedAmountTotal = Converte::all()->where("client_username", $client)->where("convertedSymbol", $devise);
            $ConvertedAmountQueue = [];
            $RecevedAmountQueue = [];
            foreach ($getRecevedAmountTotal as $convert) {
                $RecevedAmountQueue[$convert->id] = $convert->amount * ConvertedSymbolBase($convert->devise) / ConvertedSymbolBase($convert->convertedSymbol);
            }
            $totalRecevedAmount = array_reduce($RecevedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            foreach ($getConvertedAmountTotal as $convert) {
                $ConvertedAmountQueue[$convert->id] = $convert->amount;
            }
            $totalConvertedAmount = array_reduce($ConvertedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            // dd($getConvertedAmountTotal);
            $operations = Operation::all()->where("client", $client)->where("devise", $devise);
            $entreprise = Entreprise::first();
            $totalSoldAmount = [];
            foreach ($operations as $operation) {
                $totalSoldAmount[$operation->id] = $operation->total;
            }
            $totalSold = array_reduce($totalSoldAmount, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $devise = Devise::all()->where("symbol", $devise)->first();
            $client = Client::all()->where("username", $client)->first();
            $devises = Devise::all();
            $ConvertedAmounts = [];
            foreach ($converts as $convert) {
                $ConvertedAmounts[$convert->id] = $convert->amount;
            }
            $totalConverted = array_reduce($ConvertedAmounts, function ($carry, $item) {
                return $carry + $item;
            }, 0);
            $DeviseBaseValue = Devise::where("symbol", $entreprise->base_devise)->first();
            $historiques = Historiques_Operations::where('client', $client->username)->where('devise', $devise->symbol)->get();
            Session::put('converts', [$converts->toArray(), $totalConvertedAmount, $totalRecevedAmount - $totalConvertedAmount]);
            session::put('client', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);
            return view("converts", compact("converts", "DeviseBaseValue", "historiques", "totalConvertedAmount", "totalRecevedAmount", "devise", "client", "devises", "totalConverted", "totalSold", "entreprise"));
        }
    }
    public function add(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('convertir', $items) && in_array("ajouter", $items["convertir"]))) {
            return redirect()->route("403");
        } else {
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Ajouter',
                    "page" => "converts/".$request->client_username."/".$request->devise,
                    "model" => "CONVERTE",
                    'details' => json_encode([
                        'date' => $request->date,
                        'client_username' => $request->client_username,
                        'convertedSymbol' => $request->convertedSymbol,
                        'amount' => $request->amount,
                        "devise" => $request->devise,
                        'commentaire' => $request->commentaire,
                    ]),
                ]);
                return redirect()->route('converts.index', ['client' => $request->client_username, 'devise' => $request->devise])->with('success', 'Attends L`ACCEPTATION DE ADMIN');
            
            }else {
                $Convert = Converte::create([
                    'date' => $request->date,
                    'client_username' => $request->client_username,
                    'convertedSymbol' => $request->convertedSymbol,
                    'amount' => $request->amount,
                    "devise" => $request->devise,
                    'commentaire' => $request->commentaire,
                ]);
                return redirect()->route('converts.index', ['client' => $request->client_username, 'devise' => $request->devise])->with('success', 'Convert a été créée avec succès.');
            
            }

        }
    }

    public function update(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('convertir', $items) && in_array("modifier", $items["convertir"]))) {
            return redirect()->route("403");
        } else {

            $Converte = Converte::find($request->id);
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Modifier',
                    "page" => "converts/".$request->client_username."/".$request->devise,
                    "model" => "CONVERTE",
                    'details' => json_encode([
                        'id' => $request->id,
                        'date' => $request->date,
                        'client_username' => $request->client_username,
                        'convertedSymbol' => $request->convertedSymbol,
                        'amount' => $request->amount,
                        "devise" => $request->devise,
                        'commentaire' => $request->commentaire,
                    ]),
                ]);
                return redirect()->route('converts.index', ['client' => $request->client_username, 'devise' => $request->devise])->with('success', 'Attends L`ACCEPTATION DE ADMIN');
            }else {
                $Converte->date = $request->date;
                $Converte->convertedSymbol = $request->convertedSymbol;
                $Converte->client_username = $request->client_username;
                $Converte->amount = $request->amount;
                $Converte->commentaire = $request->commentaire;
                $Converte->save();
                return redirect()->route('converts.index', ['client' => $request->client_username, 'devise' => $request->devise])->with('success', 'Convert a été sauvegardée avec succès.');
            
            }

        }
    }

    public function delete(Request $request)
    {
        $items = Session::get('actions');
        if ((array_key_exists('convertir', $items) && in_array("supprimer", $items["convertir"]))) {
            return redirect()->route("403");
        } else {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                return redirect()->route('converts.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('error', 'Le mot de passe actuel est incorrect');
            }
            if (auth()->user()->role == 'comptable') {
                PendingAction::create([
                    'comptable' => auth()->id(),
                    'action' => 'Supprimer',
                    "page" => "converts/".$request->client_principale."/".$request->devise,
                    "model" => "CONVERTE",
                    'details' => json_encode([
                        'id' => $request->transfert,
                    ]),
                ]);
                return redirect()->route('converts.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Attends L`ACCEPTATION DE ADMIN');
            }else {
                $transfert = Converte::all()->where("id", $request->transfert)->first();
                $transfert->delete();
                return redirect()->route('converts.index', ['client' => $request->client_principale, 'devise' => $request->devise])->with('success', 'Transfert a été supprimée avec succès.');
            
            }

        }
    }
    public function pdf()
    {
        $items = Session::get('actions');
        if ((array_key_exists('convertir', $items) && in_array("exporter", $items["convertir"]))) {
            return redirect()->route("403");
        } else {
            $donnees = Session::has('converts') ? Session::get('converts') : [];
            $informations = Session::has('client') ? Session::get('client') : [];
            $headers = ['Date', "DEVISE D'ORIGINE", "DEVISE DESTINATION", "MONTANT", "MONTANT CONVERTI", 'Commentaire'];

            $html = '<style>th,td{border: 1px solid black;text-align:center;font-size:9px;}th{font-size:17px;background-color:gold;}</style>';


            // Tcpdf ==========================================================================================================
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetTitle('Convertes');
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
            $pdf->Cell(70, 20, "Convertes", 0, 0, '', 1);
            $pdf->SetFont('helvetica', 'b', 11);
            $pdf->setXY(10, 25);
            $pdf->Cell(70, 10, 'Mr. ' . $informations[0]["nom"], 0, 0, '', 1);
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


            foreach ($donnees[0] as $key => $operation) {

                // dd($donnees[0][$key]);
                $x = "- ";
                $html .= '<tr bgcolor="rgb(255, 255, 255)">';

                $x = "+ ";
                $html .= '<tr />';

                $html .= '<td align="center">' . $donnees[0][$key]["date"] . '</td>';
                $html .= '<td align="center">' . $donnees[0][$key]["devise"] . '</td>';
                $html .= '<td align="center">' . $donnees[0][$key]["convertedSymbol"] . '</td>';
                $html .= '<td align="center">' . number_format($donnees[0][$key]["amount"], 2) . ' ' . $donnees[0][$key]["devise"] . '</td>';
                $html .= '<td align="center">' . number_format($donnees[0][$key]["amount"] * ConvertedSymbolBase($donnees[0][$key]["devise"]) / ConvertedSymbolBase($donnees[0][$key]["convertedSymbol"]), 2) . ' ' . $donnees[0][$key]["convertedSymbol"] . '</td>';
                // $html .= '<td align="center">' . $donnees[0][$key]["convertedSymbol"] . '</td>';
                $html .= '<td align="center">' . $donnees[0][$key]["commentaire"] . '</td>';
                $html .= '</tr>';
            }

            function CheckSold($sold)
            {
                return $sold < 0 ? $sold : "+" . $sold;
            }

            $html .= '<tr  >
        <td colspan="3" align="center" ><h4>Total RECU: </h4></td>
        <td colspan="3"  >' . "<h4>" . number_format($donnees[2] + $donnees[1], 2) . ' ' . $informations[1]["symbol"] . "</h4>" . '</td>
        </tr>';

            $html .= '<tr  >
        <td colspan="3" align="center" ><h4>TOTAL CONVERTED : </h4></td>
        <td colspan="3"  >' . "<h4>" .  number_format($donnees[1], 2) . ' ' . $informations[1]["symbol"] . "</h4>" . '</td>
        </tr>';
            $html .= '<tr  >
        <td colspan="3" align="center" ><h4>TOTAL : </h4></td>
        <td colspan="3"  >' . "<h4>" .  number_format($donnees[2], 2) . ' ' . $informations[1]["symbol"] . "</h4>" . '</td>
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
        if ((array_key_exists('convertir', $items) && in_array("exporter", $items["convertir"]))) {
            return redirect()->route("403");
        } else {
            return Excel::download(new ExportConverts, 'converts.xlsx');
        }
    }
    public function search($client, $devise, $col = NULL, $val = NULL)
    {
        $operations = Operation::all()->where("client", $client)->where("devise", $devise);
        $entreprise = Entreprise::first();
        if ($val == NULL) {
            $converts = Converte::where("client_username", $client)->where(function ($query) use ($devise) {
                $query->where('convertedSymbol', $devise)->orWhere('devise', $devise);
            })->get()->map(function ($convert) {
                $convert->converted_solde = number_format($this->JsConvertedSymbol($convert->devise, $convert->convertedSymbol, $convert->amount), 2);
                return $convert;
            });
            $getConvertedAmountTotal = Converte::all()->where("client_username", $client)->Where("devise", $devise);
            $getRecevedAmountTotal = Converte::all()->where("client_username", $client)->where("convertedSymbol", $devise);
            $ConvertedAmountQueue = [];
            $RecevedAmountQueue = [];
            foreach ($getRecevedAmountTotal as $convert) {
                $RecevedAmountQueue[$convert->id] = $convert->amount * ConvertedSymbolBase($convert->devise) / ConvertedSymbolBase($convert->convertedSymbol);
            }
            $totalRecevedAmount = array_reduce($RecevedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            foreach ($getConvertedAmountTotal as $convert) {
                $ConvertedAmountQueue[$convert->id] = $convert->amount;
            }
            $totalConvertedAmount = array_reduce($ConvertedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);
        } else {
            if ($col == "commentaire") {
                $converts = Converte::where("client_username", $client)->where(function ($query) use ($devise) {
                    $query->where('convertedSymbol', $devise)->orWhere('devise', $devise);
                })->where($col, 'REGEXP', $val)->get()->map(function ($convert) {
                    $convert->converted_solde = number_format($this->JsConvertedSymbol($convert->devise, $convert->convertedSymbol, $convert->amount), 2);
                    return $convert;
                });
                $getConvertedAmountTotal = Converte::where("client_username", $client)->Where("devise", $devise)->where($col, 'REGEXP', $val)->get();
                $getRecevedAmountTotal = Converte::where("client_username", $client)->where("convertedSymbol", $devise)->where($col, 'REGEXP', $val)->get();
            } elseif ($devise == $val) {
                $converts = Converte::where("client_username", $client)->where($col, $val)->get()->map(function ($convert) {
                    $convert->converted_solde = number_format($this->JsConvertedSymbol($convert->devise, $convert->convertedSymbol, $convert->amount), 2);
                    return $convert;
                });
                if ($col == "devise") {
                    $getConvertedAmountTotal = Converte::where("client_username", $client)->Where("devise", $devise)->where($col, 'REGEXP', $val)->get();
                    $getRecevedAmountTotal = [];
                } else {
                    $getConvertedAmountTotal = [];
                    $getRecevedAmountTotal = Converte::where("client_username", $client)->where("convertedSymbol", $devise)->where($col, 'REGEXP', $val)->get();
                }
            } else {
                $converts = Converte::where("client_username", $client)->where(function ($query) use ($devise) {
                    $query->where('convertedSymbol', $devise)->orWhere('devise', $devise);
                })->where($col, 'REGEXP', $val)->get()->map(function ($convert) {
                    $convert->converted_solde = number_format($this->JsConvertedSymbol($convert->devise, $convert->convertedSymbol, $convert->amount), 2);
                    return $convert;
                });
                $getConvertedAmountTotal = Converte::where("client_username", $client)->Where("devise", $devise)->where($col, 'REGEXP', $val)->get();
                $getRecevedAmountTotal = Converte::where("client_username", $client)->where("convertedSymbol", $devise)->where($col, 'REGEXP', $val)->get();
            }

            $ConvertedAmountQueue = [];
            $RecevedAmountQueue = [];
            foreach ($getRecevedAmountTotal as $convert) {
                $RecevedAmountQueue[$convert->id] = $convert->amount * ConvertedSymbolBase($convert->devise) / ConvertedSymbolBase($convert->convertedSymbol);
            }
            $totalRecevedAmount = array_reduce($RecevedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            foreach ($getConvertedAmountTotal as $convert) {
                $ConvertedAmountQueue[$convert->id] = $convert->amount;
            }
            $totalConvertedAmount = array_reduce($ConvertedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);
        }
        $devise = Devise::all()->where("symbol", $devise)->first();
        $client = Client::all()->where("username", $client)->first();
        session::put('client', [$client->toArray(), $devise->toArray(), $entreprise->base_devise]);
        Session::put('converts', [$converts->toArray(), $totalConvertedAmount, $totalRecevedAmount - $totalConvertedAmount]);
        echo json_encode([$converts, $totalConvertedAmount, $totalRecevedAmount, $totalRecevedAmount - $totalConvertedAmount]);
    }
    public function searchdate($client, $devise, $date1 = NULL, $date2 = NULL)
    {
        $operations = Operation::all()->where("client", $client)->where("devise", $devise);
        if ($date1 == NULL || $date2 == NULL) {
            $converts = Converte::where("client_username", $client)->where(function ($query) use ($devise) {
                $query->where('convertedSymbol', $devise)->orWhere('devise', $devise);
            })->get()->map(function ($convert) {
                $convert->converted_solde = number_format($this->JsConvertedSymbol($convert->devise, $convert->convertedSymbol, $convert->amount), 2);
                return $convert;
            });
            $getConvertedAmountTotal = Converte::all()->where("client_username", $client)->Where("devise", $devise);
            $getRecevedAmountTotal = Converte::all()->where("client_username", $client)->where("convertedSymbol", $devise);
            $ConvertedAmountQueue = [];
            $RecevedAmountQueue = [];
            foreach ($getRecevedAmountTotal as $convert) {
                $RecevedAmountQueue[$convert->id] = $convert->amount * ConvertedSymbolBase($convert->devise) / ConvertedSymbolBase($convert->convertedSymbol);
            }
            $totalRecevedAmount = array_reduce($RecevedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            foreach ($getConvertedAmountTotal as $convert) {
                $ConvertedAmountQueue[$convert->id] = $convert->amount;
            }
            $totalConvertedAmount = array_reduce($ConvertedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);
        } else {
            $converts = Converte::where("client_username", $client)->where(function ($query) use ($devise) {
                $query->where('convertedSymbol', $devise)->orWhere('devise', $devise);
            })->whereBetween('date', [$date1, $date2])->get()->map(function ($convert) {
                $convert->converted_solde = number_format($this->JsConvertedSymbol($convert->devise, $convert->convertedSymbol, $convert->amount), 2);
                return $convert;
            });
            $getConvertedAmountTotal = Converte::where("client_username", $client)->Where("devise", $devise)->whereBetween('date', [$date1, $date2])->get();
            $getRecevedAmountTotal = Converte::where("client_username", $client)->where("convertedSymbol", $devise)->whereBetween('date', [$date1, $date2])->get();
            $ConvertedAmountQueue = [];
            $RecevedAmountQueue = [];
            foreach ($getRecevedAmountTotal as $convert) {
                $RecevedAmountQueue[$convert->id] = $convert->amount * ConvertedSymbolBase($convert->devise) / ConvertedSymbolBase($convert->convertedSymbol);
            }
            $totalRecevedAmount = array_reduce($RecevedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);

            foreach ($getConvertedAmountTotal as $convert) {
                $ConvertedAmountQueue[$convert->id] = $convert->amount;
            }
            $totalConvertedAmount = array_reduce($ConvertedAmountQueue, function ($carry, $item) {
                return $carry + $item;
            }, 0);
        }
        Session::put('converts', [$converts->toArray(), $totalConvertedAmount, $totalRecevedAmount - $totalConvertedAmount]);
        echo json_encode([$converts, $totalConvertedAmount, $totalRecevedAmount, $totalRecevedAmount - $totalConvertedAmount]);
    }
    public function JsConvertedSymbol($deviseOrigin, $convertedDevise, $solde)
    {
        $devise1 = Devise::where("symbol", $deviseOrigin)->first();
        $devise2 = Devise::where("symbol", $convertedDevise)->first();
        return $solde * $devise1->base / $devise2->base;
    }
    public function JsConvertedSymbols($deviseOrigin, $convertedDevise)
    {
        $devise1 = Devise::where("symbol", $deviseOrigin)->first();
        $devise2 = Devise::where("symbol", $convertedDevise)->first();
        echo json_encode([$devise1->base, $devise2->base]);
    }
    public function findByDevise($devise = null)
    {
        $convertes = Converte::where("devise", $devise)->get();
        echo json_encode([$convertes]);
    }
}
