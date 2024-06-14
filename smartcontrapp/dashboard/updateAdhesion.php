<?php
require_once('log.php');

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {

    if (isset($_POST["enregistrer"]) && !empty($_POST["enregistrer"])) {
        // Récupération des valeurs du formulaire
        $negoce = isset($_POST["negoce"]) ? strip_tags(htmlspecialchars($_POST["negoce"])) : null;
        $val_off = isset($_POST["val_off"]) ? strip_tags(htmlspecialchars($_POST["val_off"])) : null;
        $info_client = isset($_POST["info_client"]) ? strip_tags(htmlspecialchars($_POST["info_client"])) : null;
        $elab_contrat = isset($_POST["elab_contrat"]) ? strip_tags(htmlspecialchars($_POST["elab_contrat"])) : null;
        $trans_contrat = isset($_POST["trans_contrat"]) ? strip_tags(htmlspecialchars($_POST["trans_contrat"])) : null;
        $final_dossier = isset($_POST["final_dossier"]) ? strip_tags(htmlspecialchars($_POST["final_dossier"])) : null;
        $control_final = isset($_POST["control_final"]) ? strip_tags(htmlspecialchars($_POST["control_final"])) : null;
        $val_doss = isset($_POST["val_doss"]) ? strip_tags(htmlspecialchars($_POST["val_doss"])) : null;
        $trans_contrat_remise = isset($_POST["trans_contrat_remise"]) ? strip_tags(htmlspecialchars($_POST["trans_contrat_remise"])) : null;
        $trans_decharge = isset($_POST["trans_decharge"]) ? strip_tags(htmlspecialchars($_POST["trans_decharge"])) : null;
        $recept_doc = isset($_POST["recept_doc"]) ? strip_tags(htmlspecialchars($_POST["recept_doc"])) : null;
        $archivage_doc = isset($_POST["archivage"]) ? strip_tags(htmlspecialchars($_POST["archivage"])) : null;
        $id_operatoire = isset($_POST["id_operatoire"]) ? strip_tags(htmlspecialchars($_POST["id_operatoire"])) : null;
        $numContrat = isset($_POST['numContrat']) ? strip_tags(htmlspecialchars($_POST['numContrat'])) : null;

        // Traitement des valeurs et mise à jour de la base de données
        if ($val_off == "") {
            $val_off = null;
        }
        if ($elab_contrat == "") {
            $elab_contrat = null;
        }
        if ($control_final == "") {
            $control_final = NULL;
        }
        
        try {
            $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $updateAdhesion = $bdd->prepare("UPDATE adhesion SET negoce = ?, validation_offre = ?, info_client = ?, elaboration_contrat = ?, transmition_contrat_client = ?, finalisation_dossier = ?, control_final = ?, validation_dossier = ?, transmition_contrat_remise = ?, transmition_decharge = ?, reception_dossier = ?, archivage = ? WHERE id_operatoire = ?");
            $res = $updateAdhesion->execute(array($negoce, $val_off, $info_client, $elab_contrat, $trans_contrat, $final_dossier, $control_final, $val_doss, $trans_contrat_remise, $trans_decharge, $recept_doc, $archivage_doc, $id_operatoire));
            $updateNumContrat = $bdd->prepare("UPDATE mode_operatoire SET numero_dossier = ? WHERE id = ?");
            $res1 = $updateNumContrat->execute(array($numContrat,$id_operatoire));
            
            if($res && $res1){
                setlog($_SESSION['id'], 5, "Mise à jour de la checkliste d'adhésion № $id_operatoire");
                $updateAdhesion->closeCursor();
            }else{
                header('location: index.php?maj=Erreur lors de la mise à jour de la checkliste d\'adhésion#main3');
            }

            $verif = $bdd->prepare("SELECT `mode_operatoire`.`etat`, `adhesion`.* FROM `mode_operatoire`,`adhesion` WHERE `mode_operatoire`.`id` = `adhesion`.`id_operatoire`");
            $verif->execute();

            while ($resultat = $verif->fetch()) {
                if ($resultat['etat'] != "Résilié" && $resultat['etat'] != "En-Résiliation") {
                    $percent = 0;
                    $val_offre = ($resultat['validation_offre'] === 1 || $resultat['validation_offre'] === 0) ? 1 : 0;
                    $elab_contrat = ($resultat['elaboration_contrat'] === 1 || $resultat['elaboration_contrat'] === 0) ? 1 : 0;
                    $control_final = ($resultat['control_final'] === 1 || $resultat['control_final'] === 0) ? 1 : 0;
                    $percent = $resultat['negoce'] + $val_offre + $resultat['info_client'] + $elab_contrat + $resultat['transmition_contrat_client'] + $resultat['finalisation_dossier'] + $control_final + $resultat['validation_dossier'] + $resultat['transmition_contrat_remise'] + $resultat['transmition_decharge'] + $resultat['reception_dossier'] + $resultat['archivage'];
                    $percent = $percent * 100 / 12;
                    if ($percent == 100) {
                        $maj = $bdd->prepare("UPDATE mode_operatoire SET etat = 'Actif' WHERE id = ?");
                        $maj->execute(array($resultat['id_operatoire']));
                        $maj->closeCursor();
                    }
                }
            }
            $verif->closeCursor();

            header("location: index.php?maj=Checkliste mis à jour ✅#main3");
            exit;
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }else{
        header('location: index.php?maj=Erreur lors de la mise à jour de la checkliste#main3');
    }
}else{
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: ../index.php');
}
