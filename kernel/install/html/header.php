<?php
$timeStart = microtime(true);
session_start();
//Si la session $_SESSION['token'] est vide c'est que le joueur à validé un formulaire
if (empty($_SESSION['token']))
{
	//On génère un token qu'on stock dans une session pour sécuriser les formulaires
	$_SESSION['token'] = bin2hex(random_bytes(32)); 
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../favicon.ico">

        <title>Caranille</title>

		<!-- Bootstrap core CSS -->
        <link href="../../../css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../../../css/navbar-top-fixed.css" rel="stylesheet">
    </head>

	<body>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand"  href="../../../index.php">Caranille</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
		</nav>

		<main class="container">
		  <div class="bg-light p-5 rounded">