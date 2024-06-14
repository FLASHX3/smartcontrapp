<?php
try {
    // Connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=gestcontrapp;charset=utf8';
    $username = 'root';
    $password = '';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $options);

    // Définir la langue pour les noms de mois en français
    $pdo->exec("SET lc_time_names = 'fr_FR';");

    // Préparer et exécuter la requête
    $stmt = $pdo->prepare("SELECT
            CONCAT(MONTHNAME(date_debut_contrat), ' ', YEAR(date_debut_contrat)) AS mois_annee,
            COUNT(*) AS nombre_contrats
        FROM
            mode_operatoire
        GROUP BY
            YEAR(date_debut_contrat),
            MONTH(date_debut_contrat)
        ORDER BY
            YEAR(date_debut_contrat),
            MONTH(date_debut_contrat)
    ");
    $stmt->execute();

    // Récupérer et afficher les résultats
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $jsonResults = json_encode($results);
} catch (PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nombre de contrat par mois</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Contenu de la page -->

    <body>
        <div style="height: 100vh; width: 100%; display: flex; justify-content: center; align-items: center;">
            <canvas id="myChart"></canvas>
        </div>
        <script>
            var data = <?php echo $jsonResults; ?>;
            //console.log(data);

            // Initialiser les tableaux
            var moisAnnee = [];
            var nombreContrats = [];

            // Parcourir les données et les enregistrer dans les tableaux
            data.forEach(function(item) {
                moisAnnee.push(item.mois_annee);
                nombreContrats.push(item.nombre_contrats);
            });

            // Afficher les tableaux dans la console (ou les utiliser comme vous le souhaitez)
            // console.log(moisAnnee);
            // console.log(nombreContrats);
            document.addEventListener('DOMContentLoaded', (event) => {
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'line', // Spécifiez 'line' et utilisez 'fill' pour remplir l'aire
                    data: {
                        labels: moisAnnee,
                        datasets: [{
                            label: 'Nombre de contrats',
                            data: nombreContrats,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Couleur de remplissage
                            borderColor: 'rgba(75, 192, 192, 1)', // Couleur de la ligne
                            borderWidth: 1,
                            fill: true // Remplir l'aire sous la ligne
                        }]
                    },
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: 'Nombre de contrat par mois'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    </body>

</body>

</html>