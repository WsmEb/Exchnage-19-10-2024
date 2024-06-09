
{{-- 



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/jpg" href="../img/money.jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Connexion</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        .container {
            height: 100%;
            background: linear-gradient(to right, #001F3F, #0099FF);
        }

        .login-form {
            background-color: #fff;
            width: 100%;
            /* Adjust width to 100% for responsiveness */
            max-width: 450px;
            height: 450px;
            border-radius: 15px;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .text {
            color: #4f4f4f;
            font-size: 30px;
            font-weight: 700;
            text-align: center;
            line-height: 80px;
            margin-top: 40px;
        }

        .form-item,
        .btn,
        .other {
            margin: 30px auto;
            width: 90%;
        }

        input {
            width: 80%;
            margin-top: 20px;
            padding: 10px;
            border: 0;
            outline: none;
            border-bottom: 1px solid #0099FF;
        }

        input::placeholder {
            font-weight: bold;
            color: #acb7c9;
        }

        input:focus {
            animation: bBottom 2s infinite;
        }

        @keyframes bBottom {
            50% {
                border-bottom: 1px solid #a6c1ee;
            }
        }

        .btn {
            height: 40px;
            width: 200px;
            line-height: 40px;
            color: #fff;
            font-weight: bold;
            border: none;
            text-align: center;
            cursor: pointer;
            border-radius: 10px;
            background: linear-gradient(to right, #001F3F, #0099FF);
            background-size: 200%;
            outline: none;
        }

        .btn:hover {
            animation: btnAnimate 1s infinite;
        }

        @keyframes btnAnimate {
            50% {
                background-position: 100%;
            }
        }

        a {
            text-decoration: none;
            color: #fbc2eb;
        }

        /* Media queries for responsiveness */
        @media (max-width: 768px) {
            .login-form {
                max-width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-form">
            <div class="text">Connexion</div>
            <form action="{{ route('connexion') }}" method="POST">
            @csrf
                <div class="form-item">
                    <input type="text" name="username" placeholder="Identifient" value="{{ old('username') }}" required>
                    <input type="password" name="password" placeholder="Mot de passe" value="{{ old('password') }}" required>
                    @if(session('error'))
                    <p style="font-size: 14px;color:red;margin-top:20px;font-weight: 700;">
                        {{ session('error') }}
                    </p>
                    @endif
                </div>
                <button class="btn" name="btnConnecte">Connexion</button>
            </form>
        </div>
    </div>
</body>

</html> --}}

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
            <form action="{{ route('connexion') }}" method="POST" class="sign-in-form">
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



