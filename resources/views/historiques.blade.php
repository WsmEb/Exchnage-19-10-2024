<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Historiques Opérations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}"> -->
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <script>
        function info_delete(id, val) {
            document.getElementById(id).value = val;
        }

        function info_show(id) {
            document.getElementById("lien_operations").href = "/historiquesoperations/" + id;
            document.getElementById("lien_transferts").href = "/historiquestransferts/" + id;
            document.getElementById("lien_deposers").href = "/historiquesdeposers/" + id;
            document.getElementById("lien_convertir").href = "/historiquesconvertes/" + id;

        }
        setTimeout(function() {
            document.querySelector('.message').style.display = 'none';
        }, 5000);

        function info_update(id, date, commentaire, solde) {
            document.getElementById("id_update").value = id;
            document.getElementById("date_update").value = date;
            document.getElementById("commentaire_update").value = commentaire;
            document.getElementById("solde_update").value = parseFloat(solde).toFixed(2);
        }

        function ChangeRecherche(select) {
            let val = select.value;
            let periode_rechereche = document.getElementById("periode_rechereche");
            let normale_rechereche = document.getElementById("normale_rechereche");
            let input = document.getElementById("input_recherche");
            input.value = "";
            normale_rechereche.style.display = "block";
            periode_rechereche.style.display = "none";
            find()
            if (val !== "choisir") {
                input.readonly = false
                switch (val) {
                    case "date":
                        normale_rechereche.style.display = "none";
                        periode_rechereche.style.display = "flex";
                        break;
                    case "client":
                        input.placeholder = "Tapez le nom du client à rechercher";
                        break;
                    case "commentaire":
                        input.placeholder = "Tapez le commentaire à rechercher";
                        break;
                }
                input.disabled = false
            } else {
                input.placeholder = "Rechercher";
                input.value = "";
                input.disabled = true
                find()
            }
        }

        function checkDates() {
            var date1 = document.getElementById('date1').value;
            var date2 = document.getElementById('date2').value;

            if (date1 && date2)
                if (date1 > date2)
                    document.getElementById('date1').value = date2;
        }

        function find() {
            const collone_recherche = document.getElementById('collone_recherche');
            var x = new XMLHttpRequest()
            let url = `/historique/`;
            if (collone_recherche.value == "date") {
                checkDates()
                const datedebut = document.getElementById("date1").value;
                const datefin = document.getElementById("date2").value;
                if (datedebut != "" && datefin != "")
                    x.open('GET', url + `date/${datedebut}/${datefin}`, true);
                else
                    x.open('GET', url + `date/`, true);

            } else {
                val = document.getElementById("input_recherche").value;
                x.open('GET', url + `search/${collone_recherche.value}/${val}`, true);
            }
            x.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    data = JSON.parse(this.responseText);
                    var tbody = document.getElementById('resultats');
                    var html = '';
                    tbody.innerHTML = '';
                    data.forEach(function(element, index) {
                        html += '<tr>';
                        html += '<td>' + element.datehistorique + '</td>';
                        html += '<td>' + element.commentaire + '</td>';
                        html += '<td>' + element.nom_client + '</td>';
                        html += '<td>' + element.valeur.toFixed(2) + " " + element.devise + '</td>';
                        html += '<td>';
                        html += '<a class="btn btn-warning"  href="#showHistorique" data-bs-toggle="modal" onclick="info_show( \'' + element.id + '\')"><i class="bi bi-eye"></i></a> ';
                        html += '<a class="btn btn-danger" href="#deleteHistorique" data-bs-toggle="modal" onclick="info_delete(\'id_delete\', \'' + element.id + '\')"><i class="bi bi-trash"></i></a> ';
                        html += '<a class="btn btn-success" href="#updateHistorique" data-bs-toggle="modal" onclick="info_update(\'' + element.id + '\', \'' + element.datehistorique + '\', \'' + element.commentaire + '\', \'' + element.valeur + '\')"><i class="bi bi-pencil"></i></a>';
                        html += '</td>';
                        html += '</tr>';
                    });

                    if (data.length === 0) {
                        html += '<tr>';
                        html += '<td colspan="6" class="alert alert-danger text-center fw-bold p-3 text-light">N\'est aucun Historique</td>';
                        html += '</tr>';
                    }
                    tbody.innerHTML = html;
                }
            }
            x.send();
        }
    </script>
</head>

