<?php
session_start();
$serveurname = "localhost";
$username = "root";
$password = "";
$dbname = "gestcontrapp";

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {

    $site = isset($_GET['site'])? strip_tags(htmlspecialchars($_GET['site'])) : null;

    if ($site == null ) {
        header('location: index.php?modif=Erreur lors de la mise à jour des entités :#main1');
    }

    try {
        $bdd = new PDO("mysql:host=$serveurname;dbname=$dbname;charset=utf8", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        die('Erreur : ' . $e->getMessage());
    }

    $req = $bdd->prepare("SELECT `entite`, `ville`, `logement` FROM `immeubles_entites` WHERE `site`= ? ORDER BY logement");
    $req->execute(array($site));
    $res = $req->fetchAll(PDO::FETCH_ASSOC);
    $nb = $req->rowCount();
    
    if($nb != 0){
        // var_dump($res);
        echo json_encode($res);
    }else{
        
    }

}