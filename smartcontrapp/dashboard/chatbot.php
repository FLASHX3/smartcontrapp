<?php
header('Content-Type: application/json');
require_once('db.php');
$anneeActuelle = date('Y');

// Récupérer les données envoyées en POST
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'];
$botResponse = '';

// Logique simple pour répondre aux messages
if (strpos($userMessage, 'Bonjour') !== false) {
    $botResponse = 'Salut! Comment puis-je vous aider aujourd\'hui?';
} else if ($userMessage == "Dans quelle ville il y a plus de contrats actifs?") {
    try {
        $requete = $bdd->prepare("SELECT ville, COUNT(*) AS nombre_de_contrats FROM mode_operatoire WHERE etat = 'Actif' GROUP BY ville ORDER BY nombre_de_contrats DESC LIMIT 1");
        $requete->execute();
        $resultat = $requete->fetch();
        $requete->closeCursor();

        if ($resultat) {
            $nombreAleatoire = mt_rand(0, 5);
            switch ($nombreAleatoire) {
                case 0:
                    $botResponse = "La ville avec le plus de contrats actif est " . $resultat['ville'] . " avec " . $resultat['nombre_de_contrats'] . " contrats actif.";
                    break;
                case 1:
                    $botResponse = $resultat['ville'] . " est la ville ayant le plus de contrats actifs, avec un total de " . $resultat['nombre_de_contrats'] . " contrats actifs.";
                    break;
                case 2:
                    $botResponse = "La ville comptant le plus grand nombre de contrats actifs est " . $resultat['ville'] . ", avec " . $resultat['nombre_de_contrats'] . " contrats actifs.";
                    break;
                case 3:
                    $botResponse = "Avec " . $resultat['nombre_de_contrats'] . " contrats actifs, " . $resultat['ville'] . " est la ville qui en a le plus.";
                    break;
                case 4:
                    $botResponse = $resultat['ville'] . ", avec ses " . $resultat['nombre_de_contrats'] . " contrats actifs, est la ville en tête des contrats actifs.";
                    break;
                case 5:
                    $botResponse = "La ville de " . $resultat['ville'] . " se distingue avec le plus de contrats actifs, totalisant " . $resultat['nombre_de_contrats'] . " contrats actifs.";
                    break;
                case 6:
                    $botResponse = $resultat['ville'] . " possède le nombre le plus élevé de contrats actifs, s'élevant à " . $resultat['nombre_de_contrats'] . ".";
                    break;

                default:
                    $botResponse = "La ville avec le plus de contrats actif est " . $resultat['ville'] . " avec " . $resultat['nombre_de_contrats'] . " contrats actif.";
                    break;
            }
        } else {
            $botResponse = "Aucune donnée disponible.";
        }
    } catch (PDOException $e) {
        $botResponse = 'Erreur lors de l\'exécution de la requête SQL: ' . $e->getMessage();
    }
} else if ($userMessage == "Quel site a le plus de contrats actifs?") {
    try {
        $requete = $bdd->prepare("SELECT site, COUNT(*) AS nombre_de_contrats FROM mode_operatoire WHERE etat = 'Actif' GROUP BY site ORDER BY nombre_de_contrats DESC LIMIT 1");
        $requete->execute();
        $resultat = $requete->fetch();
        $requete->closeCursor();

        if ($resultat) {
            $nombreAleatoire = mt_rand(0, 5);
            switch ($nombreAleatoire) {
                case 0:
                    $botResponse = "Le site avec le plus de contrats actif est " . $resultat['site'] . " avec " . $resultat['nombre_de_contrats'] . " contrats actif.";
                    break;
                case 1:
                    $botResponse = $resultat['site'] . " est le site ayant le plus de contrats actifs, avec un total de " . $resultat['nombre_de_contrats'] . " contrats actifs.";
                    break;
                case 2:
                    $botResponse = "Le site comptant le plus grand nombre de contrats actifs est " . $resultat['site'] . ", avec " . $resultat['nombre_de_contrats'] . " contrats actifs.";
                    break;
                case 3:
                    $botResponse = "Avec " . $resultat['nombre_de_contrats'] . " contrats actifs, " . $resultat['site'] . " est le site qui en a le plus.";
                    break;
                case 4:
                    $botResponse = $resultat['site'] . ", avec ses " . $resultat['nombre_de_contrats'] . " contrats actifs, est le site en tête des contrats actifs.";
                    break;
                case 5:
                    $botResponse = "Le site de " . $resultat['site'] . " se distingue avec le plus de contrats actifs, totalisant " . $resultat['nombre_de_contrats'] . " contrats actifs.";
                    break;
                case 6:
                    $botResponse = $resultat['site'] . " possède le nombre le plus élevé de contrats actifs, s'élevant à " . $resultat['nombre_de_contrats'] . ".";
                    break;

                default:
                    $botResponse = "Le site avec le plus de contrats actif est " . $resultat['site'] . " avec " . $resultat['nombre_de_contrats'] . " contrats actif.";
                    break;
            }
        } else {
            $botResponse = "Aucune donnée disponible.";
        }
    } catch (PDOException $e) {
        $botResponse = 'Erreur lors de l\'exécution de la requête SQL: ' . $e->getMessage();
    }
} else if ($userMessage == "Quel est le pourcentage de résiliation en cette année?") {
    try {
        $requete = $bdd->prepare("SELECT (COUNT(r.id_resiliation) * 100.0 / c.total_contrats) AS pourcentage_resiliation FROM (SELECT COUNT(*) AS total_contrats FROM mode_operatoire) c LEFT JOIN resiliation r ON YEAR(r.date_resiliation) = ?");
        $requete->execute(array($anneeActuelle));
        $resultat = $requete->fetch();
        $requete->closeCursor();

        if ($resultat) {
            $nombreAleatoire = mt_rand(0, 5);
            switch ($nombreAleatoire) {
                case 0:
                    $botResponse = "Le pourcentage de résiliation cette année est de " . $resultat['pourcentage_resiliation'] . "%";
                    break;
                case 1:
                    $botResponse = "Cette année, le taux de résiliation est de " . $resultat['pourcentage_resiliation'] . "%.";
                    break;
                case 2:
                    $botResponse = "Le taux de résiliation pour cette année s'élève à " . $resultat['pourcentage_resiliation'] . "%.";
                    break;
                case 3:
                    $botResponse = "En $anneeActuelle, le pourcentage de résiliation atteint " . $resultat['pourcentage_resiliation'] . "%.";
                    break;
                case 4:
                    $botResponse = "Le pourcentage des résiliations cette année est de " . $resultat['pourcentage_resiliation'] . "%.";
                    break;
                case 5:
                    $botResponse = "Cette année, " . $resultat['pourcentage_resiliation'] . "% des contrats ont été résiliés.";
                    break;
                case 6:
                    $botResponse = "La résiliation des contrats cette année représente " . $resultat['pourcentage_resiliation'] . "%.";
                    break;

                default:
                    $botResponse = "Le pourcentage de résiliation cette année est de " . $resultat['pourcentage_resiliation'] . "%";
                    break;
            }
        } else {
            $botResponse = "Aucune donnée disponible.";
        }
    } catch (PDOException $e) {
        $botResponse = 'Erreur lors de l\'exécution de la requête SQL: ' . $e->getMessage();
    }
} else if ($userMessage == "Quel site ramène plus de bénéfice ctte année?") {
    try {
        $requete = $bdd->prepare("SELECT site, SUM(loyer_mensuel) AS total_benefice FROM mode_operatoire, adhesion WHERE YEAR(date_debut_contrat) = ? AND etat = 'Actif' GROUP BY site ORDER BY total_benefice DESC LIMIT 1");
        $requete->execute(array($anneeActuelle));
        $resultat = $requete->fetch();
        $requete->closeCursor();

        if ($resultat) {
            $nombreAleatoire = mt_rand(0, 5);
            switch ($nombreAleatoire) {
                case 0:
                    $botResponse = $resultat['site'] . " est le site qui rapporte le plus de bénéfice cette année avec un total d'entrée  de " . $resultat['total_benefice'] . "frcs";
                    break;
                case 1:
                    $botResponse = "Le site qui génère le plus de bénéfices cette année est " . $resultat['site'] . ", avec un total de " . $resultat['total_benefice'] . " francs de revenus.";
                    break;
                case 2:
                    $botResponse = "Avec un bénéfice total de " . $resultat['total_benefice'] . " francs, " . $resultat['site'] . " est le site le plus rentable cette année.";
                    break;
                case 3:
                    $botResponse = "Cette année, " . $resultat['site'] . " a rapporté le plus de bénéfices, atteignant " . $resultat['total_benefice'] . " francs.";
                    break;
                case 4:
                    $botResponse = $resultat['site'] . " est en tête des sites les plus rentables cette année, avec des revenus de " . $resultat['total_benefice'] . " francs.";
                    break;
                case 5:
                    $botResponse = "Le site " . $resultat['site'] . " enregistre le plus grand bénéfice de l'année avec un total de " . $resultat['total_benefice'] . " francs.";
                    break;
                case 6:
                    $botResponse = "Cette année, " . $resultat['site'] . " se distingue par le plus haut bénéfice, totalisant " . $resultat['total_benefice'] . " francs.";
                    break;

                default:
                    $botResponse = $resultat['site'] . " est le site qui rapporte le plus de bénéfice cette année avec un total d'entrée  de " . $resultat['total_benefice'] . "frcs";;
                    break;
            }
        } else {
            $botResponse = "Aucune donnée disponible.";
        }
    } catch (PDOException $e) {
        $botResponse = 'Erreur lors de l\'exécution de la requête SQL: ' . $e->getMessage();
    }
} else if ($userMessage == "En quelle année a-t-on eu le plus de client?") {
    try {
        $requete = $bdd->prepare("SELECT YEAR(date_debut_contrat) AS annee, COUNT(DISTINCT nom_locataire) AS nombre_de_clients FROM mode_operatoire GROUP BY YEAR(date_debut_contrat) ORDER BY nombre_de_clients DESC LIMIT 1");
        $requete->execute();
        $resultat = $requete->fetch();
        $requete->closeCursor();

        if ($resultat) {
            $nombreAleatoire = mt_rand(0, 5);
            switch ($nombreAleatoire) {
                case 0:
                    $botResponse = $resultat['annee'] . " est l'année où l'on a eu le plus de client, à savoir  " . $resultat['nombre_de_clients'] . " clients";
                    break;
                case 1:
                    $botResponse = "En " . $resultat['annee'] . ", nous avons atteint le nombre record de " . $resultat['nombre_de_clients'] . " clients.";
                    break;
                case 2:
                    $botResponse = "L'année " . $resultat['annee'] . " a enregistré le plus grand nombre de clients, avec un total de " . $resultat['nombre_de_clients'] . ".";
                    break;
                case 3:
                    $botResponse = "Nous avons eu le plus de clients en " . $resultat['annee'] . ", avec un total de " . $resultat['nombre_de_clients'] . " clients.";
                    break;
                case 4:
                    $botResponse = $resultat['annee'] . " est l'année où nous avons accueilli le plus grand nombre de clients, soit " . $resultat['nombre_de_clients'] . ".";
                    break;
                case 5:
                    $botResponse = "Avec " . $resultat['nombre_de_clients'] . " clients, " . $resultat['annee'] . " est l'année où nous avons eu la plus forte clientèle.";
                    break;
                case 6:
                    $botResponse = "L'année où nous avons eu le plus de clients est " . $resultat['annee'] . ", avec un total de " . $resultat['nombre_de_clients'] . " clients.";
                    break;

                default:
                    $botResponse = $resultat['annee'] . " est l'année où l'on a eu le plus de client, à savoir  " . $resultat['nombre_de_clients'] . " clients";
                    break;
            }
        } else {
            $botResponse = "Aucune donnée disponible.";
        }
    } catch (PDOException $e) {
        $botResponse = 'Erreur lors de l\'exécution de la requête SQL: ' . $e->getMessage();
    }
} else if ($userMessage == "Quels sont les dossiers en cours d'adhesion?") {
    try {
        $requete = $bdd->prepare("SELECT id, nom_locataire FROM mode_operatoire WHERE etat = 'En-cours'");
        $requete->execute();
        $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
        $requete->closeCursor();

        if ($resultat) {
            $botResponse = "Liste des contrats en-cours d'adhésion: \n";
            foreach ($resultat as $key) {
                $botResponse .= "\n№: " . $key['id'] . ". " . $key['nom_locataire'] . ";\n";
            }
        } else {
            $botResponse = "Aucune donnée disponible.";
        }
    } catch (PDOException $e) {
        $botResponse = 'Erreur lors de l\'exécution de la requête SQL: ' . $e->getMessage();
    }
} else if ($userMessage == "Quel GI gère le site le plus actif?") {
    try {
        $requete = $bdd->prepare("SELECT nom_GI FROM mode_operatoire WHERE site = (SELECT site FROM mode_operatoire WHERE etat = 'Actif' GROUP BY site ORDER BY COUNT(*) DESC LIMIT 1)LIMIT 1");
        $requete->execute();
        $resultat = $requete->fetch();
        $requete->closeCursor();

        if ($resultat) {
            $nombreAleatoire = mt_rand(0, 5);
            switch ($nombreAleatoire) {
                case 0:
                    $botResponse = $resultat['nom_GI'] . " est le GI qui gère le site le plus actif";
                    break;
                case 1:
                    $botResponse = "Le site le plus actif est géré par " . $resultat['nom_GI'] . ", le GI.";
                    break;
                case 2:
                    $botResponse = $resultat['nom_GI'] . ", le GI, est responsable du site le plus actif.";
                    break;
                case 3:
                    $botResponse = "Le GI " . $resultat['nom_GI'] . " administre le site le plus actif.";
                    break;
                case 4:
                    $botResponse = $resultat['nom_GI'] . " est le gestionnaire immobilier du site le plus actif.";
                    break;
                case 5:
                    $botResponse = "Le site le plus fréquenté est sous la gestion de " . $resultat['nom_GI'] . ", le GI.";
                    break;
                case 6:
                    $botResponse = "La gestion du site le plus actif est assurée par " . $resultat['nom_GI'] . ", le GI.";
                    break;

                default:
                    $botResponse = $resultat['nom_GI'] . " est le GI qui gère le site le plus actif";
                    break;
            }
        } else {
            $botResponse = "Aucune donnée disponible.";
        }
    } catch (PDOException $e) {
        $botResponse = 'Erreur lors de l\'exécution de la requête SQL: ' . $e->getMessage();
    }
} else {
    $botResponse = "Je ne suis pas sûr de comprendre. Pouvez-vous s'il vous plaît reformuler ?";
}

echo json_encode(['response' => $botResponse]);
