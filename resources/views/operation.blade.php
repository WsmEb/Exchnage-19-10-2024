   <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Opération</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/highlights.css') }}">
    <script src="{{ asset('/assets/js/chart.min.js') }}"></script>
    <script src="{{ asset('/js/deconnexion.js') }}"></script>

    <script>
        var operations_check = [];
        var total_operations = 0;


        function toggleCheckboxes() {
            const toutOperationCheckbox = document.getElementById('toutoperation');
            const checkboxes = document.querySelectorAll('.check_historique');
            total_operations = 0;
            operations_check = [];
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = toutOperationCheckbox.checked;
                let valeurs = checkbox.value.split('***');
                if (toutOperationCheckbox.checked)
                    check_operation(checkbox, valeurs[0], valeurs[1], valeurs[2]);
                else
                    document.getElementById("nb_check").innerHTML = operations_check.length;

            });

        }

        function check_operation(check_input, id, type, solde) {
            if (check_input.checked) {
                operations_check.push(parseInt(id));
                total_operations = type === "moi" ? total_operations + parseFloat(solde) : total_operations - parseFloat(solde);
            } else {
                const index = operations_check.indexOf(parseInt(id));
                if (index > -1)
                    operations_check.splice(index, 1);
                total_operations = type === "moi" ? total_operations - parseFloat(solde) : total_operations + parseFloat(solde);
            }
            document.getElementById("nb_check").innerHTML = operations_check.length;
        }

        function archiver_operation() {
            document.getElementById("btn_historique").disabled = operations_check.length > 0 ? false : true;
            document.getElementById("solde_historique").value = document.getElementById("solde_historique_exist").value = total_operations.toFixed(2);
        }


        function addhistorique() {
            const newHistoryRadio = document.getElementById('newHistory');
            const nouvelleDiv = document.getElementById('nouvelle');
            const existeDiv = document.getElementById('existe');
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            if (newHistoryRadio.checked) {
                let date = document.getElementById('date_historique');
                let commentaire = document.getElementById('commentaire_historique');
                let valeur = document.getElementById('solde_historique');
                let client = document.getElementById('client_historique');
                let devise = document.getElementById('devise_historique');
                let xhr = new XMLHttpRequest();
                xhr.open('POST', '/historique/add-operations', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Erreur lors de l\'ajout de l\'historique: ' + xhr.responseText);
                    }
                };
                xhr.send(JSON.stringify({
                    date: date.value,
                    commentaire: commentaire.value,
                    valeur: valeur.value,
                    client: client.value,
                    devise: devise.value,
                    operations_check: operations_check,
                }));
            } else {
                const radios = document.getElementsByName('old_historique');
                let valeur = document.getElementById('solde_historique_exist');
                let selectedValue = null;
                for (const radio of radios) {
                    if (radio.checked) {
                        selectedValue = radio.value;
                        break;
                    }
                }
                let xhr = new XMLHttpRequest();
                xhr.open('POST', '/historique/addexiste-operations', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Erreur lors de l\'ajout de l\'historique: ' + xhr.responseText);
                    }
                };
                xhr.send(JSON.stringify({
                    operations_check: operations_check,
                    id_historique: selectedValue,
                    valeur: valeur.value,
                }));
            }
        }

        function checkDates() {
            var date1 = document.getElementById('date1').value;
            var date2 = document.getElementById('date2').value;

            if (date1 && date2)
                if (date1 > date2)
                    document.getElementById('date1').value = date2;
        }

        function ChangeRecherche(select, client, devise, base, devise_base) {
            let val = select.value;
            let periode_rechereche = document.getElementById("periode_rechereche");
            let normale_rechereche = document.getElementById("normale_rechereche");
            let type_rechereche = document.getElementById("type_rechereche");
            let input = document.getElementById("input_recherche");
            input.value = "";
            normale_rechereche.style.display = "block";
            periode_rechereche.style.display = "none";
            type_rechereche.style.display = "none";
            find(client, devise, base, devise_base)
            if (val !== "choisir") {
                input.readonly = false
                switch (val) {
                    case "date":
                        normale_rechereche.style.display = "none";
                        periode_rechereche.style.display = "flex";
                        break;
                    case "comments":
                        input.type = "text";
                        input.placeholder = "Tapez le commentaire à rechercher";
                        break;
                    case "type_operation":
                        normale_rechereche.style.display = "none";
                        type_rechereche.style.display = "flex";
                        break;
                    case "prix":
                        input.type = "number";
                        input.placeholder = "Tapez le prix à rechercher";
                        break;
                    case "quantity":
                        input.type = "number";
                        input.placeholder = "Tapez le quantité à rechercher";
                        break;
                    case "ville":
                        input.type = "text";
                        input.placeholder = "Tapez la ville à rechercher";
                        break;
                }
                input.disabled = false
            } else {
                input.placeholder = "Rechercher";
                input.value = "";
                input.disabled = true
                find(client, devise, base, devise_base)
            }
        }

        function find(client, devise, base, devise_base) {
            const collone_recherche = document.getElementById('collone_recherche');
            var x = new XMLHttpRequest()
            let url = `/operations/`;
            if (collone_recherche.value == "date") {
                checkDates()
                const datedebut = document.getElementById("date1").value;
                const datefin = document.getElementById("date2").value;
                if (datedebut != "" && datefin != "")
                    x.open('GET', url + `date/${client}/${devise}/${datedebut}/${datefin}`, true);
                else
                    x.open('GET', url + `date/${client}/${devise}`, true);
                x.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        data = JSON.parse(this.responseText);
                        afficher(data[0])
                        document.getElementById("statistique_moi").innerHTML = data[1][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_toi").innerHTML = data[2][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_total").innerHTML = data[3].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_moi_base").innerHTML = (data[1][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_toi_base").innerHTML = (data[2][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_total_base").innerHTML = (data[3] * base).toFixed(2) + ` ${devise_base}`;
                        myPieChart.data.labels = [data[1][1] + " moi",
                                data[2][1] + " toi"
                            ],
                            myPieChart.data.datasets = [{
                                data: [data[1][1], data[2][1]],
                                backgroundColor: ['green', 'red'],
                                borderColor: 'black',
                                borderWidth: 1
                            }];
                        myPieChart.update();
                    }
                }
            } else {
                let val = "";
                if (collone_recherche.value == "type_operation") {
                    if (document.getElementById("toi_find").checked == true)
                        val = "toi";
                    else if (document.getElementById("moi_find").checked == true)
                        val = "moi";
                    else
                        val = "";
                } else
                    val = document.getElementById("input_recherche").value;

                x.open('GET', url + `search/${client}/${devise}/${collone_recherche.value}/${val}`, true);
                x.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        data = JSON.parse(this.responseText);
                        afficher(data[0])
                        document.getElementById("statistique_moi").innerHTML = data[1][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_toi").innerHTML = data[2][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_total").innerHTML = data[3].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_moi_base").innerHTML = (data[1][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_toi_base").innerHTML = (data[2][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_total_base").innerHTML = (data[3] * base).toFixed(2) + ` ${devise_base}`;
                        myPieChart.data.labels = [data[1][1] + " moi",
                                data[2][1] + " toi"
                            ],
                            myPieChart.data.datasets = [{
                                data: [data[1][1], data[2][1]],
                                backgroundColor: ['green', 'red'],
                                borderColor: 'black',
                                borderWidth: 1
                            }];
                        myPieChart.update();
                    }
                }
            }
            x.send();
        }


        function Informations_Update(id, date, ville, quantite, prix, type, commentaire, total, porcentage) {
            // alert(type);
            let inputs = document.getElementsByClassName("update");
            inputs[0].value = id;
            inputs[1].value = date !== "null" ? date : "";
            inputs[2].value = ville !== "null" ? ville : "";
            inputs[3].value = quantite;
            inputs[4].value = prix;
            inputs[5].value = total;
            inputs[6].value = porcentage.slice(0, 1);
            inputs[7].value = porcentage.slice(1);
            inputs[8].value = commentaire !== "null" ? commentaire : "";
            document.getElementById(type + "_update").checked = true;
        }

        function Information_Delete(id) {
            document.getElementById("id_delete").value = id;
        }
        setTimeout(function() {
            document.querySelector('.message').style.display = 'none';
        }, 5000);


        function appendValue(value, idinput) {
            document.getElementById(idinput).value += value;
        }

        function clearDisplay(idinput) {
            document.getElementById(idinput).value = "";
        }

        function CalculePrixQuantite(Quantite, Prix, Operation1, Operation2, Porcentage, Total) {
            let quantite = document.getElementById(Quantite);
            let prix = document.getElementById(Prix);
            let operation1 = document.getElementById(Operation1);
            let operation2 = document.getElementById(Operation2);
            let porcentage = document.getElementById(Porcentage);
            let total = document.getElementById(Total);

            let som = 0;
            if (quantite.value != "" && prix.value != "")
                som = eval(quantite.value + operation1.value + prix.value);
            else
                som = 0;

            total.value = eval(som + operation2.value + som + "*" + porcentage.value + "/100")
        }

        function afficher(tab) {

            document.getElementById("informations").innerHTML = "";
            if (tab.length != 0) {
                for (const i in tab) {
                    bgcolor = tab[i].type_operation == 'moi' ? "rgba(0, 128, 0, 0.7)" : "rgba(255, 0, 0, 0.8)";
                    ligne = "<tr style='background-color: " + bgcolor + ";'>";
                    if (operations_check.includes(parseInt(tab[i].id))) {
                        ligne += `<td><input type="checkbox" value="${tab[i].id}***${tab[i].type_operation}***${tab[i].total}" checked onclick="check_operation(this,'${tab[i].id}','${tab[i].type_operation}','${tab[i].total}')" name="${tab[i].id}" id="${tab[i].id}" class="check_historique" /></td>`;
                    } else
                        ligne += `<td><input type="checkbox" value="${tab[i].id}***${tab[i].type_operation}***${tab[i].total}" onclick="check_operation(this,'${tab[i].id}','${tab[i].type_operation}','${tab[i].total}')" name="${tab[i].id}" id="${tab[i].id}" class="check_historique" /></td>`;

                    ligne += `<td>${tab[i].date !== null ? tab[i].date : "-"}</td>`;
                    ligne += `<td>${tab[i].ville !== null ? tab[i].ville : "-"}</td>`;
                    ligne += `<td>${tab[i].quantity !== null ? tab[i].quantity : "-"}</td>`;
                    ligne += `<td>${tab[i].prix !== null ? tab[i].prix : "-"}</td>`;
                    ligne += `<td>${tab[i].comments !== null ? tab[i].comments : "-"}</td>`;
                    ligne += `<td>${tab[i].percentage !== null ? tab[i].percentage : "-"}</td>`;
                    ligne += `<td>${tab[i].total !== null ? (tab[i].total).toFixed(2) : "-"}</td>`;
                    ligne += `<td> 
                           <a class="btn btn-danger" href="#deleteOperationModal" data-bs-toggle="modal" onclick="Information_Delete('${tab[i].id}')"><i class="bi bi-trash"></i></a>
                            <a class="btn btn-warning" href="#updateOperationModal" data-bs-toggle="modal" onclick="Informations_Update('${tab[i].id}','${tab[i].date}','${tab[i].ville}','${tab[i].quantity}','${tab[i].prix}','${tab[i].type_operation}','${tab[i].comments}','${tab[i].total}','${tab[i].percentage}')"><i class="bi bi-pencil"></i></a>
                        </td>`;
                    ligne += "</tr>";
                    document.getElementById("informations").innerHTML += ligne;
                }
            } else
                document.getElementById(
                    "informations"
                ).innerHTML = `<tr><td colspan="9" class="alert alert-danger text-center fw-bold p-3 text-light">Aucun Opération a cette client avec cette devise !</td></tr>`;

        }



        function toggleDivs() {
            const newHistoryRadio = document.getElementById('newHistory');
            const nouvelleDiv = document.getElementById('nouvelle');
            const existeDiv = document.getElementById('existe');

            if (newHistoryRadio.checked) {
                document.getElementById("btn_historique").disabled = operations_check.length > 0 ? false : true;
                nouvelleDiv.classList.remove('d-none');
                existeDiv.classList.add('d-none');
            } else {

                document.getElementById("btn_historique").disabled = false;
                nouvelleDiv.classList.add('d-none');
                existeDiv.classList.remove('d-none');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleDivs();
        });
    </script>
    <style>
        .btn-calculatrice {
            width: 40px
        }

        /* Additional Styles for Responsive Table */
        @media (max-width: 576px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                -ms-overflow-style: -ms-autohiding-scrollbar;
            }
        }

        input,
        select,
        textarea {
            background-color: white !important;
            border: 1px solid darkcyan !important;
        }

        .label {
            padding: 5px;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-weight: bold;
        }

        th,
        td {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .nav-button {
            width: 33%;
            text-align: center;
            background-color: aliceblue;
            font-size: 17px !important;
            border-radius: 10px;
            /* box-shadow: 2px 2px 1px gray; */
            /* border-bottom: 3px solid black; */
        }

        @media (max-width: 576px) {
            .nav-button {
                width: 100%;
            }
        }

        .radio-btn {
            display: none;
        }

        .radio-btn+label {
            margin-bottom: 0;
            cursor: pointer;
        }

        .radio-btn+label::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border: 2px solid black;
            border-radius: 50%;
            background-color: white;
            vertical-align: middle;
        }

        .radio-btn:checked+label::before {
            background-color: black;
            border-color: white;
        }


        .radio-btn+label.btn {
            background-color: white;
            border-color: gray;
            color: black;
        }

        .radio-btn:checked+label.btn {
            background-color: gray;
            color: white;
        }
    </style>
