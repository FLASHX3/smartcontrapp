<?php
session_start();
if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {
    $searchMain4 = isset($_GET["search"]) ? strip_tags(htmlspecialchars($_GET["search"])) : null;
    $ville = isset($_GET["ville"]) ? strip_tags(htmlspecialchars($_GET["ville"])) : null;

    $serveurname = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestcontrapp";

    try {
        $bdd = new PDO("mysql:host=$serveurname;dbname=$dbname", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion! " . $e->getMessage());
    }

    if ($searchMain4 === null) {
        $reqSearch = "SELECT mode_operatoire.nom_locataire, mode_operatoire.ville, mode_operatoire.logement, mode_operatoire.numero_dossier, mode_operatoire.nom_GI, mode_operatoire.site, `resiliation`.* FROM `mode_operatoire`, `resiliation` WHERE `mode_operatoire`.`id` = `resiliation`.`id_mode` AND mode_operatoire.ville = ?";
    } else {
        $reqSearch = "SELECT mode_operatoire.nom_locataire, mode_operatoire.ville, mode_operatoire.logement, mode_operatoire.numero_dossier, mode_operatoire.nom_GI, mode_operatoire.site, `resiliation`.* FROM `mode_operatoire`, `resiliation` WHERE `mode_operatoire`.`id` = `resiliation`.`id_mode` AND mode_operatoire.ville = ? AND CONCAT_WS('|', mode_operatoire.numero_dossier, mode_operatoire.nom_locataire, mode_operatoire.nom_GI, mode_operatoire.logement) LIKE ?";
    }

    $smtp = $bdd->prepare($reqSearch);
    $smtp->execute(array($ville, '%' . $searchMain4 . '%'));
    $res = $smtp->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($res);
    echo json_encode($res);
}
