<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin") {

    $id = isset($_GET['id']) ? strip_tags(htmlspecialchars($_GET['id'])) : null;
    $type = isset($_GET['type']) ? strip_tags(htmlspecialchars($_GET['type'])) : null;

    if ($id == null || $type == null || empty($id) || empty($type)) {
        header('location: index.php?delete=Erreur lors de la suppression du contrat#main2');
    }

    try {
        $bdd = new PDO('mysql:host=localhost;dbname=gestcontrapp;charset=utf8', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Erreur : ' . $e->getMessage());
    }

    $delete = $bdd->prepare("DELETE FROM mode_operatoire WHERE `mode_operatoire`.`id` = ?");
    $res = $delete->execute(array($id));
    if ($type == "En-cours" || $type == "Actif") {
        $supprime = $bdd->prepare("DELETE FROM adhesion WHERE adhesion.id_operatoire = ?");
        $del1 = $supprime->execute(array($id));
    } else {
        $supprime = $bdd->prepare("DELETE FROM resiliation WHERE resiliation.id_mode = ?");
        $del2 = $supprime->execute(array($id));
    }

    if ($res && $del1) {
        setlog($_SESSION['id'], 7, "Suppression du dossier d'adhésion № $id");
        header("location: index.php?delete=Suppression du contrat № $id réussie#main2");
    }else if ($res && $del2) {
        setlog($_SESSION['id'], -7, "Suppression du dossier de résiliation № $id");
        header("location: index.php?delete=Suppression du contrat № $id réussie#main2");
    } else {
        header("location: index.php?delete=Erreur lors de la suppression du contrat#main2");
    }
} else {
    header('location: index.php?delete=Vous n\'avez pas le droit de suppression!#main2');
}
