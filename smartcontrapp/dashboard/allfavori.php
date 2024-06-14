<?php
require_once('log.php');

$etat = isset($_GET['etat']) ? strip_tags(htmlspecialchars($_GET['etat'])) : null;

if ($etat == null) {
    echo json_encode(array("error" => "Erreur lors de l'affichage des favoris"));
}

try {
    $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Errerur ' . $e->getMessage());
}
$req = "";

if ($etat == "off") {
    $req = "SELECT mode_operatoire.*, adhesion.*, resiliation.* FROM mode_operatoire LEFT JOIN adhesion on mode_operatoire.id = adhesion.id_operatoire LEFT JOIN resiliation ON mode_operatoire.id = resiliation.id_mode WHERE mode_operatoire.favori = 1 ORDER BY mode_operatoire.id DESC";
} else if ($etat == "actif") {
    $req = $reqSearch = "SELECT mode_operatoire.*,adhesion.*, resiliation.* FROM mode_operatoire LEFT JOIN adhesion on mode_operatoire.id = adhesion.id_operatoire LEFT JOIN resiliation ON mode_operatoire.id = resiliation.id_mode ORDER BY mode_operatoire.id DESC";
}
$requetefav = $bdd->prepare($req);
$requetefav->execute();

$res = $requetefav->fetchAll(PDO::FETCH_ASSOC);

// var_dump($res);
echo json_encode($res);
