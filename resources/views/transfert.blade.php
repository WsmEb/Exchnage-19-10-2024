<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Transfert</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/highlights.css') }}">
    <script src="{{ asset('/assets/js/chart.min.js') }}"></script>
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <script>
        function checkDates() {
            var date1 = document.getElementById('date1').value;
            var date2 = document.getElementById('date2').value;

            if (date1 && date2) {
                if (date1 > date2) {
                    document.getElementById('date1').value = date2;
                }
            }
        }

        function ChangeRecharche(select, client, devise, base, devise_base) {
            let val = select.value;
            let periode_rechereche = document.getElementById("periode_rechereche");
            let type_rechereche = document.getElementById("type_rechereche");
            type_rechereche.style.display = "none";
            periode_rechereche.style.display = "none";
            find(client, devise, base, devise_base)
            if (val !== "choisir") {
                switch (val) {
                    case "date":
                        type_rechereche.style.display = "none";
                        periode_rechereche.style.display = "flex";
                        break;
                    case "type":
                        type_rechereche.style.display = "flex";
                        periode_rechereche.style.display = "none";
                        break;
                }
            } else {
                find(client, devise, base, devise_base)
            }
        }

        function find(client, devise, base, devise_base) {
            const collone_recherche = document.getElementById('collone_recherche');
            var x = new XMLHttpRequest()
            let url = `/transferts/`;
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
                        afficher(data[0], client)
                        document.getElementById("statistique_recepteur").innerHTML = data[1][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_expediteur").innerHTML = data[2][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_total").innerHTML = data[3].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_recepteur_base").innerHTML = (data[1][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_expediteur_base").innerHTML = (data[2][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_total_base").innerHTML = (data[3] * base).toFixed(2) + ` ${devise_base}`;
                        myPieChart.data.labels = [data[1][1] + " recepteur",
                                data[2][1] + " expéditeur"
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
                if (collone_recherche.value == "type") {
                    if (document.getElementById("recepteur_find").checked == true)
                        val = "recepteur";
                    else if (document.getElementById("expediteur_find").checked == true)
                        val = "expediteur";
                    else
                        val = "";
                }
                x.open('GET', url + `search/${client}/${devise}/${val}`, true);
                x.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        data = JSON.parse(this.responseText);
                        // console.log(data);
                        afficher(data[0], client)
                        document.getElementById("statistique_recepteur").innerHTML = data[1][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_expediteur").innerHTML = data[2][0].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_total").innerHTML = data[3].toFixed(2) + ` ${devise}`;
                        document.getElementById("statistique_recepteur_base").innerHTML = (data[1][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_expediteur_base").innerHTML = (data[2][0] * base).toFixed(2) + ` ${devise_base}`;
                        document.getElementById("statistique_total_base").innerHTML = (data[3] * base).toFixed(2) + ` ${devise_base}`;
                        myPieChart.data.labels = [data[1][1] + " recepteur",
                                data[2][1] + " expéditeur"
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

        function Informations_Update(id, date, expediteur, recepteur, solde, client) {
            // alert(type);
            let type = expediteur == client ? 'recepteur' : 'expediteur';
            let inputs = document.getElementsByClassName("update");
            inputs[0].value = id;
            inputs[1].value = date;
            inputs[2].value = expediteur == client ? recepteur : expediteur;
            inputs[3].value = solde;
            document.getElementById(type + "_update").checked = true;
        }

        function Information_Delete(id) {
            document.getElementById("id_delete").value = id;
        }
        setTimeout(function() {
            document.querySelector('.message').style.display = 'none';
        }, 5000);

        function afficher(tab, client) {

            document.getElementById("informations").innerHTML = "";
            if (tab.length != 0) {
                for (const i in tab) {
                    bgcolor = tab[i][2] == client ? "rgba(0, 128, 0, 0.7)" : "rgba(255, 0, 0, 0.8)";

                    ligne = "<tr style='background-color: " + bgcolor + ";'>";

                    if (transferts_check.includes(parseInt(tab[i][0])))
                        ligne += ` <td><input type="checkbox" checked value="${tab[i][0]}***${tab[i][2] == client ? "expediteur" : "recepteur"}***${tab[i][4]}" name="${tab[i][0]}" id="${tab[i][0]}" onchange="check_operation(this,'${tab[i][0]}','${tab[i][2] == client ? "expediteur" : "recepteur"}','${tab[i][4]}')" class="check_historique" /></td>`;
                    else
                        ligne += ` <td><input type="checkbox" value="${tab[i][0]}***${tab[i][2] == client ? "expediteur" : "recepteur"}***${tab[i][4]}" name="${tab[i][0]}" id="${tab[i][0]}" onchange="check_operation(this,'${tab[i][0]}','${tab[i][2] == client ? "expediteur" : "recepteur"}','${tab[i][4]}')" class="check_historique" /></td>`;
                    ligne += `<td>${tab[i][1] !== null ? tab[i][1] : "-"}</td>`;
                    ligne += `<td>${tab[i][2] == client ? tab[i][6] : "--"}</td>`;
                    ligne += `<td>${tab[i][3] == client ? tab[i][5] : "--"}</td>`;
                    ligne += `<td>${tab[i][4] !== null ? tab[i][4].toFixed(2) : "-"}</td>`;
                    ligne += `<td> 
               <a class="btn btn-danger" href="#deleteTransfertModal" data-bs-toggle="modal" onclick="Information_Delete('${tab[i][0]}')"><i class="bi bi-trash"></i></a>
                <a class="btn btn-warning" href="#updateTransfertModal" data-bs-toggle="modal" onclick="Informations_Update('${tab[i][0]}','${tab[i][1]}','${tab[i][3]}','${tab[i][2]}','${tab[i][4]}','${client}')"><i class="bi bi-pencil"></i></a>
            </td>`;
                    ligne += "</tr>";
                    document.getElementById("informations").innerHTML += ligne;
                }
            } else
                document.getElementById(
                    "informations"
                ).innerHTML = `<tr><td colspan="5" class="alert alert-danger text-center fw-bold p-3 text-light">Aucun Opération a cette client avec cette devise !</td></tr>`;

        }



        var transferts_check = [];
        var total_transferts = 0;

        function toggleDivs() {
            const newHistoryRadio = document.getElementById('newHistory');
            const nouvelleDiv = document.getElementById('nouvelle');
            const existeDiv = document.getElementById('existe');

            if (newHistoryRadio.checked) {
                document.getElementById("btn_historique").disabled = transferts_check.length > 0 ? false : true;
                nouvelleDiv.classList.remove('d-none');
                existeDiv.classList.add('d-none');
            } else {

                document.getElementById("btn_historique").disabled = false;
                nouvelleDiv.classList.add('d-none');
                existeDiv.classList.remove('d-none');
            }
        }



        function toggleCheckboxes() {
            const toutOperationCheckbox = document.getElementById('toutoperation');
            const checkboxes = document.querySelectorAll('.check_historique');
            total_transferts = 0;
            transferts_check = [];
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = toutOperationCheckbox.checked;
                let valeurs = checkbox.value.split('***');
                if (toutOperationCheckbox.checked)
                    check_operation(checkbox, valeurs[0], valeurs[1], valeurs[2]);
                else {
                    document.getElementById("nb_check").innerHTML = transferts_check.length;
                }
            });
        }

        function check_operation(check_input, id, type, solde) {
            // alert(id+" "+type+" "+solde);
            if (check_input.checked) {
                transferts_check.push(parseInt(id));
                total_transferts = type === "expediteur" ? total_transferts + parseFloat(solde) : total_transferts - parseFloat(solde);
            } else {
                const index = transferts_check.indexOf(parseInt(id));
                if (index > -1)
                    transferts_check.splice(index, 1);
                total_transferts = type === "expediteur" ? total_transferts - parseFloat(solde) : total_transferts + parseFloat(solde);
            }
            document.getElementById("nb_check").innerHTML = transferts_check.length;
        }

        function archiver_operation() {
            document.getElementById("btn_historique").disabled = transferts_check.length > 0 ? false : true;
            document.getElementById("solde_historique").value = document.getElementById("solde_historique_exist").value = total_transferts.toFixed(2);
            // alert(transferts_check);
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
                xhr.open('POST', '/historique/add-transferts', true);
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
                    transferts_check: transferts_check,
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
                xhr.open('POST', '/historique/addexiste-transferts', true);
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
                    transferts_check: transferts_check,
                    id_historique: selectedValue,
                    valeur: valeur.value,
                }));
            }
        }
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
                            <h5 class="font-weight-bold">Gestion Transfert Avec M.{{$client->nom}}</h5>
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
                        @unless ((array_key_exists('transfert', $items) && in_array("affichage", $items["transfert"])))

                        <div class="col-md-2 p-1">
                            <select class="form-control w-100 mr-2" onchange="ChangeRecharche(this,'{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" id="collone_recherche">
                                <option value="choisir">Choisir Recherche</option>
                                <option value="date">Date Transfert</option>
                                <option value="type">Type</option>
                                <!-- <option value="solde">Solde</option> -->
                            </select>
                        </div>
                        <div class="col-md-6 p-1">

                            <div id="type_rechereche" style="display:none;" class="text-center pl-3">
                                <input type="radio" class="form-control-sm" name="type" value="expediteur" id="expediteur_find" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />&nbsp;&nbsp;
                                <label for="expediteur_find" class="mt-1">Expéditeur</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="form-control-sm" name="type" value="recepteur" id="recepteur_find" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />&nbsp;&nbsp;
                                <label for="recepteur_find" class="mt-1">Recepteur</label>
                            </div>
                            <div id="periode_rechereche" style="display:none;">
                                <input type="date" class="form-control w-50" placeholder="Rechercher client" id="date1" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />
                                <input type="date" class="form-control w-50" placeholder="Rechercher client" id="date2" onchange="find('{{$client->username}}','{{$devise->symbol}}','{{$devise->base}}','{{$entreprise->base_devise}}')" />
                            </div>
                        </div>

                        @endunless
                        <div class="col-md-4 p-1 text-center ">
                            <a href="#changeTypeDeviseModal" data-bs-toggle="modal" class="btn btn-warning btn-circle" title="Modifier type" style="background: linear-gradient(to right, #FFA500, #FFD700);">
                                <i class="bi bi-arrow-clockwise"></i>
                                Devise ( {{$devise->symbol}} )
                            </a>


                            @unless ((array_key_exists('transfert', $items) && in_array("ajouter", $items["transfert"])))
                            <a href="#addTransfertModal" data-bs-toggle="modal" class="btn btn-primary btn-circle" title="Ajouter" style="background: linear-gradient(to right, #001F3F, #0099FF);">
                                <i class="bi bi-moi"></i>
                                Nouvelle
                            </a>

                            @endunless
                            @unless ((array_key_exists('transfert', $items) && in_array("exporter", $items["transfert"])))
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

                                            <a href="{{ route('transfert.pdf') }}" class="btn btn-danger" value="1"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z" />
                                                </svg> Export PDF</a>


                                            <a href="{{ route('transfert.excel') }}" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-xlsx" viewBox="0 0 16 16">
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
                                    <a href="{{ route('operation.index',[$client->username,$devise->symbol]) }}" class="nav-link m-1 nav-button text-info text-decoration-underline  fw-bold " type="button">Opérations avec client</a>
                                    <a href="{{ route('converts.index',[$client->username,$devise->symbol]) }}" class="nav-link m-1 nav-button text-danger text-decoration-underline  fw-bold " type="button">Convertir entre les devises</a>
                                    <a href="{{ route('deposes.index',[$client->username,$devise->symbol]) }}" class="nav-link m-1 nav-button text-primary text-decoration-underline  fw-bold " type="button">Déposer en Stock</a>
                                </div>
                            </div>
                        </nav> -->

                        <nav class="navbar navbar-expand-lg mt-2">
                            <div class="container-fluid">
                                <div class="navbar-nav" style="width: 100%;">
                                    <select class="form-select" onchange="window.location.href=this.value;">
                                        <option value="{{ route('transfert.index',[$client->username,$devise->symbol]) }}">Transfert entre les clients</option>
                                        <option value="{{ route('operation.index',[$client->username,$devise->symbol]) }}">operation avec les clients</option>
                                        <option value="{{ route('converts.index',[$client->username,$devise->symbol]) }}">Convertir entre les devises</option>
                                        <option value="{{ route('deposes.index',[$client->username,$devise->symbol]) }}">Déposer en Stock</option>
                                    </select>
                                </div>
                            </div>
                        </nav>
                        <div class=" container-fluid">
                            <div class="row">
                                <div class="table-responsive">
                                    @if ((array_key_exists('transfert', $items) && in_array("affichage", $items["transfert"])))
                                    <div class="alert alert-danger text-center fw-bold p-3 text-light"> Désolé, vous n'êtes pas autorisé à accéder à affichage.</div>
                                    @else
                                    <table class="text-dark" id="clientTable">
                                        <thead>
                                            <tr>
                                                <th>Recepteur</th>
                                                <th>+ <span id="statistique_recepteur">{{number_format($statistique_recepteur[0], 2)}} {{$devise->symbol}}</th>
                                                <th>+ <span id="statistique_recepteur_base">{{number_format($statistique_recepteur[0] * $devise->base, 2)}} {{$entreprise->base_devise}}</th>
                                                <th rowspan="3" colspan="3" width="25%" id="graphique">
                                                    <canvas id="pieChart"></canvas>
                                                    <script>
                                                        var data = {
                                                            labels: [
                                                                '{{$statistique_recepteur[1]}}' + " recepteur",
                                                                '{{$statistique_expediteur[1]}}' + " expéditeur"
                                                            ],
                                                            datasets: [{
                                                                data: ['{{$statistique_recepteur[1]}}', '{{$statistique_expediteur[1]}}'],
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
                                                <th>Expéditeur</th>
                                                <th>- <span id="statistique_expediteur">{{number_format($statistique_expediteur[0], 2)}} {{$devise->symbol}}</th>
                                                <th>- <span id="statistique_expediteur_base">{{number_format($statistique_expediteur[0] * $devise->base, 2)}} {{$entreprise->base_devise}}</th>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <th><span id="statistique_total">{{number_format($statistique_recepteur[0] - $statistique_expediteur[0] , 2)}} {{$devise->symbol}}</th>
                                                <th><span id="statistique_total_base">{{number_format(($statistique_recepteur[0] - $statistique_expediteur[0]) * $devise->base , 2)}} {{$entreprise->base_devise}}</th>

                                            </tr>
                                            <tr class=" text-nowrap">
                                                <th><input type="checkbox" name="toutoperation" id="toutoperation" onchange="toggleCheckboxes()" /> (<span id="nb_check">0</span>) <a href="#historiquesTransfertsModal" onclick="archiver_operation()" data-bs-toggle="modal" class="btn btn-danger btn-circle" title="exporter"> <i class="bi bi-archive"></i></a></th>

                                                <th>Date</th>
                                                <th>Expediteur</th>
                                                <th>Recepteur</th>
                                                <th>Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="informations">
                                            @if (count($transferts) > 0)
                                            @foreach ($transferts as $tr)
                                            @if (session()->has('hover') && in_array($tr->id, session('hover')))
                                            <tr style="background-color: cyan;" class=" text-nowrap">
                                                @elseif ($tr->recepteur == $client->username )
                                            <tr style="background-color: #64daa5;" class=" text-nowrap" >
                                                @else
                                            <tr style="background-color: #FF8F8F;" class=" text-nowrap">
                                                @endif
                                                <!-- <tr> -->
                                                <td><input type="checkbox" value="{{ $tr->id}}***{{$tr->expediteur != $client->username ? 'expediteur' : 'recepteur'}}***{{$tr->solde}}" name="{{ $tr->id}}" id="{{ $tr->id}}" onchange="check_operation(this,'{{$tr->id}}',`{{$tr->expediteur != $client->username ?  'expediteur' : 'recepteur'}}`,'{{$tr->solde}}')" class="check_historique" /></td>

                                                <td>{{$tr->date}}</td>
                                                <td>{{$tr->expediteur != $client->username ? $tr->info_expediteur->nom : "--"}}</td>
                                                <td>{{$tr->recepteur != $client->username ? $tr->info_recepteur->nom : "--"}}</td>
                                                <td>{{number_format($tr->solde,2)}}</td>
                                                <td>
                                                    <a class="btn btn-danger" href="#deleteTransfertModal" data-bs-toggle="modal" onclick="Information_Delete('{{$tr->id}}')"><i class="bi bi-trash"></i></a>
                                                    <a class="btn btn-warning" href="#updateTransfertModal" data-bs-toggle="modal" onclick="Informations_Update('{{$tr->id}}','{{$tr->date}}','{{$tr->expediteur}}','{{$tr->recepteur}}','{{$tr->solde}}','{{$client->username}}')"><i class="bi bi-pencil"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="5">
                                                    <div class="alert fw-bold text-danger">Aucun Transfert a cette client avec cette devise !</div>
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

                    <div class="modal fade" id="addTransfertModal" tabindex="-1" aria-labelledby="addTransfertModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('transfert.add') }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addTransfertModalLabel">Ajouter Transfert</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="client_principale" value="{{ $client->username }}" />
                                        <input type="hidden" name="devise" value="{{ $devise->symbol }}" />
                                        <div class="form-group">
                                            <label class="label">Date d'Transfert :</label>
                                            <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" />
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Clients :</label>
                                            <select name="client" id="client" class="form-control">
                                                @foreach ($clients as $cl)
                                                @if ($cl->username != $client->username)
                                                <option value="{{ $cl->username }}">{{ $cl->nom }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Type de client :</label>
                                            <span class="d-flex">
                                                <input type="radio" class="form-control-sm" name="type" value="recepteur" checked id="recepteur" />&nbsp;&nbsp;
                                                <label for="recepteur">Recepteur</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="radio" class="form-control-sm" name="type" value="expediteur" id="expediteur" />&nbsp;&nbsp;
                                                <label for="expediteur">Expéditeur</label>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Amount :</label>
                                            <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="solde" value="0" id="solde" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        @if (count($clients) >1)
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                                        @else
                                        <div class="alert alert-danger w-100 text-light">Aucun Client !!</div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="updateTransfertModal" class="modal fade">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('transfert.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h4 class="modal-title">Modifier Opération :</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    @unless ((array_key_exists('transfert', $items) && in_array("modifier", $items["transfert"])))
                                    <div class=" modal-body">

                                        <input type="hidden" name="client_principale" value="{{ $client->username }}" />
                                        <input type="hidden" name="devise" value="{{ $devise->symbol }}" />
                                        <input type="hidden" name="id" id="id_update" class="form-control update" />
                                        <div class="form-group">
                                            <label class="label">Date d'Transfert :</label>
                                            <input type="date" name="date" id="date_update" class="form-control update" />
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Clients :</label>
                                            <select name="client" id="client_update" class="form-control update">
                                                @foreach ($clients as $cl)
                                                @if ($cl->username != $client->username)
                                                <option value="{{ $cl->username }}">{{ $cl->nom }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Type de client :</label>
                                            <span class="d-flex">
                                                <input type="radio" class="form-control-sm" name="type" value="recepteur" checked id="recepteur_update" />&nbsp;&nbsp;
                                                <label for="recepteur_update">Recepteur</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="radio" class="form-control-sm" name="type" value="expediteur" id="expediteur_update" />&nbsp;&nbsp;
                                                <label for="expediteur_update">Expéditeur</label>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Amount :</label>
                                            <input type="text" pattern="\d+(\.\d+)?" title="Entrez un nombre entier ou un nombre flottant (utilisez '.' comme séparateur décimal)" name="solde" value="0" id="solde_update" class="form-control update" />
                                        </div>
                                    </div>
                                    @endunless
                                    <div class="modal-footer">
                                        @if ((array_key_exists('transfert', $items) && in_array("modifier", $items["transfert"])))
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
                    <div class="modal fade" id="deleteTransfertModal" tabindex="-1" aria-labelledby="deleteTransfertModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('transfert.delete') }}">
                                    @csrf
                                    @method('delete')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteTransfertModalLabel">Supprimer Transfert</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    @unless ((array_key_exists('transfert', $items) && in_array("supprimer", $items["transfert"])))

                                    <div class="modal-body">
                                        <input type="hidden" name="client_principale" value="{{ $client->username }}" />
                                        <input type="hidden" name="devise" value="{{ $devise->symbol }}" />
                                        <input type="hidden" class="form-control" id="id_delete" name="transfert">
                                        <div class="form-group">
                                            <label class="form-label">Mot de passe :</label>
                                            <input type="password" class="form-control" id="password" name="password" required />
                                        </div>
                                        <br>
                                        <p>Êtes-vous sûr de vouloir supprimer cette transfert?</p>
                                        <p class="text-warning"><small>Cette action ne peut pas être annulée.</small></p>
                                    </div>
                                    @endunless
                                    <div class="modal-footer">
                                        @if ((array_key_exists('transfert', $items) && in_array("supprimer", $items["transfert"])))
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
                                        <input type="hidden" class="form-control" id="page" name="page" value="transfert">
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

                    <div class="modal fade" id="historiquesTransfertsModal" tabindex="-1" aria-labelledby="historiquesTransfertsModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="historiquesTransfertsModalLabel">Archiver</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                @unless ((array_key_exists('transfert', $items) && in_array("archiver", $items["transfert"])))
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
                                    @if ((array_key_exists('transfert', $items) && in_array("archiver", $items["transfert"])))
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    @include('layout.footer')

    <script src="{{ asset('/assets/js/core/jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/now-ui-dashboard.min.js') }}" defer></script>

</body>

</html>