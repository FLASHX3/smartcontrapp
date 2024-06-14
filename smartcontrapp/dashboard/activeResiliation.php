<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {

    $idResiliation = (isset($_GET['id'])) ? strip_tags(htmlspecialchars($_GET['id'])) : null;
    $dateResiliation = (isset($_GET['dateResiliation'])) ? strip_tags(htmlspecialchars($_GET['dateResiliation'])) : null;

    $dateObj = DateTime::createFromFormat('d/m/Y', $dateResiliation);
    $dateResiliation = $dateObj->format('Y-m-d');

    if ($idResiliation == null || $dateResiliation == null) {
        header('location index.php?resilie=Erreur lors de l\'ajout du dossier en résiliation#main3');
    }

    try {
        $bdd = new PDO('mysql:host=localhost;dbname=gestcontrapp;charset=utf8', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur : ' . $e->getMessage());
    }

    $check = $bdd->prepare("SELECT id_mode FROM resiliation WHERE id_mode = ?");
    $check->execute(array($idResiliation));
    $nbResult = $check->rowCount();

    if ($nbResult >= 1) {
        header('location: index.php?resilie=Ce contrat a déjà été ajouté aux résiliés!!#main2');
    } else {
        $ajoutResiliation = $bdd->prepare("INSERT INTO resiliation VALUES('',?,1,0,0,0,0,0,0,0,NULL,0,0,0,NOW(),?)");
        $ajoutResiliation->execute(array($idResiliation, $dateResiliation));

        setlog($_SESSION['id'], 6, "Ajout du dossier № $idResiliation en résiliation");

        $update = $bdd->prepare("UPDATE mode_operatoire SET etat = 'En-Résiliation' WHERE id = ? ");
        $bool = $update->execute(array($idResiliation));
        header("location: index.php?resilie=Le contrat № $idResiliation a été résilié#main4");
    }
}else{
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: ../index.php');
}
