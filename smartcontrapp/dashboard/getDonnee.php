<?php

session_start();
$serveurname = "localhost";
$username = "root";
$password = "";
$dbname = "gestcontrapp";

$id = isset($_GET["id"]) ? strip_tags(htmlspecialchars($_GET["id"])) : null;

if ($id == null) {
    header('location: index.php?modif=Erreur lors de la recuperation des donnée#main2');
}

try {
    $bdd = new PDO("mysql:host=$serveurname;dbname=$dbname;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

$requete = $bdd->prepare(("SELECT * FROM mode_operatoire WHERE id = ?"));
$requete->execute(array($id));

$res = $requete->fetchAll(PDO::FETCH_ASSOC);

// var_dump($res);
echo json_encode($res);
