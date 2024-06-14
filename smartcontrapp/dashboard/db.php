<?php
$serveurname = "localhost";
$username = "root";
$password = "";
$dbname = "gestcontrapp";

try {
    $bdd = new PDO("mysql:host=$serveurname;dbname=$dbname;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    echo json_encode(['response' => 'Erreur de connexion à la base de données: ' . $e->getMessage()]);
    exit;
}

