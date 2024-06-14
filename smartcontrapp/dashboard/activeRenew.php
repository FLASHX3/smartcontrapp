<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {

    $idRenew= (isset($_GET['id'])) ? strip_tags(htmlspecialchars($_GET['id'])) : null;
    $dateRenew = (isset($_GET['dateRenew'])) ? strip_tags(htmlspecialchars($_GET['dateRenew'])) : null;

    $dateObj = DateTime::createFromFormat('d/m/Y', $dateRenew);
    $dateRenew = $dateObj->format('Y-m-d');

    if ($idRenew == null || $dateRenew == null) {
        header('location index.php?maj=Erreur lors du renouvellement du dossier#main3');
    }

    try {
        $bdd = new PDO('mysql:host=localhost;dbname=gestcontrapp;charset=utf8', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur : ' . $e->getMessage());
    }

    $check = $bdd->prepare("UPDATE mode_operatoire SET date_fin_contrat = ? WHERE id = ?");
    $check->execute(array( $dateRenew,$idRenew));

    setlog($_SESSION['id'], 10, "Le contrat № $idRenew a été renouvellé");
    header("location: index.php?maj=Le contrat № $idRenew a été renouvellé#main3");
}else{
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: ../index.php');
}
