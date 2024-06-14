<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin") {

    if (isset($_POST['delete']) && !empty($_POST['delete'])) {
        $nom = isset($_POST['delnom']) ? strip_tags(htmlspecialchars($_POST['delnom'])) : null;

        if ($nom == null || empty($nom)) {
            header("location: index.php?compte=Erreur lors de la suppression du compte#main7");
        }

        try {
            $bdd = new PDO('mysql:host=localhost;dbname=gestcontrapp;charset=utf8', 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }

        $delete = $bdd->prepare("DELETE FROM users WHERE `users`.`nom` = ?");
        $resultat = $delete->execute(array($nom));

        if ($resultat) {
            setlog($_SESSION['id'], -8, "Suppression du compte de $nom");
            header("location: index.php?compte=Suppression du compte de $nom réussie#main7");
        } else {
            header("location: index.php?compte=Erreur lors de la suppression du compte#main7");
        }
    }else{
        header('location: index.php#main6');
    }
}else{
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: index.php');
}