<body class>
    <div class="wrapper ">
        @include('layout.sidebar')
        <div class="main-panel" id="main-panel">
            @include('layout.navbar')
            @php
            $items = Session::get('actions');
            @endphp
            <div class="panel-header panel-header-lg"></div>
            <div class="content">
                <div class="" style="margin-top: 50px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header row">
                                    <div class="col-md-5 h5 card-title text-center" style="color: blueviolet; "> Historiques</div>

                                    @unless (array_key_exists('historique', $items) && in_array("affichage", $items["historique"]))
                                    <div class="col-md-2 p-1">
                                        <select class="form-control w-100 mr-2" onchange="ChangeRecherche(this)" id="collone_recherche">
                                            <option value="choisir">Choisir Recherche</option>
                                            <option value="date">Date Historique</option>
                                            <option value="commentaire">Commentaire</option>
                                            <option value="client">Nom du client</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 p-1">
                                        <div id="normale_rechereche">
                                            <input type="text" class="form-control" placeholder="Rechercher client" disabled id="input_recherche" onkeyup="find()" />
                                        </div>
                                        <div id="periode_rechereche" style="display:none;">
                                            <input type="date" class="form-control w-50" placeholder="Rechercher client" id="date1" onchange="find()" />
                                            <input type="date" class="form-control w-50" placeholder="Rechercher client" id="date2" onchange="find()" />
                                        </div>
                                    </div>
                                    @endunless
                                </div>
                                <div class="row message" style="margin: 0px 25px;">
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
                                            <thead class=" text-nowrap">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Commentaire</th>
                                                    <th>Client </th>
                                                    <th>Valeur Terminé</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id='resultats text-nowrap'>
                                                @if (array_key_exists('historique', $items) && in_array("affichage", $items["historique"]))
                                                <tr>
                                                    <td colspan="5" class="alert alert-danger text-center fw-bold p-3 text-light">
                                                        Désolé, vous n'êtes pas autorisé à accéder à affichage.</td>
                                                </tr>
                                                @else
                                                @if(count($historiques) > 0)
                                                @foreach($historiques as $hist)
                                                @php
                                                if (session()->has('hover') && in_array($hist->id, session('hover')))
                                                $bg ="bg-info";
                                                else
                                                $bg = "";
                                                @endphp
                                                <tr class=" text-nowrap">
                                                    <td class="{{$bg}}">{{ $hist->datehistorique}}</td>
                                                    <td class="{{$bg}}">{{ $hist->commentaire}}</td>
                                                    <td class="{{$bg}}">{{ $hist->nom_client}}</td>
                                                    <td class="{{$bg}}">{{ number_format($hist->valeur,2) }} {{ $hist->devise}}</td>
                                                    <td class="{{$bg}}">
                                                        <a class="btn btn-warning" href="#showHistorique" data-bs-toggle="modal" onclick="info_show('{{$hist->id}}')"><i class="bi bi-eye"></i></a>
                                                        <a class="btn btn-danger" href="#deleteHistorique" data-bs-toggle="modal" onclick="info_delete('id_delete','{{$hist->id}}')"><i class="bi bi-trash"></i></a>
                                                        <a class="btn btn-success" href="#updateHistorique" data-bs-toggle="modal" onclick="info_update('{{$hist->id}}','{{$hist->datehistorique}}','{{$hist->commentaire}}','{{$hist->valeur}}')"><i class="bi bi-pencil"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach

                                                @else
                                                <tr>
                                                    <td colspan="6" class="alert alert-danger text-center fw-bold p-3 text-light">N'est aucun Historique</td>
                                                </tr>
                                                @endif

                                                @endif
                                            </tbody>

                                        </table>
                                        @php
                                        Session::forget('hover');
                                        @endphp
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="updateHistorique" tabindex="-1" aria-labelledby="updateHistoriqueLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('historique.update') }}">
                                @csrf
                                @method("put")
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateHistoriqueLabel">Modifier Client</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless (array_key_exists('historique', $items) && in_array("modifier", $items["historique"]))
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="id_update" name="id" />
                                    <div class="form-group">
                                        <label class="form-label">Date :</label>
                                        <input type="date" class="form-control" id="date_update" name="date" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Commentaire :</label>
                                        <textarea class="form-control" id="commentaire_update" name="commentaire" row="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Solde :</label>
                                        <input type="text" class="form-control" pattern="^(\+|-)?\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" id="solde_update" name="solde" />
                                    </div>
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('historique', $items) && in_array("modifier", $items["historique"]))
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
                <div class="modal fade" id="deleteHistorique" tabindex="-1" aria-labelledby="deleteHistoriqueLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('historique.delete') }}">
                                @csrf
                                @method('delete')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteHistoriqueLabel">Supprimer Historique</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless (array_key_exists('historique', $items) && in_array("supprimer", $items["historique"]))
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="id_delete" name="id" />
                                    <div class="form-group">
                                        <label class="form-label">Mot de passe :</label>
                                        <input type="password" class="form-control" id="password" name="password" required />
                                    </div>
                                    <br>
                                    <p class="text-danger">Tu as sure à supprimer cette historique ?</p>
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('historique', $items) && in_array("supprimer", $items["historique"]))
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



                <div class="modal fade" id="showHistorique" tabindex="-1" aria-labelledby="showHistoriqueLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteDetailLabel">Historiques</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if (array_key_exists('historique', $items) && in_array("supprimer", $items["historique"]))
                                <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                @else
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        <div><a href="" id="lien_operations" class="btn btn-primary btn-block my-2 w-100">Historiques des opérations</a></div>
                                        <div><a href="" id="lien_transferts" class="btn btn-danger btn-block my-2 w-100">Historiques des transferts</a></div>
                                        <div><a href="" id="lien_convertir" class="btn btn-success btn-block my-2 w-100">Historiques des Convertir</a></div>
                                        <div><a href="" id="lien_deposers" class="btn btn-info btn-block my-2 w-100">Historiques des stock</a></div>
                                    </div>
                                </div>
                                @endif
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