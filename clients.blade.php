<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <script src="{{ asset('/js/client.js') }}"></script>
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .loading {
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 100px;
            height: 100px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #solde {
            display: flex;
            gap: 10px;
        }

        #plus,
        #mois,
        #egal {
            display: none;
        }

        #label-plus,
        #label-mois,
        #label-egale {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        #plus:checked+#label-plus {
            background-color: green;
            color: white;
        }

        #mois:checked+#label-mois {
            background-color: red;
            color: white;
        }

        #egal:checked+#label-egale {
            background-color: green;
            color: white;
        }

        #label-plus:hover,
        #label-mois:hover,
        #label-egal:hover {
            background-color: lightgray;
        }
    </style>

    <script>
        function afficher(Clients, entreprise) {
            document.getElementById("resultats").innerHTML = "";
            if (Clients.length != 0) {
                for (const i in Clients) {
                    let bg = parseFloat(Clients[i].solde) >= 0 ? "bg-success" : "bg-danger";
                    ligne = "<tr >";
                    ligne += `<td class='${bg}' >${
                Clients[i].username !== null ? Clients[i].username : "-"
            }</td>`;
                    ligne += `<td class='${bg}' >${
                Clients[i].nom !== null ? Clients[i].nom : "-"
            }</td>`;
                    ligne += `<td class='${bg}' >${
                Clients[i].localisation !== null
                    ? Clients[i].localisation
                    : "-"
            }</td>`;
                    ligne += `<td class='${bg}' >${
                Clients[i].commentaire !== null
                    ? Clients[i].commentaire
                    : "-"
            }</td>`;
                    ligne += `<td class='${bg}' >${Clients[i].solde} ${
                entreprise.base_devise
            }</td>`;
                    if (Clients[i].bloque == "non")
                        ligne += `<td class='${bg}' ><a href="#verrouillerClient" data-bs-toggle="modal" class="text-dark" onclick="info_bloque('username_verrouiller','${Clients[i].username}','bloquer')"><i class="bi bi-lock"></i> bloquer</a></td>`;
                    else
                        ligne += `<td class='${bg}' ><a href="#verrouillerClient" data-bs-toggle="modal" class="text-dark" onclick="info_bloque('username_verrouiller','${Clients[i].username}','débloquer')"><i class="bi bi-unlock"></i> débloquer</a></td>`;

                    ligne += `<td class='${bg}' >
                    <a class="btn btn-warning border" href="#TypeDeviseModal" data-bs-toggle="modal" onclick="info('username_devise','${Clients[i].username}')"><i class="bi bi-eye"></i></a>
                    <a class="btn btn-info border" href="#passwordClient" data-bs-toggle="modal" onclick="info('username_password','${Clients[i].username}')"><i class="bi bi-key"></i></a>
                    <a class="btn btn-danger border" href="#deleteClient" data-bs-toggle="modal" onclick="info('username_delete','${Clients[i].username}')"><i class="bi bi-trash"></i></a>
                    <a class="btn btn-warning border" href="#updateClient" data-bs-toggle="modal" onclick="info_update('${Clients[i].username}','${Clients[i].nom}','${Clients[i].localisation}','${Clients[i].commentaire}')"><i class="bi bi-pencil"></i></a>
                </td>`;
                    ligne += "</tr>";
                    document.getElementById("resultats").innerHTML += ligne;
                }
            } else
                document.getElementById(
                    "resultats"
                ).innerHTML = `<tr><td colspan="7" class="alert alert-danger text-center fw-bold p-3 text-light">N'est aucun client</td></tr>`;

        }

        function rechercherSolde() {
            let val = "";
            if (document.getElementById("egal").checked)
                val = "egal";
            else if (document.getElementById("plus").checked)
                val = "plus";
            else if (document.getElementById("mois").checked)
                val = "mois";
            var x = new XMLHttpRequest();
            x.open("GET", `/clients/searchsolde/${val}`, true);
            x.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    let Clients = response.clients;
                    let entreprise = response.entreprise;
                    afficher(Clients, entreprise);
                }
            };
            x.send();
        }



        function rechercher(input) {
            let val = input.value;
            var x = new XMLHttpRequest();
            if (val === "") x.open("GET", `/clients/search/`, true);
            else x.open("GET", `/clients/search/${val}`, true);
            x.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    let Clients = response.clients;
                    let entreprise = response.entreprise;
                    afficher(Clients, entreprise);
                }
            };
            x.send();
        }

        function showSearchType() {
            var searchType = document.getElementById('typefind').value;
            var normaleDiv = document.getElementById('search-normale');
            var soldeDiv = document.getElementById('search-solde');
            normaleDiv.style.display = 'none';
            soldeDiv.style.display = 'none';
            if (searchType === 'normale') {
                normaleDiv.style.display = 'block';
            } else if (searchType === 'solde') {
                soldeDiv.style.display = 'block';
            } else if (searchType === 'choisir') {
                document.getElementById('inp_find').value = "";
                document.getElementById('plus').checked = false;
                document.getElementById('mois').checked = false;
                document.getElementById('egal').checked = false;
                var x = new XMLHttpRequest();
                x.open("GET", `/clients/search/`, true);
                x.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        let Clients = response.clients;
                        let entreprise = response.entreprise;
                        afficher(Clients, entreprise);
                    }
                };
                x.send();
            }
        }
    </script>
