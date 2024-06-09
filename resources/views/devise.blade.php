<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('/js/devise.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <style>
        .highlight {
            background-color: yellow !important;
            /* Change this color as needed */
        }
    </style>
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
                                    <div class="col-md-3 h5 card-title text-center" style="color: blueviolet; "> Les
                                        Devise</div>
                                    <div class="col-md-4">

                                    </div>
                                    <div class="col-md-5 text-center ">

                                        @unless (array_key_exists('devise', $items) && in_array("ajouter", $items["devise"]))
                                        <a href="#adddevise" data-bs-toggle="modal" class="btn btn-primary"><i class="bi bi-plus"></i> Nouveau</a>
                                        @endunless
                                        @unless (array_key_exists('devise', $items) && in_array("exporter", $items["devise"]))
                                        <a href="#exportdevise" data-bs-toggle="modal" class="btn btn-danger"><i class="bi bi-file-earmark-arrow-down"></i> Exporter</a>
                                        @endunless
                                        @unless (array_key_exists('devise', $items) && in_array("base", $items["devise"]))
                                        <button type="button" class="btn btn-primary m-1" data-bs-toggle="modal" data-bs-target="#myModal">
                                            Choisir une devise de base
                                        </button>
                                        @endunless

                                    </div>
                                </div>
                                <div class="row" style="margin: 0px 25px;">
                                    @if (session('success'))
                                    <div class="alert alert-info alert-dismissible fade show" role="alert" id="autoCloseAlert">

                                        {{ session('success') }}
                                        <button type="button" class="close btn btn-info" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    @endif
                                    @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="autoCloseAlert">

                                        {{ session('error') }}
                                        <button type="button" class="close btn btn-danger" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div>
                                        @endif



                                        <!-- Modal -->
                                        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Select Symbole</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    @unless (array_key_exists('devise', $items) && in_array("base", $items["devise"]))
                                                    <div class="modal-body">
                                                        <select name="symbol" id="symbolSelect" class="form-select">
                                                            <option value="choisir un devise">choisir un devise</option>

                                                            @foreach($symbols as $symbol)
                                                            <option value="{{ $symbol }}">{{ $symbol }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @endunless
                                                    <div class="modal-footer">
                                                        @if (array_key_exists('devise', $items) && in_array("base", $items["devise"]))
                                                        <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                                                        @else
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body" id="table_info">

                            <div class="table-responsive table-border">
                                <table class="table table-bordered  text-center ">
                                    <thead class="text-nowrap">
                                        <tr>
                                            <th>Symbole</th>
                                            <th>Description</th>
                                            <th>Taux de Base ({{ $entreprise->base_devise }})</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id='resultats '>

                                        @if (array_key_exists('devise', $items) && in_array("affichage", $items["devise"]))
                                        <tr>
                                            <td colspan="4" class="alert alert-danger text-center fw-bold p-3 text-light">
                                                Désolé, vous n'êtes pas autorisé à accéder à affichage.</td>
                                        </tr>
                                        @else
                                        @if (count($devise) > 0)
                                        @foreach ($devise as $devise)
                                        @php
                                        if (session()->has('hover') && in_array($devise->symbol, session('hover')))
                                        $bg ="bg-info";
                                        else
                                        $bg = "";
                                        @endphp
                                        <tr>
                                            <td class="{{$bg}}">1 {{ $devise->symbol }}</td>
                                            <td class="{{$bg}}">{{ $devise->description }}</td>
                                            <td class="{{$bg}}">{{ number_format($devise->base,2) }} {{ $entreprise->base_devise }}</td>
                                            <td class="{{$bg}}  d-flex justify-content-center">
                                                <a class="btn btn-danger my-1" href="#deletedevise" data-bs-toggle="modal" onclick="info('symbol_delete','{{ $devise->symbol }}')"><i class="bi bi-trash"></i></a>
                                                <a class="btn btn-warning my-1" href="#updatedevise" data-bs-toggle="modal" onclick="info_update('{{ $devise->symbol }}','{{ $devise->description }}','{{ $devise->base }}')"><i class="bi bi-pencil"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4" class="alert alert-danger text-center fw-bold p-3 text-light">
                                                N'est aucun Devise</td>
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
        <div class="modal fade" id="adddevise" tabindex="-1" aria-labelledby="adddeviseLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('devise.add') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="adddeviseLabel">Ajouter Devise</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        @unless (array_key_exists('devise', $items) && in_array("ajouter", $items["devise"]))
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">Symbole :</label>
                                <input type="text" class="form-control" id="symbol_add" name="symbol" required />
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description :</label>
                                <textarea name="description" id="description_add" class="form-control" cols="30" rows="4"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Taux de Base :</label>
                                <input type="text" class="form-control" pattern="^\d+(\.\d+)?$" title="Veuillez entrer un nombre" id="base_add" name="base" />
                            </div>
                        </div>
                        @endunless
                        <div class="modal-footer">
                            @if (array_key_exists('devise', $items) && in_array("ajouter", $items["devise"]))
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
        <div class="modal fade" id="updatedevise" tabindex="-1" aria-labelledby="updatedeviseLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form method="post" action="{{ route('devise.update') }}">


                        @csrf
                        @method('put')
                        <div class="modal-header">
                            <h5 class="modal-title" id="updatedeviseLabel">Modifier Devise</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        @unless (array_key_exists('devise', $items) && in_array("modifier", $items["devise"]))
                        <div class="modal-body">
                            <!-- <div class="form-group">
                                        <label class="form-label">Symbol :</label> -->
                            <input type="text" class="form-control" id="symbol_update" name="symbol" />
                            <!-- </div> -->
                            <div class="form-group">
                                <label class="form-label">description :</label>
                                <textarea name="description" id="description_update" class="form-control" cols="30" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Taux de Base :</label>
                                <input type="text" class="form-control" pattern="^\d+(\.\d+)?$" title="Veuillez entrer un nombre" id="base_update" name="base" />
                            </div>
                        </div>
                        @endunless
                        <div class="modal-footer">
                            @if (array_key_exists('devise', $items) && in_array("modifier", $items["devise"]))
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
        <div class="modal fade" id="deletedevise" tabindex="-1" aria-labelledby="deletedeviseLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('devise.delete') }}">
                        @csrf
                        @method('delete')
                        <div class="modal-header">
                            <h5 class="modal-title" id="deletedeviseLabel">Supprimer Devise</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        @unless (array_key_exists('devise', $items) && in_array("supprimer", $items["devise"]))
                        <div class="modal-body">

                            <input type="hidden" class="form-control" id="symbol_delete" name="symbol" />
                            <div class="form-group">
                                <label class="form-label">Mot de passe :</label>
                                <input type="password" class="form-control" id="password" name="password" required />
                            </div>
                            <br>
                            <p class="text-danger">Tu as sure à supprimer cette Devise ?</p>

                        </div>
                        @endunless
                        <div class="modal-footer">
                            @if (array_key_exists('devise', $items) && in_array("supprimer", $items["devise"]))
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


        <div class="modal fade" id="exportdevise" tabindex="-1" aria-labelledby="exportdeviseLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportClientLabel">Exporter les devises</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if (array_key_exists('devise', $items) && in_array("exporter", $items["devise"]))
                        <div class="alert alert-danger">Désolé, vous n'êtes pas autorisé à accéder à cette action.</div>
                        @else
                        <div class="text-center">
                            <a href="{{ route('export.pdf') }}" class="btn btn-danger w-25"><i class="bi bi-file-pdf"></i> PDF</a>
                            <a href="{{ route('devise.excel') }}" class="btn btn-success w-25"><i class="bi bi-file-excel"></i> Excel</a>
                        </div>
                        @endif


                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('symbolSelect').addEventListener('change', function() {
                var symbol = this.value;
                fetch('/update-base-devise', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            symbol: symbol
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        // Handle success or display a success message
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
            document.getElementById('saveChangesBtn').addEventListener('click', function() {
                location.reload();
            });
        </script>
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