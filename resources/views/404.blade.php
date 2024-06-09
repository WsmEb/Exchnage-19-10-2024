<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="{{ asset('/js/deconnexion.js') }}"></script>
    <title>Page non trouvée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            text-align: center;
        }
        .error-code {
            font-size: 100px;
            font-weight: bold;
            color: #555;
        }
        .error-message {
            font-size: 24px;
            color: #888;
        }
        .home-link {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .home-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <div class="error-message">Oups! La page que vous recherchez est introuvable.</div>
        <a href="/" class="home-link">Retour à la page d'accueil</a>
    </div>
</body>
</html>
