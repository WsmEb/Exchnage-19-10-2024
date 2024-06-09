<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <title>Entreprise Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/now-ui-dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css" rel="stylesheet">
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <script defer >
        setTimeout(function() {
    document.querySelector('.message').style.display = 'none';
}, 5000);
</script>
</head>
<body class="user-profile">
    <div class="wrapper">
        @include('layout.sidebar')
        <div class="main-panel" id="main-panel">
    
            @include('layout.navbar')
    
            <div class="panel-header panel-header-sm"></div>
            <div style="margin-top: 50px">
                <div class="row  rounded position-absolute float-right w-100" style="margin-right: 10px" >
                    <div class="col-md-8 text-center">
                        <h5 class="font-weight-bold"></h5>
                    </div>
                    <div class="col-md-4 message">
                        @if(session('success'))
                        <div class="alert alert-info text-light fw-bold">
                            {{ session('success') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
                <div class="row m-0">
    
                    <div class="col-md-12 col-sm-12 col-lg-8  m-sm-auto ">
                        <div class="row fixed mb-0">
                            <div class="col-4">
                                <a href="{{route("profile.userIndex")}}" style="border-radius: 17px" class="bg-primary text-bold p-3 text-light text-decoration-none" > Utilisateur </a>
                            </div>
                        </div>
                        <div class="card m-auto p-1">
                            @if($buttonAction)
                            <div class="card-header">
                                <h5 class="title">Edit Entreprise</h5>
                            </div>
                            <div class="card-body ">
                                <form method="POST" action="{{route("profile.update")}}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="referenceVaue" value="{{$entreprise->titre}}" >
                                     <div class="row">
                                       <div class="col-md-6 pr-1">
                                           <div class="form-group">
                                            <h6 for="company" class=" text-muted font-weight-lighter">Logo</h6>
                                            <img src="{{ '/uploads'.'/'.$entreprise->logo ?: '/uploads/onerror.png' }}" class="card-img rounded-circle" style="width: 100px;height:100px;" />
                                            <br>
                                            <input type="file" class="form-control"  name="logo" id="" />
                                        </div>
                                    </div>
                                </div>
                                    <div class="row">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label for="company">Titre :</label>
                                                <input type="text" id="titre" name="titre" class="form-control" required placeholder="Titre" value="{{$entreprise->titre}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description :</label>
                                                <textarea type="text" id="description" name="description" placeholder="Description" class="form-control" required  > {{$entreprise->description}} </textarea>
                                            </div>
                                            <button type="submit" class="btn  my-3 p-2.5 text-light rounded-3" style="font-size: 14px;background-color:#f96332;">Update</button>
                                
                                        </div>
                                    </div>
                                </form>
                                
                            </div>
                            @else
                            <h5 style="height: 400px" class="text-danger text-center flex align-content-center fw-bolder"> UnAouthorized Action de Modification </h5>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12 bg-white col-sm-12 col-lg-4">
                        <div class="card-body">
                            <header>
                                <div class="row">
                                    <div class="col-12 text-center m-auto py-2">
                                        <img class="card-img rounded-circle m-auto" style="width: 120px;height:120px;" src="{{ '/uploads'.'/'.$entreprise->logo ?: '/uploads/onerror.png' }}" alt="">
                                    </div>
                                </div>
                                <div class="row my-1">
                                    <div class="container ">
                                        <div class="row">
                                         <div class="col-12 text-center">
                                             <h2 style="color: #f96332;" class="fw-bolder"  >{{$user->nom}}</h2>
                                         </div>
                                        </div>
                                        <div class="row my-2">
                                            <div class="col-12 text-center my-2">
                                                <h6 for="" class="text-dark">Prenom : {{$user->prenom}} </h6>
                                            </div>
                                            <div class="col-12 text-center my-2">
                                                <h6 for="" class="text-dark">Entreprise : {{$entreprise->titre}} </h6>
                                            </div>
                                        </div>
                                     </div>
                                </div>
                            </header>
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
