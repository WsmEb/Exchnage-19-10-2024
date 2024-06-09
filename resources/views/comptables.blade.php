<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Comptables</title>
    <!-- <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <script>
        function info(id, value) {
            document.getElementById(id).value = value;
        }

        function info_bloque(id, value, info) {
            document.getElementById(id).value = value;
            document.getElementById("verrouiller").innerHTML = info;
        }

        function info_update(username, nom, prenom) {
            document.getElementById("username_update").value = username;
            document.getElementById("nom_update").value = nom;
            document.getElementById("prenom_update").value = prenom;
        }

        function permission_pages(id) {
            document.getElementById("username_pages").value = id;
        }

        function permission_clients(id) {
            document.getElementById("username_clients").value = id;
            const checkboxContainer = document.getElementById('checkbox-clients');
            checkboxContainer.innerHTML = "";
            fetch(`{{ route('users.permissionclients', ['userId' => ':userId']) }}`.replace(':userId', id))
                .then(response => response.json())
                .then(data => {
                    // alert(data.message)
                    const allClients = data.allClients;
                    const userClientIds = data.userClientIds;
                    allClients.forEach(client => {
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = client.username;
                        checkbox.value = client.username;
                        if (userClientIds.includes(client.username)) {
                            checkbox.checked = true;
                        }
                        const label = document.createElement('label');
                        label.appendChild(checkbox);
                        label.appendChild(document.createTextNode(" " + client.nom));

                        checkboxContainer.appendChild(label);
                        checkboxContainer.appendChild(document.createElement('br'));
                    });
                })
                .catch(error => {
                    console.error('Error fetching client data:', error);
                });
        }

        function permission_actions(id) {
            document.getElementById("username_actions").value = id;
        }
        setTimeout(function() {
            let autoCloseAlert = document.querySelector("#autoCloseAlert");
            if (autoCloseAlert)
                autoCloseAlert.style.display = "none";
        }, 5000);


        function permission_pages(id) {
            document.getElementById("username_pages").value = id;
            const checkboxContainer = document.getElementById('checkbox-pages');
            checkboxContainer.innerHTML = "";
            const AllPages = [
                ["dashboard", "Dashboard"],
                ["client", "Clients"],
                ["devise", "Devises"],
                ["operation", "Opérations avec clients"],
                ["transfert", "Transfert entre les clients"],
                ["convertir", "Convertier entre devises"],
                ["deposer", "Déposer en stock"],
                ["stock", "Stock"],
                ["historique", "Historique Opérations"],
            ]
            fetch(`{{ route('users.permissionpages', ['userId' => ':userId']) }}`.replace(':userId', id))
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    const userPageIds = data.userPageIds;
                    AllPages.forEach(page => {
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = page[0];
                        checkbox.value = page[0];
                        if (userPageIds.includes(page[0])) {
                            checkbox.checked = true;
                        }
                        const label = document.createElement('label');
                        label.appendChild(checkbox);
                        label.appendChild(document.createTextNode(" " + page[1]));

                        checkboxContainer.appendChild(label);
                        checkboxContainer.appendChild(document.createElement('br'));
                    });
                })
                .catch(error => {
                    console.error('Error fetching client data:', error);
                });
        }


        function ChangePages(select) {


            const actions_pages = {
                "devise": [
                    ["ajouter", "Ajouter devise"],
                    ["supprimer", "Supprimer devise"],
                    ["modifier", "Modifier devise"],
                    ["exporter", "Exporter des devises"],
                    ["base", "Modifier devise de base"],
                    ["affichage", "Affichage de tableau de devises"]
                ],
                "client": [
                    ["ajouter", "Ajouter client"],
                    ["supprimer", "Supprimer client"],
                    ["modifier", "Modifier client"],
                    ["exporter", "Exporter des clients"],
                    ["motpasse", "Modifier le mot de passe"],
                    ["detail", "Voir le detail de client"],
                    ["verrouiller", "verrouiller de client"],
                    ["affichage", "Affichage de tableau"],
                ],
                "operation": [
                    ["ajouter", "Ajouter Opération"],
                    ["supprimer", "Supprimer Opération"],
                    ["modifier", "Modifier Opération"],
                    ["exporter", "Exporter des Opérations"],
                    ["affichage", "Affichage de tableau de Opérations"],
                    ["archiver", "Archivage des opérations"]
                ],
                "transfert": [
                    ["ajouter", "Ajouter Transfert"],
                    ["supprimer", "Supprimer Transfert"],
                    ["modifier", "Modifier Transfert"],
                    ["exporter", "Exporter des Transferts"],
                    ["affichage", "Affichage de tableau de Transferts"],
                    ["archiver", "Archivage des transferts"]
                ],
                "convertir": [
                    ["ajouter", "Ajouter convertir"],
                    ["supprimer", "Supprimer convertir"],
                    ["modifier", "Modifier convertir"],
                    ["exporter", "Exporter des convertirs"],
                    ["affichage", "Affichage de tableau de convertirs"],
                    ["archiver", "Archivage des convertirs"]
                ],
                "deposer": [
                    ["ajouter", "Ajouter deposer"],
                    ["supprimer", "Supprimer deposer"],
                    ["modifier", "Modifier deposer"],
                    ["exporter", "Exporter des deposers"],
                    ["affichage", "Affichage de tableau de deposers"],
                    ["archiver", "Archivage des deposers"]
                ],
                "stock": [
                    ["exporter", "Exporter des stock"],
                    ["detail", "Voir detail du stock"],
                    ["affichage", "Affichage de Liste des stock"]
                ],
                "historique": [
                    ["supprimer", "Supprimer Historique"],
                    ["modifier", "Modifier Historique"],
                    ["detail", "Voir detail du Historique"],
                    ["affichage", "Affichage de Liste des historiques"]
                ],
            }


            const div = document.getElementById("liste_actions");
            const btn_actions = document.getElementById("btn-actions");
            const client = document.getElementById("username_actions");

            div.innerHTML = "";
            btn_actions.disabled = true;

            if (select.value != "choisir") {
                btn_actions.disabled = false;

                fetch(`{{ route('users.permissionactions', ['userId' => ':userId', 'page' => ':page']) }}`
                        .replace(':userId', client.value)
                        .replace(':page', select.value))
                    .then(response => response.json())
                    .then(data => {
                        // console.log(data);
                        actions_pages[select.value].forEach(action => {
                            if (data.actions.indexOf(action[0]) !== -1) div.innerHTML += `<div class='form-group'><input type='checkbox' checked value='${action[0]}' name='${action[0]}' id='${action[0]}' class='form-check-input' /> <label for='${action[0]}'>${action[1]}</label></div>`;
                            else div.innerHTML += `<div class='form-group'><input type='checkbox' name='${action[0]}' value='${action[0]}' id='${action[0]}' class='form-check-input' /> <label for='${action[0]}'>${action[1]}</label></div>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching client data:', error);
                    });
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
            <div class="content">
                <div class="" style="margin-top: 50px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header row">
                                    <div class="col-md-3 h5 card-title text-center" style="color: blueviolet; "> Les Comptables</div>
                                    <div class="col-md-6">
                                        <!-- <div class="input-group">
                                            <input type="text" class="form-control" style="border:1px solid gray" placeholder="Rechercher..." onkeyup="rechercher(this)">

                                        </div> -->
                                    </div>
                                    <div class="col-md-3 text-center ">
                                        <a href="#addComptable" data-bs-toggle="modal" class="btn btn-primary"><i class="bi bi-plus"></i> Nouveau</a>
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
                                            <thead class="text-nowrap">
                                                <tr>
                                                    <th>Identifient</th>
                                                    <th>Nom</th>
                                                    <th>Prénom</th>
                                                    <th>Verrouiller</th>
                                                    <th>Permission</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id='resultats text-nowrap'>

                                                @if(count($users) > 0)
                                                @foreach($users as $user)
                                                @php
                                                $bg = $user->is_connected == true ? "text-warning" : "";
                                                @endphp
                                                <tr>
                                                    <td><i class="bi bi-person-circle {{$bg}}"></i> {{ $user->username }} </td>
                                                    <td>{{ $user->nom }}</td>
                                                    <td>{{ $user->prenom }}</td>
                                                    @if( $user->bloque == 'non')
                                                    <td><a href="#verrouillerComptable" data-bs-toggle="modal" onclick="info_bloque('username_verrouiller','{{ $user->username }}','bloquer')"><i class="bi bi-lock"></i> bloquer</a></td>
                                                    @else
                                                    <td><a href="#verrouillerComptable" data-bs-toggle="modal" onclick="info_bloque('username_verrouiller','{{ $user->username }}','débloquer')"><i class="bi bi-unlock"></i> débloquer</a></td>
                                                    @endif
                                                    <td class="">
                                                        <div class="d-flex justify-content-center">
                                                            <a class="btn btn-secondary my-1" href="#actionsComptable" data-bs-toggle="modal" onclick="permission_actions('{{ $user->username }}')"><i class="bi bi-hand-index"></i></a> 
                                                            <a class="btn btn-success  my-1" href="#pagesComptable" data-bs-toggle="modal" onclick="permission_pages('{{ $user->username }}')"><i class="bi bi-file-earmark"></i></a> 
                                                            <a class="btn btn-primary  my-1" href="#clientsComptable" data-bs-toggle="modal" onclick="permission_clients('{{ $user->username }}')"><i class="bi bi-person"></i></a>

                                                        </div>
                                                    </td>
                                                    <td class="d-flex justify-content-center">
                                                        <a class="btn btn-info  my-1" href="#passwordComptable" data-bs-toggle="modal" onclick="info('username_password','{{ $user->username }}')"><i class="bi bi-key"></i></a> 
                                                        <a class="btn btn-danger  my-1" href="#deleteComptable" data-bs-toggle="modal" onclick="info('username_delete','{{ $user->username }}')"><i class="bi bi-trash"></i></a> 
                                                        <a class="btn btn-warning  my-1" href="#updateComptable" data-bs-toggle="modal" onclick="info_update('{{ $user->username }}','{{ $user->nom }}','{{ $user->prenom }}')"><i class="bi bi-pencil"></i></a> 

                                                        @if( $user->is_connected == true)
                                                        <a class="btn btn-success  my-1" href="#deconnecteComptable" data-bs-toggle="modal" onclick="info('username_deconnect','{{ $user->username }}')"><i class="bi bi-power"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach

                                                @else
                                                <tr>
                                                    <td colspan="7" class="alert alert-danger text-center fw-bold p-3 text-light">N'est aucun comptable</td>
                                                </tr>
                                                @endif
                                            </tbody>

                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="addComptable" tabindex="-1" aria-labelledby="addComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comptables.add') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addComptableLabel">Ajouter Comptable</h5>
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
                                        <label class="form-label">Prénom :</label>
                                        <input type="text" class="form-control" id="prenom_add" name="prenom" />
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
                <div class="modal fade" id="updateComptable" tabindex="-1" aria-labelledby="updateComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comptables.update') }}">
                                @csrf
                                @method("put")
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateComptableLabel">Modifier Comptable</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_update" name="username" />

                                    <div class="form-group">
                                        <label class="form-label">Nom :</label>
                                        <input type="text" class="form-control" id="nom_update" name="nom" />
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Prénom :</label>
                                        <input type="text" class="form-control" id="prenom_update" name="prenom" />
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
                <div class="modal fade" id="deleteComptable" tabindex="-1" aria-labelledby="deleteComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comptables.delete') }}">
                                @csrf
                                @method('delete')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteComptableLabel">Supprimer Comptable</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_delete" name="username" />
                                    <div class="form-group">
                                        <label class="form-label">Mot de passe :</label>
                                        <input type="password" class="form-control" id="password" name="password" required />
                                    </div>
                                    <br>
                                    <p class="text-danger">Tu as sure à supprimer cette comptable ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="deconnecteComptable" tabindex="-1" aria-labelledby="deconnecteComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comptables.deconnecter') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deconnecteComptableLabel">Déconnecter Comptable</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_deconnect" name="username" />
                                    <p class="text-danger">Tu as sure à déconnecter cette comptable ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Déconnecter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="verrouillerComptable" tabindex="-1" aria-labelledby="verrouillerComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comptables.update_verrouiller') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="verrouillerComptableLabel">Verrouiller Comptable</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_verrouiller" name="username" />
                                    <p class="text-warning">Tu as sure à <span id="verrouiller" class="fw-bold">bloquer</span> cette comptable ?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info">Sauvegarder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="passwordComptable" tabindex="-1" aria-labelledby="passwordComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('comptables.update_password') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="passwordComptableLabel">Mot de passe Comptable</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
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
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info">Sauvegarder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="pagesComptable" tabindex="-1" aria-labelledby="pagesComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('users.addpermissionpages') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="pagesComptableLabel">Permission des pages</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_pages" name="username" />
                                    <div id="checkbox-pages">

                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info">Sauvegarder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="clientsComptable" tabindex="-1" aria-labelledby="clientsComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('users.addpermissionclients') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="clientsComptableLabel">Permission des clients</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_clients" name="username" />
                                    <div id="checkbox-clients">

                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info">Sauvegarder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="actionsComptable" tabindex="-1" aria-labelledby="actionsComptableLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('users.addpermissionactions') }}">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="actionsComptableLabel">Permission des actions</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_actions" name="username" />

                                    <div class="form-group mb-2">
                                        <label>Page :</label>
                                        <select name="page" id="page" class="form-control" onchange="ChangePages(this)">
                                            <option value="choisir">Choisir page</option>
                                            <option value="client">Client</option>
                                            <option value="devise">Devise</option>
                                            <option value="operation">Opérations clients</option>
                                            <option value="transfert">Transfert entre les clients</option>
                                            <option value="convertir">Convertier entre devises</option>
                                            <option value="deposer">Déposer en stock</option>
                                            <option value="stock">Stock</option>
                                            <option value="historique">Historique Opérations</option>
                                        </select>
                                    </div>


                                    <div id="liste_actions"></div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-info" id="btn-actions" disabled>Sauvegarder</button>
                                </div>
                            </form>
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