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

    $requete = $pdo->prepare("SELECT
    ville,
    COUNT(*) AS nombre_contrats
    FROM
        mode_operatoire
    WHERE
        etat = 'Actif' OR etat = 'En-cours'
    GROUP BY
        ville
    ORDER BY
        ville
    ");

    $requete->execute();

    // Récupérer et afficher les résultats
    $results = $requete->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Nombre de contrat par Ville</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Contenu de la page -->

    <body>
        <div style="height: 100vh; width: 100%;">
            <canvas id="myChart" style="width: 100%; margin: auto"></canvas>
        </div>
        <script>
            var data = <?php echo $jsonResults; ?>;
            //console.log(data);

            // Initialiser les tableaux
            var ville = [];
            var nombreContrats = [];

            // Parcourir les données et les enregistrer dans les tableaux
            data.forEach(function(item) {
                ville.push(item.ville);
                nombreContrats.push(item.nombre_contrats);
            });

            // Afficher les tableaux dans la console (ou les utiliser comme vous le souhaitez)
            // console.log(ville);
            // console.log(nombreContrats);
            document.addEventListener('DOMContentLoaded', (event) => {
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'pie', // Type de graphique (bar, line, pie, etc.)
                    data: {
                        labels: ville, // Labels des données
                        datasets: [{
                            label: 'Nombre de contrats',
                            data: nombreContrats, // Données du graphique
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Répartition des contrats par Ville'
                            }
                        }
                    }
                });
            });
        </script>
    </body>

</body>

</html>