</head>

<body class="user-profile">
    <div class="wrapper">
        @include('layout.sidebar')
        <div class="main-panel" id="main-panel">

            @include('layout.navbar')

            @php
            $items = Session::get('actions');
            @endphp
            <div class="panel-header panel-header-sm"></div>
            <div class="content">
                <div style="margin-top: 50px;">
                    <div class="row">
                        <div class="col-md-8 text-center">
                            <h5 class="font-weight-bold">Gestion Opérations Avec M.{{$client->nom}}</h5>
                        </div>
                        <div class="col-md-4 message">
                            @if(session('success'))
                            <div class="alert alert-info text-light fw-bold">
                                {{ session('success') }}
                            </div>
                            @endif
                            @if(session('error'))
                            <div class="alert alert-danger text-light fw-bold">
                                {{ session('error') }}
                            </div>

                            @endif
                        </div>
                    </div>
                    <div class="row">
                        @unless ((array_key_exists('operation', $items) && in_array("affichage", $items["operation"])))

                        <div class="col-md-2 p-1">
                            <select class="form-control w-100 mr-2" onchange="ChangeRecherche(this,'{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" id="collone_recherche">
                                <option value="choisir">Choisir Recherche</option>
                                <option value="date">Date d'opération</option>
                                <option value="comments">Commentaire</option>
                                <option value="type_operation">Type Opération</option>
                                <!-- <option value="prix">Prix</option>
                                <option value="quantity">Quantité</option> -->
                                <option value="ville">Ville</option>
                            </select>
                        </div>
                        <div class="col-md-6 p-1">
                            <div id="normale_rechereche">
                                <input type="text" class="form-control" placeholder="Rechercher client" disabled id="input_recherche" onkeyup="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />
                            </div>
                            <div id="periode_rechereche" style="display:none;">
                                <input type="date" class="form-control w-50" placeholder="Rechercher client" id="date1" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />
                                <input type="date" class="form-control w-50" placeholder="Rechercher client" id="date2" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />
                            </div>
                            <div id="type_rechereche" style="display:none;" class="text-center pl-3">
                                <input type="radio" class="form-control-sm" name="type" value="toi" id="toi_find" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />&nbsp;&nbsp;
                                <label for="toi_find" class="mt-1">Client</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="form-control-sm" name="type" value="moi" id="moi_find" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />&nbsp;&nbsp;
                                <label for="moi_find" class="mt-1">moi</label>
                            </div>
                        </div>
                        @endunless
                        <div class="col-md-4 p-1 text-center ">
                            <a href="#changeTypeDeviseModal" data-bs-toggle="modal" class="btn btn-warning btn-circle" title="Modifier type" style="background: linear-gradient(to right, #FFA500, #FFD700);">
                                <i class="bi bi-arrow-clockwise"></i>
                                Devise ( {{$devise->symbol}} )
                            </a>
                            @unless ((array_key_exists('operation', $items) && in_array("ajouter", $items["operation"])))
                            <a href="#addOperationModal" data-bs-toggle="modal" class="btn btn-primary btn-circle" title="Ajouter" style="background: linear-gradient(to right, #001F3F, #0099FF);">
                                <i class="bi bi-moi"></i>
                                Nouvelle
                            </a>
                            @endunless
                            @unless ((array_key_exists('operation', $items) && in_array("exporter", $items["operation"])))
                            <a href="#exportModal" data-bs-toggle="modal" class="btn btn-danger btn-circle" title="exporter">
                                <i class="bi bi-box-arrow-up"></i>
                                Exporter
                            </a>
                            @endunless
                        </div>

                        <!-- Add this modal at the end of the body -->
                        <div id="exportModal" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Export Options</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Choose export format:</p>

                                        <div class="d-flex">

                                            <a href="{{ route('operation.pdf') }}" class="btn btn-danger" value="1"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z" />
                                                </svg> Export PDF</a>


                                            <a href="{{ route('operation.excel') }}" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-xlsx" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM7.86 14.841a1.13 1.13 0 0 0 .401.823c.13.108.29.192.479.252.19.061.411.091.665.091.338 0 .624-.053.858-.158.237-.105.416-.252.54-.44a1.17 1.17 0 0 0 .187-.656c0-.224-.045-.41-.135-.56a1.002 1.002 0 0 0-.375-.357 2.028 2.028 0 0 0-.565-.21l-.621-.144a.97.97 0 0 1-.405-.176.37.37 0 0 1-.143-.299c0-.156.061-.284.184-.384.125-.101.296-.152.513-.152.143 0 .266.023.37.068a.624.624 0 0 1 .245.181.56.56 0 0 1 .12.258h.75a1.093 1.093 0 0 0-.199-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.552.05-.777.15-.224.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.123.524.082.149.199.27.351.367.153.095.332.167.54.213l.618.144c.207.049.36.113.462.193a.387.387 0 0 1 .153.326.512.512 0 0 1-.085.29.558.558 0 0 1-.255.193c-.111.047-.25.07-.413.07-.117 0-.224-.013-.32-.04a.837.837 0 0 1-.249-.115.578.578 0 0 1-.255-.384h-.764Zm-3.726-2.909h.893l-1.274 2.007 1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415H1.5l1.24-2.016-1.228-1.983h.931l.832 1.438h.036l.823-1.438Zm1.923 3.325h1.697v.674H5.266v-3.999h.791v3.325m7.636-3.325h.893l-1.274 2.007 1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415h-.861l1.24-2.016-1.228-1.983h.931l.832 1.438h.036l.823-1.438Z" />
                                                </svg> Export Excel</a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <nav class="navbar navbar-expand-lg  mt-2">
                            <div class="container-fluid">
                                <div class="navbar-nav" style="width: 100%;">
                                    <a href="{{ route('transfert.index',[$client->username,$devise->symbol]) }}" class="nav-link m-1 nav-button text-info text-decoration-underline  fw-bold " type="button">Transfert entre les clients</a>
                                    <a href="{{ route('converts.index',[$client->username,$devise->symbol]) }}" class="nav-link m-1 nav-button text-danger text-decoration-underline  fw-bold " type="button">Convertir entre les devises</a>
                                    <a href="{{ route('deposes.index',[$client->username,$devise->symbol]) }}" class="nav-link m-1 nav-button text-primary text-decoration-underline  fw-bold " type="button">Déposer en Stock</a>
                                </div>
                            </div>
                        </nav> -->


                        <nav class="navbar navbar-expand-lg mt-2">
                            <div class="container-fluid">
                                <div class="navbar-nav" style="width: 100%;">
                                    <select class="form-select" onchange="window.location.href=this.value;">
                                        <option value="{{ route('operation.index',[$client->username,$devise->symbol]) }}">operation avec les clients</option>
                                        <option value="{{ route('transfert.index',[$client->username,$devise->symbol]) }}">Transfert entre les clients</option>
                                        <option value="{{ route('converts.index',[$client->username,$devise->symbol]) }}">Convertir entre les devises</option>
                                        <option value="{{ route('deposes.index',[$client->username,$devise->symbol]) }}">Déposer en Stock</option>
                                    </select>
                                </div>
                            </div>
                        </nav>

                        <div class=" container-fluid">
                            <div class="row">
                                <div class="table-responsive">
                                    @if ((array_key_exists('operation', $items) && in_array("affichage", $items["operation"])))
                                    <div class="alert alert-danger text-center fw-bold p-3 text-light"> Désolé, vous n'êtes pas autorisé à accéder à affichage.</div>
                                    @else
                                    <table class="text-dark" id="clientTable">
                                        <thead>
                                            <tr>
                                                <th colspan="3">Client</th>
                                                <th colspan="2">- <span id="statistique_toi">{{number_format($statistique_toi[0], 2)}} {{$devise->symbol}}</span></th>
                                                <th>- <span id="statistique_toi_base">{{number_format($statistique_toi[0] * $devise->base, 2)}} {{$entreprise->base_devise}}</span></th>
                                                <th rowspan="3" colspan="3" width="25%" id="graphique">
                                                    <canvas id="pieChart"></canvas>
                                                    <script>
                                                        var data = {
                                                            labels: [
                                                                '{{$statistique_moi[1]}}' + " moi",
                                                                '{{$statistique_toi[1]}}' + " toi"
                                                            ],
                                                            datasets: [{
                                                                data: ['{{$statistique_moi[1]}}', '{{$statistique_toi[1]}}'],
                                                                backgroundColor: ['#64daa5', '#FF8F8F'],
                                                                borderColor: 'black',
                                                                borderWidth: 1
                                                            }]
                                                        };
                                                        var options = {
                                                            responsive: true
                                                        };
                                                        var ctx = document.getElementById('pieChart').getContext('2d');
                                                        var myPieChart = new Chart(ctx, {
                                                            type: 'pie',
                                                            data: data,
                                                            options: options
                                                        });
                                                    </script>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">moi</th>
                                                <th colspan="2">+ <span id="statistique_moi">{{number_format($statistique_moi[0], 2)}} {{$devise->symbol}}</span></th>
                                                <th>+ <span id="statistique_moi_base">{{number_format($statistique_moi[0] * $devise->base, 2)}} {{$entreprise->base_devise}}</span></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3">Total</th>
                                                <th colspan="2"><span id="statistique_total">{{number_format($statistique_moi[0] - $statistique_toi[0] , 2)}} {{$devise->symbol}}</span></th>
                                                <th><span id="statistique_total_base">{{number_format(($statistique_moi[0] - $statistique_toi[0]) * $devise->base , 2)}} {{$entreprise->base_devise}}</span></th>

                                            </tr>
                                            <tr class=" text-nowrap">
                                                <th><input type="checkbox" name="toutoperation" id="toutoperation" onchange="toggleCheckboxes()" /> (<span id="nb_check">0</span>) <a href="#historiquesOperationModal" onclick="archiver_operation()" data-bs-toggle="modal" class="btn btn-danger btn-circle" title="exporter"> <i class="bi bi-archive"></i></a></th>
                                                <th>Date</th>
                                                <th>Ville</th>
                                                <th>Quantité</th>
                                                <th>Prix</th>
                                                <th>Commentaire</th>
                                                <th>%</th>
                                                <th>Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="informations text-nowrap">
                                            @if (count($operations) > 0)
                                            @foreach ($operations as $op)

                                            @if (session()->has('hover') && in_array($op->id, session('hover')))
                                            <tr style="background-color: cyan;" class=" text-nowrap">
                                                @elseif ($op->type_operation == 'moi' )
                                            <tr  class=" text-nowrap element-primary">
                                                @else
                                            <tr  class=" text-nowrap element-secondary">
                                                @endif

                                                <td><input type="checkbox" value="{{ $op->id}}***{{$op->type_operation}}***{{$op->total}}" name="{{ $op->id}}" id="{{ $op->id}}" onchange="check_operation(this,'{{$op->id}}','{{$op->type_operation}}','{{$op->total}}')" class="check_historique" /></td>
                                                <td>{{ $op->date ?? '-' }}</td>
                                                <td>{{ $op->ville ?? '-' }}</td>
                                                <td>{{ $op->quantity ?? '-' }}</td>
                                                <td>{{ $op->prix ?? '-' }}</td>
                                                <td>{{ $op->comments ?? '-' }}</td>
                                                <td>{{ $op->percentage ?? '-' }}</td>
                                                <td>{{ number_format($op->total, 2) ?? '-' }}</td>  
                                                <td>
                                                    <a class="btn btn-danger" href="#deleteOperationModal" data-bs-toggle="modal" onclick="Information_Delete('{{$op->id}}')"><i class="bi bi-trash"></i></a>
                                                    <a class="btn btn-warning" href="#updateOperationModal" data-bs-toggle="modal" onclick="Informations_Update('{{$op->id}}','{{$op->date}}','{{$op->ville}}','{{$op->quantity}}','{{$op->prix}}','{{$op->type_operation}}','{{$op->comments}}','{{$op->total}}','{{$op->percentage}}')"><i class="bi bi-pencil"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="8">
                                                    <div class="alert fw-bold text-danger">Aucun Opération a cette client avec cette devise !</div>
                                                </td>
                                            </tr>

                                            @endif

                                        </tbody>
                                    </table>
                                    @endif
                                    @php
                                    Session::forget('hover');
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="addOperationModal" tabindex="-1" aria-labelledby="addOperationModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('operation.add') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addOperationModalLabel">Ajouter Opération</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="client" value="{{ $client->username }}" />
                                        <input type="hidden" name="devise" value="{{ $devise->symbol }}" />
                                        <div class="form-group">
                                            <label class="label">Date d'opération :</label>
                                            <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" />
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Localisation :</label>
                                            <input type="text" name="ville" id="ville" class="form-control" list="villes" />
                                            <datalist id="villes">
                                                @foreach($villes as $ville)
                                                <option value="{{ $ville->ville }}">
                                                    @endforeach
                                            </datalist>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Quantité :</label>
                                            <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="quantite" value="0" id="quantite" class="form-control" onchange="CalculePrixQuantite('quantite','prix','operation1add','operation2add','porcentage','total')" />
                                        </div>

                                        <div class="form-group">
                                            <label class="label">Prix :</label>
                                            <div class="d-flex">
                                                <select class="form-control w-25" id="operation1add" onchange="CalculePrixQuantite('quantite','prix','operation1add','operation2add','porcentage','total')">
                                                    <option value="+">+</option>
                                                    <option value="-">-</option>
                                                    <option value="*">x</option>
                                                    <option value="/">/</option>
                                                </select>
                                                <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="prix" id="prix" value="0" class="form-control" onchange="CalculePrixQuantite('quantite','prix','operation1add','operation2add','porcentage','total')" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Total :</label>
                                            <div class="d-flex">
                                                <input type="number" name="total" readonly value="0" id="total" class="form-control" style="width:60%" />
                                                <select class="form-control" style="width:15%" id="operation2add" name="operation2add" onchange="CalculePrixQuantite('quantite','prix','operation1add','operation2add','porcentage','total')">
                                                    <option value="+">+</option>
                                                    <option value="-">-</option>
                                                    <option value="*">x</option>
                                                    <option value="/">/</option>
                                                </select>
                                                <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="porcentage" value="0" id="porcentage" class="form-control" style="width:15%" onchange="CalculePrixQuantite('quantite','prix','operation1add','operation2add','porcentage','total')" />
                                                <span style="width:10%;padding:5px">%</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="label">Type d'opération :</label>
                                            <span class="d-flex pl-5">
                                                <input type="radio" class="form-control-sm" name="type" value="moi" id="moi" checked />&nbsp;&nbsp;
                                                <label for="moi" class="mt-1">moi</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="radio" class="form-control-sm" name="type" value="toi" id="toi" />&nbsp;&nbsp;
                                                <label for="toi" class="mt-1">Client</label>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Commentaire :</label>
                                            <textarea name="commentaire" id="commentaire" cols="3" class="form-control"></textarea>
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

                    <div id="updateOperationModal" class="modal fade">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('operation.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h4 class="modal-title">Modifier Opération :</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    @unless ((array_key_exists('operation', $items) && in_array("modifier", $items["operation"])))
                                    <div class=" modal-body">

                                        <input type="hidden" name="client" value="{{ $client->username }}" />
                                        <input type="hidden" name="devise" value="{{ $devise->symbol }}" />
                                        <input type="hidden" name="id" id="id_update" class="form-control update" />
                                        <div class="form-group">
                                            <label class="label">Date d'opération :</label>
                                            <input type="date" name="date" id="date_update" class="form-control update" />
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Localisation :</label>
                                            <input type="text" name="ville" id="ville_update" class="form-control update" list="villes" />
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Quantité :</label>
                                            <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="quantite" id="quantite_update" class="form-control update" onchange="CalculePrixQuantite('quantite_update','prix_update','operation1update','operation2update','porcentage_update','total_update')" />
                                        </div>


                                        <div class="form-group">
                                            <label class="label">Prix :</label>
                                            <div class="d-flex">
                                                <select class="form-control w-25" id="operation1update" onchange="CalculePrixQuantite('quantite_update','prix_update','operation1update','operation2update','porcentage_update','total_update')">
                                                    <option value="+">+</option>
                                                    <option value="-">-</option>
                                                    <option value="*">x</option>
                                                    <option value="/">/</option>
                                                </select>
                                                <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="prix" id="prix_update" class="form-control w-75 update" onchange="CalculePrixQuantite('quantite_update','prix_update','operation1update','operation2update','porcentage_update','total_update')" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Total :</label>

                                            <div class="d-flex">
                                                <input type="number" name="total" id="total_update" style="width:60%" readonly class="form-control update" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" />
                                                <select class="form-control update" style="width:15%" id="operation2update" name="operation2update" onchange="CalculePrixQuantite('quantite_update','prix_update','operation1update','operation2update','porcentage_update','total_update')">
                                                    <option value="+">+</option>
                                                    <option value="-">-</option>
                                                    <option value="*">x</option>
                                                    <option value="/">/</option>
                                                </select>
                                                <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="porcentage" value="0" id="porcentage_update" class="form-control update" style="width:15%" onchange="CalculePrixQuantite('quantite_update','prix_update','operation1update','operation2update','porcentage_update','total_update')" />
                                                <span style="width:10%;padding:5px">%</span>
                                            </div>
                                        </div>
                                        <div class="form-group">

                                            <label class="label">Type d'opération :</label>
                                            <span class="d-flex pl-5">
                                                <input type="radio" class="form-control-sm" name="type" value="toi" id="toi_update" />&nbsp;&nbsp;
                                                <label for="toi_update" class="mt-1">Client</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="radio" class="form-control-sm" name="type" value="moi" id="moi_update" />&nbsp;&nbsp;
                                                <label for="moi_update" class="mt-1">moi</label>
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <label class="label">Commentaire :</label>
                                            <textarea name="commentaire" id="commentaire_update" cols="3" class="form-control update"></textarea>
                                        </div>
                                    </div>
                                    @endunless
                                    <div class="modal-footer">
                                        @if ((array_key_exists('operation', $items) && in_array("modifier", $items["operation"])))
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
                    <div class="modal fade" id="deleteOperationModal" tabindex="-1" aria-labelledby="deleteOperationModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('operation.delete') }}">
                                    @csrf
                                    @method('delete')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteOperationModalLabel">Supprimer Opération</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    @unless ((array_key_exists('operation', $items) && in_array("supprimer", $items["operation"])))
                                    <div class="modal-body">
                                        <input type="hidden" name="client" value="{{ $client->username }}" />
                                        <input type="hidden" name="devise" value="{{ $devise->symbol }}" />
                                        <input type="hidden" class="form-control" id="id_delete" name="operation">
                                        <div class="form-group">
                                            <label class="form-label">Mot de passe :</label>
                                            <input type="password" class="form-control" id="password" name="password" required />
                                        </div>
                                        <br>
                                        <p>Êtes-vous sûr de vouloir supprimer cette opération?</p>
                                        <p class="text-warning"><small>Cette action ne peut pas être annulée.</small></p>

                                    </div>
                                    @endunless
                                    <div class="modal-footer">
                                        @if ((array_key_exists('operation', $items) && in_array("supprimer", $items["operation"])))
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

                    <div class="modal fade" id="changeTypeDeviseModal" tabindex="-1" aria-labelledby="changeTypeDeviseModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('clients.operation') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title">Choisir Devise</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" class="form-control" id="client" name="client" value="{{$client->username}}">
                                        <input type="hidden" class="form-control" id="page" name="page" value="operation">

                                        @if(count($devises) > 1)
                                        <div class="form-group">
                                            <label>Type Devise</label>
                                            <select class="form-control" name="typedevise" id="typedevise">
                                                @foreach($devises as $dv)
                                                @if($dv->symbol != $devise->symbol)
                                                <option value="{{$dv->symbol}}">{{$dv->symbol}}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                            <div class="form-group p-2">
                                                <div id="tout"></div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group text-center">
                                            <input type="text" class="form-control alert-danger text-danger font-weight-bol" value="Aucun type devise" />
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">

                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        @if(count($devises) > 1)
                                        <button type="submit" class="btn btn-warning">Change</button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="historiquesOperationModal" tabindex="-1" aria-labelledby="historiquesOperationModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="historiquesOperationModalLabel">Archiver</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless ((array_key_exists('operation', $items) && in_array("archiver", $items["operation"])))
                                <div class="modal-body">

                                    <div class="d-flex">
                                        <input type="radio" id="newHistory" name="historyOption" class="radio-btn" checked onchange="toggleDivs()">
                                        <label for="newHistory" class="btn w-50">Nouvelle Historique</label>

                                        <input type="radio" id="existingHistory" name="historyOption" class="radio-btn" onchange="toggleDivs()" @if(count($historiques)==0) disabled @endif>
                                        <label for="existingHistory" class="btn w-50">Historique existe ({{count($historiques)}})</label>
                                    </div>
                                    <input type="hidden" name="client" id="client_historique" value="{{$client->username}}" />
                                    <input type="hidden" name="devise" id="devise_historique" value="{{ $devise->symbol }}" />
                                    <div id="nouvelle">
                                        <div class="form-group">
                                            <label class="label">Date historique :</label>
                                            <input type="date" name="date" id="date_historique" cols="3" class="form-control" value="{{ date('Y-m-d') }}" />
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Commentaire :</label>
                                            <textarea name="commentaire" id="commentaire_historique" cols="3" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Total terminé :</label>
                                            <input type="text" name="solde" id="solde_historique" class="form-control" />
                                        </div>
                                    </div>
                                    <div id="existe" class="d-none">

                                        <div class="form-group">
                                            <label class="label">Total terminé :</label>
                                            <input type="text" id="solde_historique_exist" class="form-control" />
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <th></th>
                                                <th>date historique</th>
                                                <th>commentaire</th>
                                                <th>Total terminé</th>
                                            </thead>
                                            <tbody>
                                                @foreach($historiques as $hist)
                                                <tr>
                                                    <td> <input type="radio" name='old_historique' id="{{$hist->id}}" value="{{$hist->id}}" /> </td>
                                                    <td><label for="{{$hist->id}}">{{$hist->datehistorique}}</label></td>
                                                    <td><label for="{{$hist->id}}">{{$hist->commentaire}}</label></td>
                                                    <td><label for="{{$hist->id}}">{{$hist->valeur}}</label></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if ((array_key_exists('operation', $items) && in_array("archiver", $items["operation"])))
                                    <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                    @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger" id="btn_historique" onclick="addhistorique()" disabled>Archiver</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    @include('layout.footer')

    <script src="{{ asset('/assets/js/core/jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/now-ui-dashboard.min.js') }}" defer></script>

</body>

</html>