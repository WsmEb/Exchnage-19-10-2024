<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script
      src="https://kit.fontawesome.com/64d58efce2.js"
      crossorigin="anonymous"
    ></script>
    <link rel="stylesheet" href="/styles/style.css" />
    <title>Connexion</title>
  </head>
  <body>
    <div class="container">

      <div class="forms-container">
        <div class="signin-signup">
            <form action="{{ route('client.connexion') }}" method="POST" class="sign-in-form">
            @csrf
            <h2 class="title-sign">Connexion</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>

              <input type="text" name="username" placeholder="Identifient" value="{{ old('username') }}" required />
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" placeholder="Mot de passe" value="{{ old('password') }}" required />
            </div>
            <input type="submit" value="Connexion" class="btn solid" name="btnConnecte" />
            @if(session('error'))
            <div class="row">
              <div class="col-12">
                  <p style="font-size: 14px;color:red;margin-top:20px;font-weight: 700;">
                      {{ session('error') }}
                  </p>
                </div>
            </div>
            @endif
          </form>
          
          <form action="#" class="sign-up-form card shadow ">
            <h2 class="title" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif">À propos de l'application <img src="/img/coin.svg" style="width: 20px;color:green" /></h2>
            <div class=" card shadow-lg border border-2 border-dark ">
              <div class="card-body">
                <div class="card-text">
                Exchange Service Administrator est une application Web complète conçue pour gérer les opérations de change. Il offre des fonctionnalités robustes, notamment la gestion des clients, des devises, des opérations et des stocks, ainsi qu'un tableau de bord personnalisable. Les utilisateurs peuvent exporter des données aux formats PDF ou Excel, utiliser des outils de gestion calculables et gérer efficacement les comptes et les autorisations.
              </div>
            </div>
           
          </form>
        </div>
      </div>
    </form>
      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>À propos de l'application:</h3>
            <p>
            Application Web de l'administrateur du service Exchange            </p>
            <button class="btn transparent" id="sign-up-btn">
            À propos
            </button>
          </div>
          <img src="img/log.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>vous êtes utilisateur ?</h3>
            <p>
            Connectez-vous à votre compte et profitez de votre expérience.
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Sign in
            </button>
          </div>
          <img src="img/register.svg" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="/js/app.js"></script>
  </body>
</html>



