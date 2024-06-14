<?php
session_start();

if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin" || $_SESSION['type'] == "user") {

    $valid_columns = ['column1', 'column2', 'column3']; // replace with your actual column names
    $tri = isset($_GET['tri']) ? $_GET['tri'] : null;

    if ($tri == null) {
        header('Location: indexp.php?tri=Erreur lors du tri');
        exit();
    }

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

    // Assuming $tri is validated and safe to use
    $req = $bdd->prepare("SELECT mode_operatoire.*, adhesion.*, resiliation.* 
                          FROM mode_operatoire 
                          LEFT JOIN adhesion ON mode_operatoire.id = adhesion.id_operatoire 
                          LEFT JOIN resiliation ON mode_operatoire.id = resiliation.id_mode 
                          ORDER BY $tri");
    $req->execute();
    $res = $req->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
} else {
    require_once('log.php');
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('Location: ../index.php');
    exit();
}
