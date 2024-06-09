<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
    <script>
        function detailClient(client) {
            document.getElementById("username_client").value = client;
            let xhr = new XMLHttpRequest();
            xhr.open('GET', '/stock/detail/' + client, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        // console.log(response);
                        let resultats = document.getElementById('devises_amounts');
                        resultats.innerHTML = '';

                        let devises = response.devises;
                        let entreprise = response.entreprise;
                        let html = '';
                        devises.forEach(function(data) {
                            html += `<tr>`;
                            html += `<td>${data[0]}</td>`;
                            html += `<td  class="text-end">${data[2].toFixed(2)} </td>`;
                            html += `<td  class="text-end">${data[3].toFixed(2)} ${entreprise.base_devise} </td>`;
                            html += `</tr>`;
                        });
                        resultats.innerHTML = html;

                    } else {
                        console.error('Error fetching data:', xhr.status);
                    }
                }
            };
            document.getElementById("devises_amounts").innerHTML = "<tr><td colspan='3' class='text-center'><div class='loading'></div></td></tr>";

            xhr.send();
        }

        function find(inp) {
            let val = inp.value;
            let xhr = new XMLHttpRequest();
            xhr.open('GET', '/stock/recherche/' + val, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        // Parse the JSON response
                        let response = JSON.parse(xhr.responseText);
                        let groupedData = response.groupedData;
                        let entreprise = response.entreprise;

                        let resultats = document.getElementById('resultats');
                        resultats.innerHTML = '';

                        if (Object.keys(groupedData).length === 0) {
                            resultats.innerHTML = '<tr><td colspan="4" class="h3 p-3 text-danger">Aucune donnée trouvée</td></tr>';
                        } else {
                            let dataValues = Object.values(groupedData);
                            let html = '';
                            dataValues.forEach(function(data) {
                                html += `<tr>
                                    <td>${data.client}</td>
                                    <td>${data.nom}</td>
                                    <td>${(data.totalDifference).toFixed(2) } ${entreprise.base_devise}</td>
                                    <td>
                                        <a class="btn btn-warning" href="#TypeDeviseModal" onclick="detailClient('${data.client}')" data-bs-toggle="modal">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                 </tr>`;
                            });
                            resultats.innerHTML = html;
                        }
                    } else {
                        console.error('Error fetching data:', xhr.status);
                    }
                }
            };
            xhr.send();

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
                                    <div class="col-md-3 h5 card-title text-center" style="color: blueviolet; ">#STOCK</div>

                                    <div class="col-md-6">
                                        @unless (array_key_exists('stock', $items) && in_array("affichage", $items["stock"]))
                                        <div class="input-group">
                                            <input type="text" class="form-control" style="border:1px solid gray" placeholder="Rechercher..." onkeyup="find(this)">
                                        </div>

                                        @endunless
                                    </div>
                                    @unless (array_key_exists('stock', $items) && in_array("exporter", $items["stock"]))
                                    <div class="col-md-3 text-center ">
                                        <a href="#exportModal" data-bs-toggle="modal" class="btn btn-danger btn-circle" title="exporter">
                                            <i class="bi bi-box-arrow-up"></i>
                                            Exporter
                                        </a>
                                    </div>
                                    @endunless
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

                                                        <form action="{{route('stock.pdf')}}" method="get" class="w-50 text-center ">
                                                            <button name="operation" class="btn btn-danger" value="1"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16">
                                                                    <path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z" />
                                                                </svg> Export PDF</button>
                                                        </form>

                                                        <form action="{{route('stock.excel')}}" method="get" class="w-50 text-center">
                                                            <button name="export_operation" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filetype-xlsx" viewBox="0 0 16 16">
                                                                    <path fill-rule="evenodd" d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zM7.86 14.841a1.13 1.13 0 0 0 .401.823c.13.108.29.192.479.252.19.061.411.091.665.091.338 0 .624-.053.858-.158.237-.105.416-.252.54-.44a1.17 1.17 0 0 0 .187-.656c0-.224-.045-.41-.135-.56a1.002 1.002 0 0 0-.375-.357 2.028 2.028 0 0 0-.565-.21l-.621-.144a.97.97 0 0 1-.405-.176.37.37 0 0 1-.143-.299c0-.156.061-.284.184-.384.125-.101.296-.152.513-.152.143 0 .266.023.37.068a.624.624 0 0 1 .245.181.56.56 0 0 1 .12.258h.75a1.093 1.093 0 0 0-.199-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.552.05-.777.15-.224.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.123.524.082.149.199.27.351.367.153.095.332.167.54.213l.618.144c.207.049.36.113.462.193a.387.387 0 0 1 .153.326.512.512 0 0 1-.085.29.558.558 0 0 1-.255.193c-.111.047-.25.07-.413.07-.117 0-.224-.013-.32-.04a.837.837 0 0 1-.249-.115.578.578 0 0 1-.255-.384h-.764Zm-3.726-2.909h.893l-1.274 2.007 1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415H1.5l1.24-2.016-1.228-1.983h.931l.832 1.438h.036l.823-1.438Zm1.923 3.325h1.697v.674H5.266v-3.999h.791v3.325m7.636-3.325h.893l-1.274 2.007 1.254 1.992h-.908l-.85-1.415h-.035l-.853 1.415h-.861l1.24-2.016-1.228-1.983h.931l.832 1.438h.036l.823-1.438Z" />
                                                                </svg> Export Excel</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                            <thead class=" text-nowrap">
                                                <tr>
                                                    <th>Identifient client</th>
                                                    <th>Nom client</th>
                                                    <th>BALANCE </th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id='resultats text-nowrap'>
                                                @if (array_key_exists('stock', $items) && in_array("affichage", $items["stock"]))
                                                <tr>
                                                    <td colspan="4" class="alert alert-danger text-center fw-bold p-3 text-light">
                                                        Désolé, vous n'êtes pas autorisé à accéder à affichage.</td>
                                                </tr>
                                                @else
                                                @foreach($groupedData as $Data)
                                                <tr>
                                                    <td> {{ $Data['client'] }} </td>
                                                    <td> {{ $Data['nom'] }} </td>
                                                    <td> {{ number_format($Data['totalDifference'],2) }} {{ $entreprise->base_devise }} </td>
                                                    <td>
                                                        <a class="btn btn-warning" href="#TypeDeviseModal" data-bs-toggle="modal" onclick="detailClient(`{{ $Data['client'] }}`)"><i class="bi bi-eye"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
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
                                @unless (array_key_exists('stock', $items) && in_array("detail", $items["stock"]))
                                <div class="modal-body">
                                    <input type="hidden" class="form-control" id="username_client" name="client">
                                    <input type="hidden" class="form-control" id="page" name="page" value="deposes">

                                    <div class="form-group">
                                        <label>Type Devise</label>
                                        <select class="form-control" name="typedevise" id="typedevise">
                                            @foreach($devises as $devise)
                                            <option value="{{ $devise->symbol }}">{{ $devise->symbol }}</option>
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
                                                <th class="text-end">Balance</th>
                                                <th class="text-end">Base</th>
                                            </thead>
                                            <tbody id="devises_amounts">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endunless
                                <div class="modal-footer">
                                    @if (array_key_exists('stock', $items) && in_array("detail", $items["stock"]))
                                    <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                    @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-warning">Détail</button>
                                    @endif

                                </div>
                            </form>
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