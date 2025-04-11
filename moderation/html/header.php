<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon.ico">

        <title><?php echo $gameName ?></title>

        <!-- Bootstrap core CSS -->
        <link href="../../css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../../css/navbar-top-fixed.css" rel="stylesheet">
    </head>

    <body>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <a class="navbar-brand" href="index.php">Panel Administration</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-expanded="false">Sanctions</a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown08">
                            <li><a class="dropdown-item" href="../../moderation/ban/index.php">Bannir/Débannir</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav pull-right">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="../../index.php">Retour au jeu</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="container">
            <div class="bg-light p-5 rounded">