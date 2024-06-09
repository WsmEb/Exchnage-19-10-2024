<?php
$deposer_json = json_encode($DeposerAction); 
$retrait_json = json_encode($RetraitAction); 
$convertes_json = json_encode($convertes); 
 $transferts_json = json_encode($transferts);
 $chartData_json = json_encode($chartData);
 $operations_json = json_encode($operationss); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Admin Dashbord</title>
  <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
  <link rel="stylesheet" href="{{asset('/assets/css/sb-admin-2.min.css')}}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
  <script src="{{ asset('/assets/js/chart.min.js') }}"></script>
  <script src="{{ asset('/js/deconnexion.js') }}"></script>
  <script defer >
        function detailClient(client,devise) {
        window.location.href = `/operations/${client}/${devise}`;
        }
        async function returnConvertedAmount(devise) {
            var devise = devise.value
            console.log(devise) 
            var url = `/converts/func/${devise}/${document.getElementById("select2").value}`
            const fetching = await fetch(url);
            const data = await fetching.json();

            document.getElementById("input2").value  = +document.getElementById("input1").value * data[0] / data[1] ;
        }
        function OnInput1Change() {
            returnConvertedAmount(document.getElementById("select1"))
        }

        function OnSelect2Change() {
            returnConvertedAmount(document.getElementById("select1"))
        }

       
  </script>


  <style>
</style>
</head>

