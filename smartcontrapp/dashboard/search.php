<?php
session_start();
if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {

    $searchMain2 = isset($_GET['searchMain2']) ? strip_tags(htmlspecialchars($_GET['searchMain2'])) : null;

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

    if ($searchMain2 === null) {
        $reqSearch = "SELECT mode_operatoire.*, adhesion.*, resiliation.* FROM mode_operatoire LEFT JOIN adhesion on mode_operatoire.id = adhesion.id_operatoire LEFT JOIN resiliation ON mode_operatoire.id = resiliation.id_mode ORDER BY mode_operatoire.id DESC";
    } else {
        $reqSearch = "
SELECT mode_operatoire.*, adhesion.*, resiliation.*
FROM mode_operatoire
LEFT JOIN adhesion on mode_operatoire.id = adhesion.id_operatoire
LEFT JOIN resiliation ON mode_operatoire.id = resiliation.id_mode
WHERE adhesion.date_ajout LIKE ? OR CONCAT_WS('|', mode_operatoire.id, mode_operatoire.site, mode_operatoire.entite, mode_operatoire.ville, mode_operatoire.nature_bail, mode_operatoire.nom_locataire, mode_operatoire.contact, mode_operatoire.logement, mode_operatoire.duree_contrat, mode_operatoire.loyer_mensuel, mode_operatoire.frequence_paiement, mode_operatoire.mode_paiement, mode_operatoire.nombre_mois, mode_operatoire.montant_caution, revision_loyer, pénalites_retard, mode_operatoire.date_debut_contrat, mode_operatoire.date_fin_contrat, mode_operatoire.droit_enregistrement, mode_operatoire.nom_GI, mode_operatoire.numero_dossier, mode_operatoire.etat, mode_operatoire.favori) LIKE ?
ORDER BY mode_operatoire.id ASC
";
    }

    $smtp = $bdd->prepare($reqSearch);
    $searchTerm = '%' . $searchMain2 . '%'; // Ajout de caractères de joker autour du terme de recherche
    $smtp->execute(array($searchTerm, $searchTerm));
    $res = $smtp->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($res);
    echo json_encode($res);
}
