<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {
    if (isset($_POST["save"]) && !empty($_POST["save"])) {
        // Récupération des valeurs du formulaire
        $preavis = isset($_POST["preavis"]) ? strip_tags(htmlspecialchars($_POST["preavis"])) : null;
        $const_trans_elem_repert = isset($_POST["const_trans_elem_repert"]) ? strip_tags(htmlspecialchars($_POST["const_trans_elem_repert"])) : null;
        $preval_dossier = isset($_POST["preval_dossier"]) ? strip_tags(htmlspecialchars($_POST["preval_dossier"])) : null;
        $val_dos_res = isset($_POST["val_dos_res"]) ? strip_tags(htmlspecialchars($_POST["val_dos_res"])) : null;
        $trans_elem_res = isset($_POST["trans_elem_res"]) ? strip_tags(htmlspecialchars($_POST["trans_elem_res"])) : null;
        $trans_rep_client = isset($_POST["trans_rep_client"]) ? strip_tags(htmlspecialchars($_POST["trans_rep_client"])) : null;
        $etat_lieux = isset($_POST["etat_lieux"]) ? strip_tags(htmlspecialchars($_POST["etat_lieux"])) : null;
        $const_trans_elem_res = isset($_POST["const_trans_elem_res"]) ? strip_tags(htmlspecialchars($_POST["const_trans_elem_res"])) : null;
        $control_val = isset($_POST["control_val"]) ? strip_tags(htmlspecialchars($_POST["control_val"])) : null;
        $approb_doc_final = isset($_POST["approb_doc_final"]) ? strip_tags(htmlspecialchars($_POST["approb_doc_final"])) : null;
        $paiement = isset($_POST["paiement"]) ? strip_tags(htmlspecialchars($_POST["paiement"])) : null;
        $archivage_doc = isset($_POST["archivage_doc"]) ? strip_tags(htmlspecialchars($_POST["archivage_doc"])) : null;
        $id_mode = isset($_POST["id_mode"]) ? strip_tags(htmlspecialchars($_POST["id_mode"])) : null;
        $numContratR = isset($_POST['numContratR']) ? strip_tags(htmlspecialchars($_POST['numContratR'])) : null;

        // Traitement des valeurs et mise à jour de la base de données
        if ($control_val == "") {
            $control_val = null;
        }

        try {
            $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $updateAdhesion = $bdd->prepare("UPDATE resiliation SET lettre_preavis = ?, transmition_elements = ?, prevalidation_dossier = ?, validation_provisoire = ?, transmition_element_provisoire = ?, transmition_reponse = ?, etat_lieux = ?, transmition_elements_complet = ?, controle_validation_dossier = ?, approbation_dossier = ?, paiement_locataire = ?, archivage_resiliation = ? WHERE id_mode = ?");
            $resultat = $updateAdhesion->execute(array($preavis, $const_trans_elem_repert, $preval_dossier, $val_dos_res, $trans_elem_res, $trans_rep_client, $etat_lieux, $const_trans_elem_res, $control_val, $approb_doc_final, $paiement, $archivage_doc, $id_mode));
            $updateNumContrat = $bdd->prepare("UPDATE mode_operatoire SET numero_dossier = ? WHERE id = ?");
            $res1 = $updateNumContrat->execute(array($numContratR,$id_mode));

            if($resultat && $res1){
                setlog($_SESSION['id'], -5, "Mise à jour de la checkliste de résiliation № $id_mode");
                $updateAdhesion->closeCursor();
            }

            $verif = $bdd->prepare("SELECT `mode_operatoire`.`etat`, `resiliation`.* FROM `mode_operatoire`,`resiliation` WHERE `mode_operatoire`.`id` = `resiliation`.`id_mode`");
            $verif->execute();

            while ($resultat = $verif->fetch()) {
                if ($resultat['etat'] != "En-cours" && $resultat['etat'] != "Actif") {
                    $percent = 0;
                    $control_val = ($resultat['controle_validation_dossier'] === 1 || $resultat['controle_validation_dossier'] === 0) ? 1 : 0;
                    $percent = $resultat['lettre_preavis'] + $control_val + $resultat['transmition_elements'] + $resultat['prevalidation_dossier'] + $resultat['validation_provisoire'] + $resultat['transmition_element_provisoire'] + $resultat['transmition_reponse'] + $resultat['etat_lieux'] + $resultat['transmition_elements_complet'] + $resultat['approbation_dossier'] + $resultat['paiement_locataire'] + $resultat['archivage_resiliation'];
                    $percent = $percent * 100 / 12;
                    if ($percent == 100) {
                        $maj = $bdd->prepare("UPDATE mode_operatoire SET etat = 'Résilié' WHERE id = ?");
                        $maj->execute(array($resultat['id_mode']));
                        $maj->closeCursor();
                    }
                }
            }
            $verif->closeCursor();

            header("location: index.php?majR=Résiliation mis à jour ✅#main4");
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }else{
        header('location: index.php?majR=Erreur lors de la mise à jour de la chekliste de résiliation#main4');
    }
}else{
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: ../index.php');
}
