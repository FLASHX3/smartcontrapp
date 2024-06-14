<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {

    if (isset($_POST['Modifiez']) && !empty($_POST["Modifiez"])) {
        $siteM = isset($_POST["siteM"]) ? strip_tags(htmlspecialchars($_POST["siteM"])) : null;
        $entiteM = isset($_POST["entiteM"]) ? strip_tags(htmlspecialchars($_POST["entiteM"])) : null;
        $villeM = isset($_POST["villeM"]) ? strip_tags(htmlspecialchars($_POST["villeM"])) : null;
        $natureBailM = isset($_POST["natureBailM"]) ? strip_tags(htmlspecialchars($_POST["natureBailM"])) : null;
        $nomM = isset($_POST["nomM"]) ? strip_tags(htmlspecialchars($_POST["nomM"])) : null;
        $contactM = isset($_POST["contactM"]) ? strip_tags(htmlspecialchars($_POST["contactM"])) : null;
        $logementM = isset($_POST["logementM"]) ? strip_tags(htmlspecialchars($_POST["logementM"])) : null;
        $timeM = isset($_POST["timeM"]) ? strip_tags(htmlspecialchars($_POST["timeM"])) : null;
        $loyerM = isset($_POST["loyerM"]) ? strip_tags(htmlspecialchars($_POST["loyerM"])) : null;
        $frequenceM = isset($_POST["frequenceM"]) ? strip_tags(htmlspecialchars($_POST["frequenceM"])) : null;
        $modePaiementM = isset($_POST["modePaiementM"]) ? strip_tags(htmlspecialchars($_POST["modePaiementM"])) : null;
        $nombreMoisM = isset($_POST["nombreMoisM"]) ? strip_tags(htmlspecialchars($_POST["nombreMoisM"])) : null;
        $montantCautionM = isset($_POST["montantCautionM"]) ? strip_tags(htmlspecialchars($_POST["montantCautionM"])) : null;
        $revisionM = isset($_POST["revisionM"]) ? strip_tags(htmlspecialchars($_POST["revisionM"])) : null;
        $taux_revisionM = isset($_POST["taux_revisionM"]) ? strip_tags(htmlspecialchars($_POST["taux_revisionM"])) : null;
        $penaliteM = isset($_POST["penaliteM"]) ? strip_tags(htmlspecialchars($_POST["penaliteM"])) : null;
        $debutM = isset($_POST["debutM"]) ? strip_tags(htmlspecialchars($_POST["debutM"])) : null;
        $finM = isset($_POST["finM"]) ? strip_tags(htmlspecialchars($_POST["finM"])) : null;
        $saveM = isset($_POST["saveM"]) ? strip_tags(htmlspecialchars($_POST["saveM"])) : null;
        $giM = isset($_POST["giM"]) ? strip_tags(htmlspecialchars($_POST["giM"])) : null;
        $numDoc = isset($_POST["numDocM"]) ? strip_tags(htmlspecialchars($_POST["numDocM"])) : null;
        $idM = isset($_POST["idM"]) ? strip_tags(htmlspecialchars($_POST["idM"])) : null;

        if (
            $siteM == null || $entiteM == null || $villeM || $natureBailM == null
            || $nomM == null || $contactM == null || $logementM == null || $timeM == null
            || $loyerM == null || $frequenceM == null || $modePaiementM == null || $nombreMoisM == null
            || $montantCautionM == null || $revisionM == null || $taux_revisionM == null || $penaliteM == null || $debutM == null
            || $finM == null || $saveM == null || $giM == null
        ) {
            header('location: index.php?modif = Erreur! une/des donnée(s) n\'a/ont pas été spécifiée(s) ⚠');
        }

        // $dateRevision = new DateTime($debutM);
        // switch ($revisionM) {
        //     case 'Annuelle':
        //         $dateRevision->modify('+1 year');
        //         break;
        //     case 'Biennale':
        //         $dateRevision->modify('+2 year');
        //         break;
        //     case 'Triennale':
        //         $dateRevision->modify('+3 year');
        //         break;
        //     default:
        //         $dateRevision->modify('+1 year');
        //         break;
        // }
        // $newDaterevision = $dateRevision->format('Y-m-d');

        try {
            $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $updateModeoperatoire = $bdd->prepare("UPDATE mode_operatoire SET site = ?, entite = ?, ville = ?, nature_bail = ?, nom_locataire = ?, contact = ?, logement = ?, duree_contrat = ?, loyer_mensuel = ?, frequence_paiement = ?, mode_paiement = ?, nombre_mois = ?, montant_caution = ?, revision_loyer = ?, taux_revision = ?, pénalites_retard = ?, date_debut_contrat = ?, date_fin_contrat = ?, droit_enregistrement = ?, nom_GI = ?, numero_dossier = ? WHERE id = ?");
            $updateModeoperatoire->execute(array($siteM, $entiteM, $villeM, $natureBailM, $nomM, $contactM, $logementM, $timeM, $loyerM, $frequenceM, $modePaiementM, $nombreMoisM, $montantCautionM, $revisionM, $taux_revisionM, $penaliteM, $debutM, $finM, $saveM, $giM, $numDoc, $idM));
            $updateModeoperatoire->closeCursor();

            setlog($_SESSION['id'], 3, "Modification du contrat № $idM");

            header("Location: index.php?modif=Modification du contrat № $idM effectuée#main2"); // Correcte le format de l'en-tête
            exit; // Assurez-vous de terminer l'exécution du script après la redirection
        } catch (PDOException $e) {
            die('Erreur : ' . $e->getMessage());
        }
    } else {
        header('location: index.php#main2');
    }
} else {
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: index.php');
}