</head>

<body class>
    <div class="wrapper ">
        @include('layout.sidebar')
        <div class="main-panel" id="main-panel">
            @include('layout.navbar')
            <div class="panel-header panel-header-lg"></div>
            @php
            $items = Session::get('actions');
            @endphp
            <div class="content">
                <div class="" style="margin-top: 50px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header row">
                                    <div class="col-md-2 h5 card-title text-center" style="color: blueviolet; "> Les Clients</div>

                                    @unless (array_key_exists('client', $items) && in_array("affichage", $items["client"]))
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <select class="form-control" id="typefind" name="typefind" onchange="showSearchType()">
                                                <option value="choisir">Choisir Type recherche</option>
                                                <option value="normale">Normale</option>
                                                <option value="solde">Solde</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group" id="search-normale" style="display: none;">
                                            <input type="text" class="form-control w-100" style="border:1px solid gray" id="inp_find" placeholder="Rechercher..." onkeyup="rechercher(this)">
                                        </div>

                                        <div class="input-group" id="search-solde" style="display: none;">
                                            <input type="radio" name="solde" value="plus" id="plus" onchange="rechercherSolde()" /> <label for="plus" id="label-plus">Plus</label>
                                            <input type="radio" name="solde" value="mois" id="mois" onchange="rechercherSolde()" /> <label for="mois" id="label-mois">Mois</label>
                                            <input type="radio" name="solde" value="egal" id="egal" onchange="rechercherSolde()" /> <label for="egal" id="label-egale">Egale</label>
                                        </div>
                                    </div>

                                    @endunless
                                    <div class="col-md-3 text-center ">
                                        @unless (array_key_exists('client', $items) && in_array("ajouter", $items["client"]))
                                        <a href="#addClient" data-bs-toggle="modal" class="btn btn-primary"><i class="bi bi-plus"></i> Nouveau</a>
                                        @endunless
                                        @unless (array_key_exists('client', $items) && in_array("exporter", $items["client"]))
                                        <a href="#exportClient" data-bs-toggle="modal" class="btn btn-danger"><i class="bi bi-file-earmark-arrow-down"></i> Exporter</a>
                                        @endunless
                                    </div>
                                </div>
                                <div class="row" style="margin: 0px 25px;">
                                    @if(session('success'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert" id="autoCloseAlert">

                                        {{ session('success') }}
                                        <button type="button" class="close btn btn-info" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    @endif
                                    @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="autoCloseAlert">

                                        {{ session('error') }}
                                        <button type="button" class="close btn btn-danger" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    @endif

                                </div>
                                <div class="card-body" id="table_info">

                                    <div class="table-responsive table-border">
                                        <table class="table table-bordered  text-center">
                                            <thead class="">
                                                <tr>
                                                    <th>Identifient</th>
                                                    <th>Nom</th>
                                                    <th>Localisation</th>
                                                    <th>Commentaire</th>
                                                    <th>Balance </th>
                                                    <th>Verrouiller</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id='resultats'>

                                                @if (array_key_exists('client', $items) && in_array("affichage", $items["client"]))
                                                <tr>
                                                    <td colspan="7" class="alert alert-danger text-center fw-bold p-3 text-light">
                                                        Désolé, vous n'êtes pas autorisé à accéder à affichage.</td>
                                                </tr>
                                                @else
                                                @if(count($clients) > 0)
                                                @foreach($clients as $client)
                                                @php
                                                $bg = $client->solde >= 0 ? "bg-success" : "bg-danger";
                                                @endphp

                                                <tr>
                                                    <td class="{{$bg}}">{{ $client->username ?? '-' }}</td>
                                                    <td class="{{$bg}}">{{ $client->nom ?? '-' }}</td>
                                                    <td class="{{$bg}}">{{ $client->localisation ?? '-' }}</td>
                                                    <td class="{{$bg}}">{{ $client->commentaire ?? '-' }}</td>

                                                    <td class="{{$bg}}">{{ $client->solde }} {{ $entreprise->base_devise }}</td>
                                                    @if( $client->bloque == 'non')
                                                    <td class="{{$bg}}"><a href="#verrouillerClient" data-bs-toggle="modal" class="text-dark" onclick="info_bloque('username_verrouiller','{{ $client->username }}','bloquer')"><i class="bi bi-lock"></i> bloquer</a></td>
                                                    @else
                                                    <td class="{{$bg}}"><a href="#verrouillerClient" data-bs-toggle="modal" class="text-dark" onclick="info_bloque('username_verrouiller','{{ $client->username }}','débloquer')"><i class="bi bi-unlock"></i> débloquer</a></td>
                                                    @endif
                                                    <td class="{{$bg}}">
                                                        <a class="btn btn-warning border" href="#TypeDeviseModal" data-bs-toggle="modal" onclick="info_devise('username_devise','{{ $client->username }}')"><i class="bi bi-eye"></i></a>
                                                        <a class="btn btn-info border" href="#passwordClient" data-bs-toggle="modal" onclick="info('username_password','{{ $client->username }}')"><i class="bi bi-key"></i></a>
                                                        <a class="btn btn-danger border" href="#deleteClient" data-bs-toggle="modal" onclick="info('username_delete','{{ $client->username }}')"><i class="bi bi-trash"></i></a>
                                                        <a class="btn btn-success border" href="#updateClient" data-bs-toggle="modal" onclick="info_update('{{ $client->username }}','{{ $client->nom }}','{{ $client->localisation }}','{{ $client->commentaire }}')"><i class="bi bi-pencil"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach

                                                @else
                                                <tr>
                                                    <td colspan="7" class="alert alert-danger text-center fw-bold p-3 text-light">N'est aucun client</td>
                                                </tr>
                                                @endif
                                                @endif
                                            </tbody>

                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="TypeDeviseModal" tabindex="-1" aria-labelledby="TypeDeviseModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('clients.operation') }}">
                                @csrf
                                <div class="modal-header">
                                    <h4 class="modal-title">Choisir Devise</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless (array_key_exists('client', $items) && in_array("detail", $items["client"]))
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_devise" name="client">
                                    <div class="form-group">
                                        <label>Choisir page</label>
                                        <select class="form-control" id="page" name="page">
                                            <option value="operation"> Opérations</option>
                                            <option value="transfert"> Transferts</option>
                                            <option value="converts"> Convertir</option>
                                            <option value="deposes"> Dépôts & Retraits</option>
                                        </select>
                                    </div>

                                    @if(count($devises) > 0)
                                    <div class="form-group">
                                        <label>Type Devise</label>
                                        <select class="form-control" name="typedevise" id="typedevise">
                                            @foreach($devises as $devise)
                                            <option value="{{$devise->symbol}}">{{$devise->symbol}}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-group p-2">
                                            <div id="tout"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <table class="table">
                                            <thead>
                                                <th>Devise</th>
                                                <th class='text-end'>Balance</th>
                                                <th class='text-end'>Balance en base</th>
                                            </thead>
                                            <tbody id="soldesdevises">

                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="form-group text-center">
                                        <input type="text" class="form-control alert-danger text-danger font-weight-bol" value="Aucun type devise" />
                                    </div>
                                    @endif
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('client', $items) && in_array("detail", $items["client"]))

                                    <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                    @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    @if(count($devises) > 0)
                                    <button type="submit" class="btn btn-warning">Détail</button>
                                    @endif
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="addClient" tabindex="-1" aria-labelledby="addClientLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('clients.add') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addClientLabel">Ajouter Client</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="form-label">Identifient :</label>
                                        <input type="text" class="form-control" id="username_add" name="username" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Nom :</label>
                                        <input type="text" class="form-control" id="nom_add" name="nom" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Localisation :</label>
                                        <input type="text" list="villes" class="form-control" id="localisation_add" name="localisation" />
                                        <datalist id="villes">
                                            @foreach($villes as $ville)
                                            <option value="{{ $ville->ville }}">
                                                @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Commentaire :</label>
                                        <input type="text" class="form-control" id="commentaire_add" name="commentaire" />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="updateClient" tabindex="-1" aria-labelledby="updateClientLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('clients.update') }}">
                                @csrf
                                @method("put")
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateClientLabel">Modifier Client</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless (array_key_exists('client', $items) && in_array("modifier", $items["client"]))
                                <div class="modal-body">
                                    <!-- <div class="form-group">
                                        <label class="form-label">Identifient :</label> -->
                                    <input type="hidden" class="form-control" id="username_update" name="username" />
                                    <!-- </div> -->
                                    <div class="form-group">
                                        <label class="form-label">Nom :</label>
                                        <input type="text" class="form-control" id="nom_update" name="nom" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Localisation :</label>
                                        <input type="text"  list="villes" class="form-control" id="localisation_update" name="localisation" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Commentaire :</label>
                                        <input type="text" class="form-control" id="commentaire_update" name="commentaire" />
                                    </div>
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('client', $items) && in_array("modifier", $items["client"]))
                                    <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                    @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="deleteClient" tabindex="-1" aria-labelledby="deleteClientLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('clients.delete') }}">
                                @csrf
                                @method('delete')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteClientLabel">Supprimer Client</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless (array_key_exists('client', $items) && in_array("supprimer", $items["client"]))
                                <div class="modal-body">

                                    <input type="hidden" class="form-control" id="username_delete" name="username" />
                                    <div class="form-group">
                                        <label class="form-label">Mot de passe :</label>
                                        <input type="password" class="form-control" id="password" name="password" required />
                                    </div>
                                    <br>
                                    <p class="text-danger">Tu as sure à supprimer cette client ?</p>

                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('client', $items) && in_array("supprimer", $items["client"]))
                                    <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                    @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                    @endif

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="verrouillerClient" tabindex="-1" aria-labelledby="verrouillerClientLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('clients.update_verrouiller') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="verrouillerClientLabel">Verrouiller Client</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless (array_key_exists('client', $items) && in_array("verrouiller", $items["client"]))
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_verrouiller" name="username" />
                                    <p class="text-warning">Tu as sure à <span id="verrouiller" class="fw-bold">bloquer</span> cette client ?</p>
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('client', $items) && in_array("verrouiller", $items["client"]))
                                    <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                    @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info">Sauvegarder</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="passwordClient" tabindex="-1" aria-labelledby="passwordClientLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('clients.update_password') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="passwordClientLabel">Mot de passe Client</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless (array_key_exists('client', $items) && in_array("motpasse", $items["client"]))
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_password" name="username" />
                                    <div class="form-group">
                                        <label class="form-label">Nouveau mot de passe :</label>
                                        <input type="password" class="form-control" id="password" name="password" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirmer mot de passe :</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required />
                                    </div>
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('client', $items) && in_array("motpasse", $items["client"]))
                                    <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                    @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info">Sauvegarder</button>
                                    @endif

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="exportClient" tabindex="-1" aria-labelledby="exportClientLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exportClientLabel">Exporter les clients</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center">
                                    <a href="{{ route('clients.pdf') }}" class="btn btn-danger w-25"><i class="bi bi-file-pdf"></i> PDF</a>
                                    <a href="{{ route('clients.excel') }}" class="btn btn-success w-25"><i class="bi bi-file-excel"></i> Excel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

                @include('layout.footer')
            </div>
        </div>
        <script src="{{ asset('/assets/js/core/jquery.min.js') }}" defer></script>
        <script src="{{ asset('/assets/js/core/bootstrap.min.js') }}" defer></script>
        <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}" defer></script>
        <script src="{{ asset('/assets/js/now-ui-dashboard.min.js') }}" defer type="text/javascript"></script>

</body>

</html>