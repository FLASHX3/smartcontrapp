<?php
require_once('log.php');

$serveurname = "localhost";
$username = "root";
$password = "";
$dbname = "gestcontrapp";

$id = isset($_GET["id"]) ? strip_tags(htmlspecialchars($_GET["id"])) : null;

if ($id == null) {
    header("Location: index.php?maj=Erreur lors de la révision du contrat № {$id}#main3");
    exit();
}

try {
    $bdd = new PDO("mysql:host=$serveurname;dbname=$dbname;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

$req = $bdd->prepare("SELECT loyer_mensuel, revision_loyer, taux_revision, date_revision FROM mode_operatoire WHERE id = ?");
$req->execute(array($id));
$res = $req->fetch(PDO::FETCH_ASSOC);

if ($res) {
    $loyer_mensuel = $res['loyer_mensuel'] * $res['taux_revision'] + $res['loyer_mensuel'];

    $dateRevisionStr = $res['date_revision'];
    $dateRevision = new DateTime($dateRevisionStr);
    switch ($res['revision_loyer']) {
        case 'Annuelle':
            $dateRevision->modify('+1 year');
            break;
        case 'Biennale':
            $dateRevision->modify('+2 year');
            break;
        case 'Triennale':
            $dateRevision->modify('+3 year');
            break;
        default:
            echo "date inconnu!";
            break;
    }
   
    // Formater la date modifiée
    $newDaterevision = $dateRevision->format('Y-m-d');
    
    $update = $bdd->prepare("UPDATE mode_operatoire SET loyer_mensuel = ?, date_revision = ? WHERE id = ?");
    $update->execute(array($loyer_mensuel, $newDaterevision, $id));

    setlog($_SESSION['id'], 11, "Révision du dossier № $id");
    header("location: index.php?maj=La révision du dossier № $id a reussit#main3");
} else {
    echo "Aucun résultat trouvé pour l'ID spécifié.";
}

