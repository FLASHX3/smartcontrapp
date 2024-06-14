<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin") {

    if (isset($_POST['creez']) && !empty($_POST['creez'])) {
        $nom = isset($_POST['newName']) ? strip_tags(htmlspecialchars($_POST['newName'])) : null;
        $login = isset($_POST['newLogin']) ? strip_tags(htmlspecialchars($_POST['newLogin'])) : null;
        $password = isset($_POST['newPassword']) ? strip_tags(htmlspecialchars($_POST['newPassword'])) : null;
        $cpassword = isset($_POST['newCPassword']) ? strip_tags(htmlspecialchars($_POST['newCPassword'])) : null;

        if ($nom == null || $login == null || $password == null || $cpassword == null) {
            header('location: index.php?compte=Erreur lors de la création du compte#main7');
        }

        try {
            $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        $unique = $bdd->prepare("SELECT login FROM users WHERE login = ? OR nom = ?");
        $unique->execute(array($login, $nom));

        $count = $unique->rowCount();
        if ($count > 0) {
            header('location: index.php?compte=Ce compte existe déjà ⚠#main7');
        }

        $insert = $bdd->prepare("INSERT INTO users VALUES ('',?,'user',?,?)");
        $create = $insert->execute(array($nom, $login, $password));

        if ($create) {
            setlog($_SESSION['id'], 8, "Création du compte de $nom");
            header("location: index.php?compte=Création du compte de $nom réussie#main7");
        } else {
            header('location: index.php?compte=Erreur lors de la création du compte#main7');
        }
    }else{
        header('location: index.php#main6');
    }
}else{
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: ../index.php');
}