</html>
<body class=" bg-white ">
    @include('layout.sidebar')
    <div class="main-panel" id="main-panel">
        @include('layout.navbar')
        <div class="panel-header panel-header-lg"></div>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800 my-2"></h1>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 p-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success  text-uppercase mb-1" >
                                                Total des Clients</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <i class="bi bi-people" style="font-size: 22px;color:#4e73df;"></i> {{$clients}}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 p-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold  text-uppercase mb-1"  style="color: #1cc88a;">
                                                Total DES DEVISES</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <i class="bi bi-currency-exchange" style="color: #1cc88a;font-size: 22px"></i>
                                                {{$devises}}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 p-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold  text-uppercase mb-1"  style="color: #36b9cc;">
                                                Total DES Employees</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <i class="bi bi-person-lines-fill" style="color: #36b9cc;font-size: 22px"></i>
                                                {{$comptables}}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 p-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Total des transactions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="25" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#FFD43B" d="M535 41c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l64 64c4.5 4.5 7 10.6 7 17s-2.5 12.5-7 17l-64 64c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l23-23L384 112c-13.3 0-24-10.7-24-24s10.7-24 24-24l174.1 0L535 41zM105 377l-23 23L256 400c13.3 0 24 10.7 24 24s-10.7 24-24 24L81.9 448l23 23c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L7 441c-4.5-4.5-7-10.6-7-17s2.5-12.5 7-17l64-64c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9zM96 64H337.9c-3.7 7.2-5.9 15.3-5.9 24c0 28.7 23.3 52 52 52l117.4 0c-4 17 .6 35.5 13.8 48.8c20.3 20.3 53.2 20.3 73.5 0L608 169.5V384c0 35.3-28.7 64-64 64H302.1c3.7-7.2 5.9-15.3 5.9-24c0-28.7-23.3-52-52-52l-117.4 0c4-17-.6-35.5-13.8-48.8c-20.3-20.3-53.2-20.3-73.5 0L32 342.5V128c0-35.3 28.7-64 64-64zm64 64H96v64c35.3 0 64-28.7 64-64zM544 320c-35.3 0-64 28.7-64 64h64V320zM320 352a96 96 0 1 0 0-192 96 96 0 1 0 0 192z"/></svg>
                                                {{$Total_transactions}}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold "  style="color: #448374">TOTAL DES OPERATIONS DANS CHAQUE MOIS</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold"  style="color: #448374">CHART DES OPERATIONS</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2">
                                            <i class="bi bi-circle-fill " style="color: #4e73df;"></i> DEPOSER
                                        </span>
                                        <span class="mr-2">
                                            <i class="bi bi-circle-fill" style="color: #1cc88a;"></i> RETRAIT
                                        </span>
                                        <span class="mr-2">
                                            <i class="bi bi-circle-fill" style="color: #36b9cc;"></i> CONVERTE
                                        </span>
                                        <span class="mr-2">
                                            <i class="bi bi-circle-fill" style="color: #850F8D;" ></i> TRANSFER
                                        </span>
                                        <span class="p-0 m-0">
                                            <i class="bi bi-circle-fill" style="color: #10439F;" ></i> OPERATION
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card border-left-danger shadow h-100 p-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-4 mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1" >
                                                DEVISE DE BASE</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <i class="bi bi-coin text-danger" ></i> ({{$entreprise->base_devise}})
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card border-left-secondary shadow h-100 p-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-12 mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1" >
                                                CLIENT avec plus d'opérations</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($MVPClient)
                                                <i class="bi bi-person-fill text-secondary" ></i>  {{$MVPClient->client}}  <span class=" font-weight-light" >#{{$MVPClient->property_count}} Operation</span>
                                                @else
                                                <i class="bi bi-person-fill text-secondary" ></i> -<span class=" font-weight-light" >#0 Operation</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4 mb-4">
                            <div class="card border-left-dark shadow h-100 p-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-12 mr-2">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1" >
                                                DEVISE avec plus d'opérations</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                @if($MVPDevise)
                                                <i class="bi bi-cash-coin text-dark" ></i> {{$MVPDevise->devise}}  <span class=" font-weight-light text-center" >#{{$MVPDevise->devise_count}}</span>
                                                @else
                                                <i class="bi bi-cash-coin text-dark" ></i>-<span class=" font-weight-light text-center" >#0</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row" style="padding: 0 10px">
                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">
                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold "  style="color: #448374">TABLE DES DEVISES</h6>
                                </div>
                                <div class="card-body col-12 text-center bold ">
                                   <table class="table table-striped  table-borderless" style="cursor: pointer;">
                                    <thead  class="border-left-success">
                                        <tr>
                                            <th>DEVISE</th>
                                            <th>PRIX EN DEVISE DE BASE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($Alldevises as $devise)
                                        <tr>
                                            <td  @if($devise->symbol == $entreprise->base_devise) class="border-left-danger" title="DEVISE DE BASE" @endif >{{$devise->symbol}}</td>
                                            <td >{{$devise->base}} {{$entreprise->base_devise}} </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                   </table>
                                </div>
                            </div>

                            <!-- Color System -->
                            <div class="row">
                                <div class="col-lg-12 col-md-12 mb-4">
                                    <div class="card bg-light text-dark shadow border-left-success">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold "  style="color: #1ec88b">RECENT DEPOSER ACTION</h6>
                                        </div>
                                        <div class="card-body text-center font-weight-bold">
                                            <div class="row ">
                                                <div class="col-3  ">
                                                    <label for="" class="  text-dark" >DATE</label>
                                                </div>
                                                <div class="col-3  ">
                                                    <label for="" class="  text-dark" >CLIENT</label>
                                                </div>
                                                <div class="col-3">
                                                    <label for="" class="  text-dark" >TYPE</label>
                                                </div>
                                                <div class="col-3">
                                                    <label for="" class="  text-dark" >MONTANT</label>
                                                </div>
                                            </div>
                                            <hr class=" w-100 bg-light text-light" >
                                            <div class="row">
                                                @if(count($lastDeposes) > 0)
                                                @foreach($lastDeposes as $lastDepose)
                                                @php
                                                    $bg = $lastDepose->type == "DEPOSER" ? "bg-success-subtle" : "bg-danger-subtle";
                                                @endphp
                                                    <div class='col-3 {{$bg}} mb-1' >
                                                        <label for="" class="  text-dark">  {{$lastDepose->date_depose}} </label>
                                                    </div>
                                                    <div class='col-3 {{$bg}} mb-1' >
                                                        <label for="" class="  text-dark">  {{$lastDepose->client}} </label>
                                                    </div>
                                                    <div class="col-3  {{$bg}} mb-1">
                                                        <label for=" "  class="  text-dark">  {{$lastDepose->type}} </label>
                                                    </div>
                                                    <div class="col-3  {{$bg}} mb-1">
                                                        <label for=" "  class="  text-dark">  {{$lastDepose->amount}} {{ $lastDepose->devise }}</label>
                                                    </div>
                                                @endforeach
                                                @else
                                                <div class="col-4">
                                                    <label for="" class="  text-dark">-</label>
                                                 </div>
                                                 <div class="col-4">
                                                    <label for=" "  class="  text-dark">-</label>
                                                 </div>
                                                 <div class="col-4">
                                                     <label for=" "  class="  text-dark">-</label>
                                                 </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-4">
                                    <div class="card bg-light border-left-success shadow">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold"  style="color: #1ec88b">RECENT TRANSFER ACTION</h6>
                                        </div>
                                        <div class="card-body text-center font-weight-bold">
                                            <div class="row ">
                                                <div class="col-3">
                                                    <label for="" class=" text-dark" >DATE</label>
                                                </div>
                                                <div class="col-3">
                                                    <label for="" class=" text-dark" >EXPIDITEUR</label>
                                                </div>
                                                <div class="col-3">
                                                    <label for="" class=" text-dark" >RECEPTEUR</label>
                                                </div>
                                                <div class="col-3">
                                                    <label for="" class=" text-dark" >MONTANT</label>
                                                </div>
                                            </div>
                                            <hr class=" w-100 bg-light text-light" >
                                            <div class="row">
                                                @if(count($lastTransfers) > 0)
                                                 @foreach($lastTransfers as $lastTransfer)
                                                 @php
                                                     $tranfBg = $lastTransfer->expediteur != $lastTransfer->client ? 'bg-success-subtle' : 'bg-danger-subtle';
                                                 @endphp
                                                    <div class="col-3  {{$tranfBg}} mb-1 ">
                                                        <label for="" class=" text-dark">  {{$lastTransfer->date}} </label>
                                                    </div>
                                                    <div class="col-3  {{$tranfBg}} mb-1 ">
                                                        <label for="" class=" text-dark">  {{$lastTransfer->expediteur}} </label>
                                                    </div>
                                                    <div class="col-3  {{$tranfBg}} mb-1 ">
                                                        <label for=" "  class=" text-dark ">  {{$lastTransfer->recepteur}} </label>
                                                    </div>
                                                    <div class="col-3  {{$tranfBg}} mb-1 ">
                                                        <label for=" "  class=" text-dark ">  {{$lastTransfer->solde}} {{$lastTransfer->devise}}</label> 
                                                    </div>
                                                 @endforeach
                                                @else
                                                    <div class="col-3">
                                                        <label for="" class=" text-dark">-</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <label for=" "  class=" text-dark">-</label>
                                                    </div>
                                                    <div class="col-3">
                                                        <label for=" "  class=" text-dark">-</label> 
                                                    </div>
                                                    <div class="col-3">
                                                        <label for=" "  class=" text-dark">-</label> 
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-4">
                                    <div class="card bg-light border-left-success shadow">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold"  style="color: #1ec88b">RECENT CONVERTER ACTION</h6>
                                        </div>
                                        <div class="card-body text-center font-weight-bold">
                                            <div class="row ">
                                                <div class="col-2">
                                                    <label for="" class=" text-dark" >DATE</label>
                                                </div>
                                                <div class="col-2">
                                                    <label for="" class=" text-dark" >CLIENT</label>
                                                </div>
                                                <div class="col-2">
                                                    <label for="" class=" text-dark" >DEVISE ORIGIN</label>
                                                </div>
                                                <div class="col-2">
                                                    <label for="" class=" text-dark" >DEVISE DESTINATION</label>
                                                </div>
                                                <div class="col-2">
                                                    <label for="" class=" text-dark" >MONTANT</label>
                                                </div>
                                                <div class="col-2">
                                                    <label for="" class=" text-dark" >MONTANT CONVERTE</label>
                                                </div>
                                            </div>
                                            <hr class=" w-100 bg-light text-light" >
                                            <div class="row">
                                                @if(count($lastConvertes) > 0)
                                                  @foreach($lastConvertes as $lastConverte)
                                                  @php
                                                     $convBg =  $lastConverte->devise == $lastConverte->convertedSymbol ? 'bg-primary-subtle' : 'bg-warning-subtle';
                                                  @endphp
                                                    <div class="col-2 {{$convBg}} mb-1">
                                                        <label for="" class=" text-dark">  {{$lastConverte->date}} </label>
                                                    </div>
                                                    <div class="col-2  {{$convBg}} mb-1">
                                                        <label for="" class=" text-dark">  {{$lastConverte->client_username}} </label>
                                                    </div>
                                                    <div class="col-2  {{$convBg}} mb-1">
                                                        <label for="" class=" text-dark">  {{$lastConverte->devise}} </label>
                                                    </div>
                                                    <div class="col-2  {{$convBg}} mb-1">
                                                        <label for=" "  class=" text-dark">  {{$lastConverte->convertedSymbol}} </label>
                                                    </div>
                                                    <div class="col-2  {{$convBg}} mb-1">
                                                        <label for=" "  class=" text-dark">  {{$lastConverte->amount}} {{$lastConverte->devise}}</label> 
                                                    </div>
                                                    <div class="col-2  {{$convBg}} mb-1">
                                                        <label for=" "  class=" text-dark">  {{number_format($lastConverte->amount * convertedSymbolBase($lastConverte->devise) / convertedSymbolBase($lastConverte->convertedSymbol),2) }}     {{$lastConverte->convertedSymbol}}</label> 
                                                    </div>
                                                  @endforeach
                                                @else
                                                    <div class="col-2">
                                                        <label for="" class=" text-dark">-</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <label for="" class=" text-dark">-</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <label for="" class=" text-dark">-</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <label for=" "  class=" text-dark">-</label>
                                                    </div>
                                                    <div class="col-2">
                                                        <label for=" "  class=" text-dark">-</label> 
                                                    </div>
                                                    <div class="col-2">
                                                        <label for=" "  class=" text-dark">-</label> 
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6 mb-4">

                            <!-- Illustrations -->
                            <div class="card shadow mb-4" >
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold " style="color: #448374">LISTS TOP 5 CLIENTS AVEC PLUS D'OPERATIONS</h6>
                                </div>
                                <div class="card-body">
                                    <div class="card-body col-12  bold ">
                                        <table class="table table-striped  table-borderless" style="cursor: pointer;">
                                         <thead  class="">
                                             <tr>
                                                 <th class=" text-center">client</th>
                                                 <th  class=" text-center">total des operations</th>
                                                 <th>view</th>
                                             </tr>
                                         </thead>
                                         <tbody>
                                             @foreach ($MVPClients as $mvp_client)
                                             <tr>
                                                 <td  class="border-left-primary text-center" >{{$mvp_client->client}}</td>
                                                 <td class=" text-center">{{$mvp_client->property_count}} </td>
                                                 <td>
                                                    <a class="btn btn-primary" href="#TypeDeviseModal" data-bs-toggle="modal" onclick="detailClient(`{{$mvp_client->client }}`,`{{$entreprise->base_devise}}`)"><i class="bi bi-eye"></i></a>
                                                 </td>
                                             </tr>
                                             @endforeach
                                         </tbody>
                                        </table>
                                     </div>
                                  </div>
                            </div>

                            <!-- Approach -->
                            <div class="col-lg-12 mb-4 p-0 m-0">
                                <div class="card bg-light border-left-success shadow">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold"  style="color: #1ec88b">RECENTE OPERATION ACTION</h6>
                                    </div>
                                    <div class="card-body text-center font-weight-bold">
                                        <div class="row ">
                                            <div class="col-3 ">
                                                <label for="" class=" text-dark" >DATE</label>
                                            </div>
                                            <div class="col-3 ">
                                                <label for="" class=" text-dark" >CLIENT</label>
                                            </div>
                                            <div class="col-3">
                                                <label for="" class=" text-dark" >TYPE OPERATION</label>
                                            </div>
                                            <div class="col-3">
                                                <label for="" class=" text-dark" >TOTAL</label>
                                            </div>
                                        </div>
                                        <hr class=" w-100 bg-light text-light" >
                                        <div class="row">
                                            @if(count($lastOperations) > 0)
                                             @foreach($lastOperations as $lastOperation)
                                             @php
                                                 $opBg = $lastOperation->type_operation == 'moi' ? "bg-success-subtle" : "bg-danger-subtle";
                                             @endphp
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for="" class=" text-dark">  {{$lastOperation->date}} </label>
                                                </div>
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for="" class=" text-dark">  {{$lastOperation->client}} </label>
                                                </div>
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for=" "  class=" text-dark">  {{$lastOperation->type_operation}} </label>
                                                </div>
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for=" "  class=" text-dark">  {{$lastOperation->total}} {{$lastOperation->devise}}  </label> 
                                                </div>
                                             @endforeach
                                            @else
                                                <div class="col-3">
                                                    <label for="" class=" text-dark">-</label>
                                                </div>
                                                <div class="col-3">
                                                    <label for=" "  class=" text-dark">-</label>
                                                </div>
                                                <div class="col-3">
                                                    <label for=" "  class=" text-dark">-</label> 
                                                </div>
                                                <div class="col-3">
                                                    <label for=" "  class=" text-dark">-</label> 
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3 p-0 m-0">
                                <div class="card  border-left-info  " >
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-info ">TEST DES DEVISES </h6>
                                    </div>
                                    <div class=" row form-group px-1">
                                        <div class="col-3">
                                            <input type="number" name="" id="input1" class=" form-control" value="0" onchange="OnInput1Change()" >
                                        </div>
                                        <div class="col-2">
                                            <select name="" id="select1" class=" form-select" onchange="returnConvertedAmount(this)">
                                                @foreach ($Alldevises as $devise)
                                                <option value="{{ $devise->symbol}}" >{{ $devise->symbol}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-1" style="font-size: 20px">
                                            →
                                        </div>
                                        <div class="col-3">
                                             <input type="text" name="" id="input2" class=" form-control" readonly value="0">
                                        </div>
                                        <div class="col-2">
                                            <select name="" id="select2" class=" form-select" onchange="OnSelect2Change()">
                                                @foreach ($Alldevises as $devise)
                                                <option value="{{ $devise->symbol}}">{{ $devise->symbol}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3 p-0 m-0">
                                @php
                                   $sumBg = $sumTotalFromAllClients < 0 ? 'danger' : 'success'; 
                                @endphp
                                <div class="card border-left-{{$sumBg}} shadow h-100 p-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs  text-uppercase mb-1 text-{{$sumBg}} bold  font-weight-bolder"  >
                                                    TOTAL AVEC LES CLIENTS</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <i class="bi bi-cash-coin text-{{$sumBg}}" style="font-size: 22px"></i>
                                                    <span class="text-{{$sumBg}} bold font-weight-bold" >{{$sumTotalFromAllClients}} ({{$entreprise->base_devise}})</span> <i class="bi bi-arrow-down text-danger" style="font-size: 22px"></i>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    

                </div>
                <!-- /.container-fluid -->

            </div>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

            @include('layout.footer')
        </div>
        <!-- End of Content Wrapper -->

    </div>


    <!-- Bootstrap core JavaScript-->


    <!-- Page level custom scripts -->
    <script src="{{ asset('/assets/js/core/jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/core/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/now-ui-dashboard.min.js') }}" defer type="text/javascript"></script>



<script type="text/javascript">
    var chartData = <?php echo $chartData_json; ?>;
    
    // Convert chartData to a format that can be used for the chart
    var labels = [];
    var data = [];
    
    chartData.forEach(function(item) {
        var month = Object.keys(item)[0];
        var count = item[month];
        labels.push(month);
        data.push(count);
    });

    // Area Chart Example
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: "Operation Count",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "#448374",
                pointRadius: 3,
                pointBackgroundColor: "#448374",
                pointBorderColor: "#448374",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "#448374",
                pointHoverBorderColor: "#448374",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: data,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12 // Adjust to show all months
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return number_format(value);
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
</script>
<script  type="text/javascript" >
    // Set new default font family and font color to mimic Bootstrap's default styling
    (Chart.defaults.global.defaultFontFamily = "Nunito"),
        '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = "#858796";

    // Pie Chart Example
        var ctx = document.getElementById("myPieChart");
        var deposerValue = <?php echo $deposer_json; ?> ;
        var retraitValue = <?php echo $retrait_json; ?> ;
        var converteValue = <?php echo $convertes_json; ?> ;
        var transfertsValue = <?php echo $transferts_json; ?> ;
        var operationsValue = <?php echo $operations_json; ?> ;
        var myPieChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Deposer", "Retrait", "Converte", "transfer",'operation'],
            datasets: [
                {
                    data: [deposerValue,retraitValue,converteValue,transfertsValue,operationsValue],
                    backgroundColor: ["#4e73df", "#1cc88a", "#36b9cc","#850F8D",'#10439F'],
                    hoverBackgroundColor: ["#2e59d9", "#17a673", "#2c9faf","#C738BD",'#10439F'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: "#dddfeb",
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false,
            },
            cutoutPercentage: 80,
        },
    });
</script>

  
</body>