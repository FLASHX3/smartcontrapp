<?php
session_start();
if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {
    $searchMain3 = isset($_GET["search"]) ? strip_tags(htmlspecialchars($_GET["search"])) : null;
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

    if ($searchMain3 === null) {
        $reqSearch = "SELECT mode_operatoire.nom_GI, mode_operatoire.entite, mode_operatoire.site, mode_operatoire.logement, mode_operatoire.nom_locataire, mode_operatoire.numero_dossier, mode_operatoire.date_fin_contrat, mode_operatoire.etat, adhesion.* FROM adhesion, mode_operatoire WHERE mode_operatoire.id = adhesion.id_operatoire AND ville = ? AND (`mode_operatoire`.`etat` = \"En-cours\" OR `mode_operatoire`.`etat` = \"Actif\")";
    } else {
        $reqSearch = "SELECT mode_operatoire.nom_GI, mode_operatoire.entite, mode_operatoire.site, mode_operatoire.logement, mode_operatoire.nom_locataire, mode_operatoire.numero_dossier, mode_operatoire.date_fin_contrat, mode_operatoire.etat, adhesion.* FROM adhesion, mode_operatoire WHERE mode_operatoire.id = adhesion.id_operatoire AND ville = ? AND (`nom_locataire` LIKE ? OR `numero_dossier` LIKE ?) AND (`mode_operatoire`.`etat` = \"En-cours\" OR `mode_operatoire`.`etat` = \"Actif\")";
    }

    $smtp = $bdd->prepare($reqSearch);
    $smtp->execute(array($ville, '%' . $searchMain3 . '%', '%' . $searchMain3 . '%'));
    $res = $smtp->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($res);
    echo json_encode($res);
}
