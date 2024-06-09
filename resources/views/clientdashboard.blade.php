

<?php
    $deposer_json = json_encode($DeposerAction); 
    $retrait_json = json_encode($RetraitAction); 
    $convertes_json = json_encode($convertes); 
    $transferts_json = json_encode($transferts);
    // $chartData_json = json_encode($chartData);
   $operations_json = json_encode($operations); 
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Client Dashbord</title>
  <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
  <link rel="stylesheet" href="{{asset('/assets/css/sb-admin-2.min.css')}}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
  <script src="{{ asset('/assets/js/chart.min.js') }}"></script>
  {{-- <script src="{{ asset('/js/deconnexion.js') }}"></script> --}}
  {{-- <script defer >
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

       
  </script> --}}


  <style>
</style>
</head>

</html>
<body class="row" >
    {{-- @include('layout.sidebar') --}}
    <div class="main-panel" id="main-panel">
        @include('layout.clientNavbar')
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
                                                TOTAL DES CLIENTS</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{$operations}}
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
                                                TOTAL DES DCONVERTES</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                             
                                                {{$convertes}}
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
                                                TOTAL DES TRANSFERTS</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                               
                                                {{$transferts}}
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
                                                TOTAL DES DEPOSES</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                             
                                                {{$deposes}}
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



                    </div>

                    <!-- Content Row -->
                    <div class="row" style="padding: 0 10px">
                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">
                            <!-- Project Card Example -->
                            <div class="col-xl-12 col-lg-5">
                                <div class="card shadow mb-4">
                                    <!-- Card Header - Dropdown -->
                                    <div
                                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold"  style="color: #2a0a9f">CHART DES OPERATIONS</h6>
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
                                        <div class="mt-4 row text-center small">
                                            <span class="mr-2 col-2">
                                                <i class="bi bi-circle-fill " style="color: #af47d2;"></i> DEPOSER
                                            </span>
                                            <span class="mr-2  col  -2">
                                                <i class="bi bi-circle-fill" style="color: #6962AD;"></i> RETRAIT
                                            </span>
                                            <span class="mr-2  col-2">
                                                <i class="bi bi-circle-fill" style="color: #36b9cc;"></i> CONVERTE
                                            </span>
                                            <span class="mr-2  col-2">
                                                <i class="bi bi-circle-fill" style="color: #e1afd1;" ></i> TRANSFER
                                            </span>
                                            <span class="p-0 m-0 col-2">
                                                <i class="bi bi-circle-fill" style="color: #492E87;" ></i> OPERATION
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Color System -->
                            <div class="row">
                                <div class="col-lg-12 col-md-12 mb-4">
                                    <div class="card bg-light  shadow" style="border:3px solid;border-left-color:#2a0a9f;border-top-color:transparent;border-bottom-color:transparent;border-right-color: transparent">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold "  style="color: #2a0a9f">RECENT DEPOSER ACTION</h6>
                                        </div>
                                        <div class="card-body text-center font-weight-bold">
                                            <div class="row ">
                                                <div class="col-4  ">
                                                    <label for="" class="  text-dark" >DATE</label>
                                                </div>
                                                <div class="col-4">
                                                    <label for="" class="  text-dark" >TYPE</label>
                                                </div>
                                                <div class="col-4 ">
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
                                                    <div class='col-4  mb-1 {{$bg}}' >
                                                    <label for="" class="  text-dark">{{$lastDepose->date_depose}}</label>
                                                    </div>
                                                    <div class="col-4   mb-1 {{$bg}}">
                                                        <label for=" "  class="  text-dark">{{$lastDepose->type}}</label>
                                                    </div>
                                                    <div class="col-4   mb-1 {{$bg}}">
                                                        <label for=" "  class="  text-dark">{{number_format($lastDepose->amount,2)}} {{$lastDepose->devise}} | {{number_format($lastDepose->amount * convertedSymbolBase($lastDepose->devise) /  convertedSymbolBase($entreprise->base_devise)  ,2)}} </label>
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
                                    <div class="card bg-light  shadow" style="border:3px solid;border-left-color:#2a0a9f;border-top-color:transparent;border-bottom-color:transparent;border-right-color: transparent">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold"  style="color: #2a0a9f">RECENT TRANSFER ACTION</h6>
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
                                                    <div class="col-3  mb-1 {{$tranfBg}} ">
                                                        <label for="" class=" text-dark ">  {{ $lastTransfer->date }} </label>
                                                    </div>
                                                    <div class="col-3  mb-1 {{$tranfBg}} ">
                                                        <label for="" class=" text-dark "> {{$lastTransfer->expediteur == $lastTransfer->client ? 'Moi' : $lastTransfer->expediteur }} </label>
                                                    </div>
                                                    <div class="col-3  mb-1 {{$tranfBg}} ">
                                                        <label for=" "  class=" text-dark "> {{$lastTransfer->recepteur == $lastTransfer->client ? 'Moi' :$lastTransfer->recepteur }} </label>
                                                    </div>
                                                    <div class="col-3  mb-1 {{$tranfBg}} ">
                                                        <label for=" "  class=" text-dark "> {{number_format($lastTransfer->solde,2)}} {{$lastTransfer->devise}} | {{number_format($lastTransfer->solde  * convertedSymbolBase($lastTransfer->devise) / convertedSymbolBase($entreprise->base_devise),2)}} {{$entreprise->base_devise}}  </label> 
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
                                    <div class="card bg-light  shadow" style="border:3px solid;border-left-color:#2a0a9f;border-top-color:transparent;border-bottom-color:transparent;border-right-color: transparent">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold"  style="color: #2a0a9f">RECENT CONVERTER ACTION</h6>
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
                                                        <label for="" class=" text-dark">  Moi</label>
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
                                                        <label for=" "  class=" text-dark">  {{number_format($lastConverte->amount * convertedSymbolBase($lastConverte->devise) / convertedSymbolBase($lastConverte->convertedSymbol),2) }}     {{$lastConverte->convertedSymbol}} |  {{number_format($lastConverte->amount * convertedSymbolBase($lastConverte->devise) / convertedSymbolBase($entreprise->base_devise),2) }} {{$entreprise->base_devise}} </label> 
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



                            <!-- Approach -->
                            <div class="col-lg-12 mb-4 p-0 m-0">
                                <div class="card bg-light  shadow" style="border:3px solid;border-left-color:#2a0a9f;border-top-color:transparent;border-bottom-color:transparent;border-right-color: transparent">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold"  style="color: #2a0a9f">RECENTE OPERATION ACTION</h6>
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
                                                 $opBg = $lastOperation->type_operation == 'toi' ? "bg-success-subtle" : "bg-danger-subtle";
                                             @endphp
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for="" class=" text-dark">  {{$lastOperation->date}} </label>
                                                </div>
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for="" class=" text-dark">  {{$lastOperation->client}} </label>
                                                </div>
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for=" "  class=" text-dark">  {{$lastOperation->type_operation == 'toi' ? 'Moi'  : 'Admin'}} </label>
                                                </div>
                                                <div class="col-3 {{$opBg}} mb-1">
                                                    <label for=" "  class=" text-dark">  {{$lastOperation->total }}  {{$lastOperation->devise }} |  {{$lastOperation->total * convertedSymbolBase($lastOperation->devise) / convertedSymbolBase($entreprise->base_devise) }} {{$entreprise->base_devise}}  </label> 
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
                                @php
                                   $sumBg = $sumTotalFromAllClients < 0 ? 'danger' : 'success'; 
                                @endphp
                                <div class="card border-left-{{$sumBg}} shadow h-100 p-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs  text-uppercase mb-1 text-{{$sumBg}} bold  font-weight-bolder"  >
                                                    TOTAL AVEC L'ADMIN</div>
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
        var operationValue = <?php echo $operations_json; ?> ;
        var myPieChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: ["Deposer", "Retrait", "Converte", "Transfer",'Operations'],
            datasets: [
                {
                    data: [deposerValue,retraitValue,converteValue,transfertsValue,operationValue],
                    backgroundColor: ["#AF47D2", "#5C88C4", "#7469B6","#E1AFD1",'#492E87'],
                    hoverBackgroundColor: ["#2e59d9", "#17a673", "#2c9faf","#C738BD",'#492E87'],
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


    <!-- Page level custom scripts -->
    <script src="{{ asset('/assets/js/core/jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/core/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.jquery.min.js') }}" defer></script>
    <script src="{{ asset('/assets/js/now-ui-dashboard.min.js') }}" defer type="text/javascript"></script>




  
</body>
