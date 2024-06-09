<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Historiques Stocks</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <script defer>
        document.addEventListener('DOMContentLoaded', function() {
            showRelevantSelect();
        });

        function showRelevantSelect() {
            var searchBy = document.getElementById('searchBy').value;
            document.getElementById('comptableSelect').style.display = 'none';
            document.getElementById('actionSelect').style.display = 'none';
            document.getElementById('pageSelect').style.display = 'none';

            if (searchBy === 'comptable') {
                document.getElementById('comptableSelect').style.display = 'block';
            } else if (searchBy === 'action') {
                document.getElementById('actionSelect').style.display = 'block';
            } else if (searchBy === 'page') {
                document.getElementById('pageSelect').style.display = 'block';
            }
        }
    </script>
    <style>
        .main-panel {
            flex: 1;

        }
    </style>
</head>

<body>
    <div class="wrapper">
        @include('layout.sidebar')
        <div class="main-panel" id="main-panel">
            @include('layout.navbar')
            <div class="panel-header panel-header-lg"></div>
            <div class="content">
                <div class="container" style="margin-top: 50px;">
                    <h5 style="color:blueviolet" class="italic">#Action Comptable</h5>
                    @if (session('status'))
                    <div class="alert alert-success text-white">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if (session('statusEror'))
                    <div class="alert alert-danger text-white">
                        {{ session('statusEror') }}
                    </div>
                    @endif

                    <form method="GET" action="{{ route('admin.pending-actions.index') }}" id="searchForm" class="mb-3">
                        <div class="row">
                            <div class="col-md-3 border border-bottom-info">
                                <select name="search_by" id="searchBy" class="form-control" onchange="showRelevantSelect()">
                                    <option value="">Choisier Type de Recherche</option>
                                    <option value="comptable" {{ request()->get('search_by') == 'comptable' ? 'selected' : '' }}>Comptable</option>
                                    <option value="action" {{ request()->get('search_by') == 'action' ? 'selected' : '' }}>Action</option>
                                    <option value="page" {{ request()->get('search_by') == 'page' ? 'selected' : '' }}>Page</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="comptableSelect" style="display: none;">
                                <select name="comptable" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select comptable</option>
                                    @foreach($comptables as $comptable)
                                    <option value="{{ $comptable }}" {{ request()->get('comptable') == $comptable ? 'selected' : '' }}>{{ $comptable }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" id="actionSelect" style="display: none;">
                                <select name="action" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select action</option>
                                    @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request()->get('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" id="pageSelect" style="display: none;">
                                <select name="page" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select page</option>
                                    @foreach($pages as $page)
                                    <option value="{{ $page }}" {{ request()->get('page') == $page ? 'selected' : '' }}>{{ $page }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-scroll">
                        <table class="table table-bordered table-hover shadow-sm text-center">
                            <thead class="table-success">
                                <tr>
                                    <th scope="col" class="text-black italic p-2">ID</th>
                                    <th scope="col" class="text-black italic p-2">COMPTABLE</th>
                                    <th scope="col" class="text-black italic p-2">ACTION</th>
                                    <th scope="col" class="text-black italic p-2">STATUS</th>
                                    <th scope="col" class="text-black italic p-2">PAGE</th>
                                    <th scope="col" class="text-black italic p-2">DETAILS</th>
                                    <th scope="col" class="text-black italic p-2">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($pendingActions) > 0)
                                @foreach ($pendingActions as $action)
                                <tr>
                                    <td>{{ $action->id }}</td>
                                    <td>{{ $action->comptable }}</td>
                                    <td>{{ $action->action }}</td>
                                    <td>{{ $action->status }}</td>
                                    <?php $details = json_decode($action->details, true); ?>
                                    <td>
                                        <form method="post" action="{{ route('admin.pending-actions.hover', $action->id) }}">
                                        @csrf
                                            <button> <i class="bi bi-box-arrow-in-down-right"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#detailsModal-{{ $action->id }}">
                                            View Details
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade" id="detailsModal-{{ $action->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-{{ $action->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="detailsModalLabel-{{ $action->id }}">Details Pour Action ID: {{ $action->id }}</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <pre>{{ json_encode(json_decode($action->details, true), JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-success" data-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-grid">
                                            <form action="{{ route('admin.pending-actions.approve', $action->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success mx-1 shadow-lg">
                                                    <i class="bi bi-check-lg text-light"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="d-grid">
                                            <form action="{{ route('admin.pending-actions.reject', $action->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger shadow-lg">
                                                    <i class="bi bi-x-lg text-light"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="7" class="bg-danger p-2 text-white bolder">Aucun Actions</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @include('layout.footer')
        </div>
    </div>
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('/assets/js/core/jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/core/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/now-ui-dashboard.min.js') }}" defer type="text/javascript"></script>
</body>

</html>