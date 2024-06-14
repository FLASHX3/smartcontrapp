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

    // Préparer et exécuter la requête
    $stmt = $pdo->prepare("SELECT users.nom, COUNT(mode_operatoire.id) AS nombre_contrats FROM users LEFT JOIN mode_operatoire ON users.nom = mode_operatoire.nom_GI WHERE type = 'user' GROUP BY users.id");
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
    <title>Contrat par GI</title>
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
            // console.log(data);

            // Initialiser les tableaux
            var nomGI = [];
            var nombreContrats = [];
            data.forEach(function(item) {
                nomGI.push(item.nom);
                nombreContrats.push(item.nombre_contrats);
            });
            // console.log(nomGI);
            // console.log(nombreContrats);
            document.addEventListener('DOMContentLoaded', (event) => {
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar', // Type de graphique (bar, line, pie, etc.)
                    data: {
                        labels: nomGI, // Labels des données
                        datasets: [{
                            label: 'Nombres de contrats',
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
                                text: 'Répartition des contrats par GI'
                            }
                        },
                        scales: {
                            y: {
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