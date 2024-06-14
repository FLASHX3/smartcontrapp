<?php
require_once('log.php');
if (isset($_SESSION["id"]) && $_SESSION['id'] != 0) {
    echo "<script>var sessionType ='" . $_SESSION['type'] . "';console.log(sessionType)</script>";

    if (!isset($_POST["submit1"])) {
        $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        //main1
        $requeteVille = $bdd->prepare("SELECT DISTINCT ville FROM immeubles_entites ORDER BY ville ASC");
        $requeteVille->execute();
        $villes = $requeteVille->fetchAll(PDO::FETCH_ASSOC);

        $requeteSite = $bdd->prepare("SELECT DISTINCT site FROM immeubles_entites ORDER BY site ASC");
        $requeteSite->execute();
        $sites = $requeteSite->fetchAll(PDO::FETCH_ASSOC);

        $requeteEntite = $bdd->prepare("SELECT DISTINCT entite FROM immeubles_entites ORDER BY entite ASC");
        $requeteEntite->execute();
        $entites = $requeteEntite->fetchAll(PDO::FETCH_ASSOC);

        $requeteGi = $bdd->prepare("SELECT nom FROM users WHERE type = 'user' ORDER BY nom");
        $requeteGi->execute();
        $Gis = $requeteGi->fetchAll(PDO::FETCH_ASSOC);

        //main3
        $recherche = "SELECT mode_operatoire.*, `adhesion`.* FROM `mode_operatoire`, `adhesion` WHERE `mode_operatoire`.`id` = `adhesion`.`id_operatoire` AND (`mode_operatoire`.`etat` = \"En-cours\" OR `mode_operatoire`.`etat` = \"Actif\")";
        $adhesion = $bdd->prepare($recherche);
        $adhesion->execute();
        $data = $adhesion->fetchAll(PDO::FETCH_ASSOC);

        //main4
        $requeteResiliation = "SELECT mode_operatoire.nom_locataire, mode_operatoire.ville, mode_operatoire.logement, mode_operatoire.numero_dossier, mode_operatoire.nom_GI, mode_operatoire.site, `resiliation`.* FROM `mode_operatoire`, `resiliation` WHERE `mode_operatoire`.`id` = `resiliation`.`id_mode`";
        $resiliation = $bdd->prepare($requeteResiliation);
        $resiliation->execute();
        $dataResiliation = $resiliation->fetchAll(PDO::FETCH_ASSOC);

        //main5
        if ($_SESSION['type'] == 'admin' || $_SESSION['type'] == 'super admin') {
            $reqactif = $bdd->prepare("SELECT COUNT(*) as nbactif FROM mode_operatoire WHERE etat = 'Actif'");
            $reqactif->execute();
            $actif = $reqactif->fetchAll(PDO::FETCH_ASSOC);

            $reqresilie = $bdd->prepare("SELECT COUNT(*) as nbresilie FROM mode_operatoire WHERE etat = 'Résilié'");
            $reqresilie->execute();
            $resilie = $reqresilie->fetchAll(PDO::FETCH_ASSOC);

            $reqadhesion = $bdd->prepare("SELECT COUNT(*) as nbadhesion FROM mode_operatoire WHERE etat = 'En-cours'");
            $reqadhesion->execute();
            $adhesion = $reqadhesion->fetchAll(PDO::FETCH_ASSOC);

            $reqresiliation = $bdd->prepare("SELECT COUNT(*) as nbresiliation FROM mode_operatoire WHERE etat = 'En-Résiliation'");
            $reqresiliation->execute();
            $resiliation = $reqresiliation->fetchAll(PDO::FETCH_ASSOC);

            $reqfavori = $bdd->prepare("SELECT COUNT(*) as nbfavori FROM mode_operatoire WHERE favori = 1");
            $reqfavori->execute();
            $favori = $reqfavori->fetchAll(PDO::FETCH_ASSOC);

            //main7
            $requeteUser = $bdd->prepare("SELECT users.*, COUNT(mode_operatoire.id) AS nombre_contrats FROM users LEFT JOIN mode_operatoire ON users.nom = mode_operatoire.nom_GI GROUP BY users.id");
            $requeteUser->execute();
            $users = $requeteUser->fetchAll(PDO::FETCH_ASSOC);
        }

?>

        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../css/dashboard.css">
            <link rel="shortcut icon" href="../images/logo_sci_sotradic.png" type="image/x-icon">
            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
            <script src="../javascript/dashboard.js" defer></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js" defer></script> <!--bibliothèque d'exportation Excel-->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js" defer></script> <!--bibliothèque d'exportation JsPDF-->
            <title>Dashboard</title>
        </head>

        <body>
            <div id="container">
                <nav>
                    <h1><a href="index.php#main2"><ion-icon name="grid-outline"></ion-icon>SmartContrApp</a></h1>
                    <img src="../images/logo_sci_sotradic_2.png" alt="logosotradic" srcset="">
                    <div id="nav">
                        <ul>
                            <li onclick="activeLi(this);"><a href="#main1"> <ion-icon name="add-outline"></ion-icon> Ajouter un contrat</a></li>
                            <li onclick="activeLi(this);"><a href="#main2" class="on"> <ion-icon name="clipboard-outline"></ion-icon> Synthèse des contrats</a></li>
                            <li onclick="activeLi(this);"><a href="#main3"> <ion-icon name="bar-chart-outline"></ion-icon> Evolution des contrats </a></li>
                            <li onclick="activeLi(this);"><a href="#main4"> <ion-icon name="document-text-outline"></ion-icon> Contrats Résiliés </a></li>
                            <?php
                            if ($_SESSION['type'] == "super admin" || $_SESSION['type'] == "admin") {
                            ?>
                                <li onclick="activeLi(this);"><a href="#main5"> <ion-icon name="podium-outline"></ion-icon> Statistiques </a></li>
                            <?php
                            }
                            if ($_SESSION['type'] == "super admin") {
                            ?>
                                <li onclick="activeLi(this);"><a href="#main6"> <ion-icon name="desktop-outline"></ion-icon> Audit du système</a></li>
                            <?php
                            }
                            if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin") {
                            ?>
                                <li onclick="activeLi(this);"><a href="#main7"> <ion-icon name="person-circle-outline"></ion-icon> Administration</a></li>
                            <?php
                            }
                            ?>
                            <li><a href="deconnexion.php"><ion-icon name="log-out-outline"></ion-icon>Se deconnecter</a></li>
                        </ul>
                    </div>
                </nav>
                <aside>
                    <div id="main1" class="main">
                        <form action="index.php" method="post" onsubmit="return veriform(this);">
                            <h2>Mode Opératoire</h2>
                            <span class="message">Les champs avec le symboles * sont obligatoires</span>
                            <div class="progressbar">
                                <div class="progress" id="progress"></div>
                                <div class="progress-step progress-step-active" data-title="Loyer"></div>
                                <div class="progress-step" data-title="Client"></div>
                                <div class="progress-step" data-title="Prix"></div>
                                <div class="progress-step" data-title="Details"></div>
                                <div class="progress-step" data-title="Finition"></div>
                            </div>
                            <div id="wrapper-list">
                                <div class="wrapper" id="wrapper1">
                                    <div class="input-group">
                                        <label for="site">Site <span class="obligatoire">*</span></label>
                                        <select name="site" id="site" required>
                                            <option disabled>Choisissez un site</option>
                                            <?php foreach ($sites as $site) {
                                            ?>
                                                <option value="<?= $site['site'] ?>"><?= $site['site'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <label for="entite">Entité <span class="obligatoire">*</span></label>
                                        <select name="entite" id="entite" required onchange="verifEntite(this, '#erreurEntite')" onblur="verifEntite(this, '#erreurEntite')">
                                            <option disabled>Choisissez une entité</option>
                                            <?php foreach ($entites as $entite) {
                                            ?>
                                                <option value="<?= $entite['entite'] ?>"><?= $entite['entite'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span id="erreurEntite" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="ville">Ville <span class="obligatoire">*</span></label>
                                        <select name="ville" id="ville" required>
                                            <option disabled>Choisissez une ville</option>
                                            <?php foreach ($villes as $ville) {
                                            ?>
                                                <option value="<?= $ville['ville'] ?>"><?= $ville['ville'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <label for="natbail">Nature du bail <span class="obligatoire">*</span></label>
                                        <select name="natbail" id="natbail" required>
                                            <option disabled>Choisissez la nature du bail</option>
                                            <option value="Commercial">Commercial</option>
                                            <option value="Habitation">Habitation</option>
                                            <option value="Professionnel">Professionnel</option>
                                        </select>
                                    </div>
                                    <div class="">
                                        <a href="#" id="next1" class="btn btn-next width-50 ml-auto">Next<ion-icon name="chevron-forward" size="large"></ion-icon></a>
                                    </div>
                                </div>
                                <div class="wrapper" id="wrapper2">
                                    <div class="input-group">
                                        <label for="nom">Nom locataire <span class="obligatoire">*</span></label>
                                        <input type="text" name="nom" id="nom" placeholder="moins de 25 caractères" required onblur="verifNom(this,'#erreurNom');">
                                        <span id="erreurNom" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="contact">Contact <span class="obligatoire">*</span></label>
                                        <input type="tel" name="contact" id="contact" placeholder="Téléphone du client ex: 699887766" required onblur="verifContact(this,'#erreurContact')">
                                        <span id="erreurContact" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group" id="logement-container">
                                        <label for="logement">Logement / Boutique / Référence du lieu <span class="obligatoire">*</span></label>
                                        <select name="logement" id="logement" required onblur="verifChampVide(this,'#erreurLogement')">
                                            <option disabled>Choisissez un logement</option>
                                            <option value="autres">Autres</option>
                                        </select>
                                        <span id="erreurLogement" style="color:red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="time_c">Durée contrat (en mois) <span class="obligatoire">*</span></label>
                                        <input type="number" min="0" name="time_c" id="time_c" placeholder="Durée du contrat ex : 12" required onblur="verifChampVide(this, '#erreurDuree');">
                                        <span id="erreurDuree" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="btns-group">
                                        <a href="#" class="btn btn-prev" id="prev1"><ion-icon name="chevron-back" size="large"></ion-icon>Previous</a>
                                        <a href="#" class="btn btn-next" id="next2">Next<ion-icon name="chevron-forward" size="large"></ion-icon></a>
                                    </div>
                                </div>
                                <div class="wrapper" id="wrapper3">
                                    <div class="input-group">
                                        <label for="loy_mens">Loyer mensuel (en fcfa) <span class="obligatoire">*</span></label>
                                        <input type="number" min="0" name="loy_mens" id="loy_mens" placeholder="Entrez le montant du loyer mensuel" required onblur="verifChampVide(this, '#erreurLoyer');">
                                        <span id="erreurLoyer" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="freq_paie">Fréquence de paiement <span class="obligatoire">*</span></label>
                                        <select name="freq_paie" id="freq_paie" required>
                                            <option disabled>Choisissez une fréquence</option>
                                            <option value="Annuelle">Annuelle</option>
                                            <option value="Semestrielle">Semestrielle</option>
                                            <option value="Trimestrielle">Trimestrielle</option>
                                            <option value="Autre">Autre</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <label for="mode_paie">Mode de paiement <span class="obligatoire">*</span></label>
                                        <select name="mode_paie" id="mode_paie" required>
                                            <option disabled>Choisissez un mode de paiement</option>
                                            <option value="Chèque">Chèque</option>
                                            <option value="Carte">Carte</option>
                                            <option value="Espèces">Espèces</option>
                                            <option value="Momo">MoMo</option>
                                            <option value="OM">OM</option>
                                            <option value="Virement">Virement</option>
                                            <option value="Tous les modes" selected>Tous les modes</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <label for="nb_mois_paye">Nombre de mois payé <span class="obligatoire">*</span></label>
                                        <input type="number" min="0" name="nb_mois_paye" id="nb_mois_paye" placeholder="Entrez le nombre de mois payé" required onblur="verifChampVide(this, '#erreurNbMois');">
                                        <span id="erreurNbMois" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="btns-group">
                                        <a href="#" class="btn btn-prev" id="prev2"><ion-icon name="chevron-back" size="large"></ion-icon>Previous</a>
                                        <a href="#" class="btn btn-next" id="next3">Next<ion-icon name="chevron-forward" size="large"></ion-icon></a>
                                    </div>
                                </div>
                                <div class="wrapper" id="wrapper4">
                                    <div class="input-group">
                                        <label for="caution">Montant caution (en fcfa) <span class="obligatoire">*</span></label>
                                        <input type="number" min="0" name="caution" id="caution" placeholder="Entrez le montant de la caution" required onblur="verifChampVide(this, '#erreurCaution');">
                                        <span id="erreurCaution" style="color: red; font-size: 14px"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="rev_loyer">Révision loyer <span class="obligatoire">*</span></label>
                                        <select name="rev_loyer" id="rev_loyer" required>
                                            <option disabled>Choisissez un fréquence de révision</option>
                                            <option value="Annuelle">Annuelle</option>
                                            <option value="Biennale">Biennale</option>
                                            <option value="Triennale">Triennale</option>
                                            <option value="Autre">Autre</option>
                                        </select>
                                    </div>
                                    <div class="input-group">
                                        <label for="taux">Taux de révision (en %) <span class="obligatoire">*</span></label>
                                        <input type="number" name="taux_revision" id="taux_revision" value="10" placeholder="Entrez le taux de révision" required onblur="verifChampVide(this, '#erreurRevision');">
                                        <span id="erreurRevision" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="pen_retard">Pénalités de retard (en %) <span class="obligatoire">*</span></label>
                                        <input type="number" name="pen_retard" id="pen_retard" value="7" placeholder="Entrez le pourcentage des pénalités" required onblur="verifChampVide(this, '#erreurPenalite');">
                                        <span id="erreurPenalite" style="color: red; font-size: 14px;"></span>
                                    </div>

                                    <div class="btns-group">
                                        <a href="#" class="btn btn-prev" id="prev3"><ion-icon name="chevron-back" size="large"></ion-icon>Previous</a>
                                        <a href="#" class="btn btn-next" id="next4">Next<ion-icon name="chevron-forward" size="large"></ion-icon></a>
                                    </div>
                                </div>
                                <div class="wrapper" id="wrapper5">
                                    <div class="input-group">
                                        <label for="droit_reg">Droit d'enregistrement (en fcfa) <span class="obligatoire">*</span></label>
                                        <input type="number" min="0" name="droit_reg" id="droit_reg" placeholder="Entrez le montant des droits d'enregistrement" required onblur="verifChampVide(this, '#erreurSave');">
                                        <span id="erreurSave" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="date_start">Date début de contrat <span class="obligatoire">*</span></label>
                                        <input type="date" name="date_start" id="date_start" required onblur="verifChampVide(this, '#erreurDatestart');">
                                        <span id="erreurDatestart" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="date_end">Date fin de contrat <span class="obligatoire">*</span></label>
                                        <input type="date" name="date_end" id="date_end" required onblur="verifChampVide(this, '#erreurDatefin');">
                                        <span id="erreurDatefin" style="color: red; font-size: 14px;"></span>
                                    </div>
                                    <div class="input-group">
                                        <label for="gi">Nom du GI <span class="obligatoire">*</span></label>
                                        <select name="gi" id="gi" required>
                                            <option disabled>Choisissez un GI</option>
                                            <?php
                                            foreach ($Gis as $Gi) {
                                            ?>
                                                <option value="<?= $Gi['nom']; ?>" <?= ($_SESSION['nom'] == $Gi['nom']) ? "selected" : ""; ?>><?= $Gi['nom']; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="btns-group">
                                        <a href="#" class="btn btn-prev" id="prev4"><ion-icon name="chevron-back" size="large"></ion-icon>Previous</a>
                                        <input type="submit" name="submit1" value="Submit" class="btn" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="main2" class="main">
                        <h1><ion-icon name="clipboard"></ion-icon> Synthèse Des Contrats</h1>
                        <div id="actionMain2">
                            <form action="" method="get" id="formMain2">
                                <input type="search" name="searchMain2" id="searchMain2" value="" placeholder="search: tapez au moins 2 lettres" oninput="searchKeyword();">
                                <ion-icon name="search-outline"></ion-icon>
                                <label for="tri">Trier</label>
                                <select name="tri" id="tri">
                                    <option value="id">&#8470;</option>
                                    <option value="site">Site</option>
                                    <option value="entite">Entité</option>
                                    <option value="ville">Ville</option>
                                    <option value="nature_bail">Nature bail</option>
                                    <option value="nom_locataire">Nom locataire</option>
                                    <option value="contact">Contact</option>
                                    <option value="logement">Logement</option>
                                    <option value="duree_contrat">Durée contrat</option>
                                    <option value="loyer_mensuel">Loyer mensuel</option>
                                    <option value="frequence_paiement">Frequence paiement</option>
                                    <option value="mode_paiement">Mode paiement</option>
                                    <option value="nombre_mois">Montant loyer payer</option>
                                    <option value="montant_caution">Montant caution</option>
                                    <option value="revision_loyer">Revision loyer</option>
                                    <option value="pénalites_retard">Pénalités retard</option>
                                    <option value="date_debut_contrat">Date Debut</option>
                                    <option value="date_fin_contrat">Date Fin</option>
                                    <option value="droit_enregistrement">Droit d'enregistrement</option>
                                    <option value="nom_GI">Nom GI</option>
                                    <option value="numero_dossier">Numéro Dossier</option>
                                </select>
                                <button id="btnfav" type="button" data-etat="off" title="Afficher les favoris"><ion-icon name="bookmark-outline"></ion-icon></button>
                                <label for="export">Exporter</label>
                                <button type="button" id="export" onclick='confirmExport();' title="Exporter le tableau"><ion-icon name="download-outline"></ion-icon></button>
                                <div id="customConfirm" class="custom-confirm">
                                    <div class="message"><ion-icon name="print-outline"></ion-icon>Exportez au format : </div><span class="close">&times;</span>
                                    <div id="buttonconfirm">
                                        <button type="button" id="excel" title="Exporter au format excel"><img src="../ressources/logo excel.png" alt="logo excel">EXCEL</button>
                                        <button type="button" id="pdf" title="Exporter au format PDF"><img src="../ressources/logo pdf.jfif" alt="logo pdf">PDF</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table id="synthese">
                            <thead>
                                <tr>
                                    <th>&#8470;</th>
                                    <th>Site</th>
                                    <th>Entité</th>
                                    <th>Ville</th>
                                    <th>Nature bail</th>
                                    <th>Nom locataire</th>
                                    <th>Contact locataire</th>
                                    <th>Logement</th>
                                    <th>Durée_contrat (en mois)</th>
                                    <th>Loyer mensuel (en fcfa)</th>
                                    <th>Frequence paiement</th>
                                    <th>Mode paiement</th>
                                    <th>Montant_loyer payé (en fcfa)</th>
                                    <th>Montant caution (en fcfa)</th>
                                    <th>Revision loyer</th>
                                    <th>Taux révision (en %)</th>
                                    <th>Pénalités de retard (en %)</th>
                                    <th>Date_debut contrat</th>
                                    <th>Date_de_fin contrat</th>
                                    <th>Date_création dossier</th>
                                    <th>Droit d'enregistrement (en fcfa)</th>
                                    <th>Nom GI</th>
                                    <th>Numero dossier</th>
                                    <th>Etat_contrat <span style="font-size:10px">(Cliquez pour voir le contrat)</span></th>
                                    <th>Actions_Rapides </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $affichage = "SELECT mode_operatoire.*, adhesion.*, resiliation.* FROM mode_operatoire LEFT JOIN adhesion on mode_operatoire.id = adhesion.id_operatoire LEFT JOIN resiliation ON mode_operatoire.id = resiliation.id_mode ORDER BY mode_operatoire.id DESC";
                                $requete = $bdd->prepare($affichage);
                                $requete->execute();
                                $nbResultat = $requete->rowCount();
                                if ($nbResultat > 0) {
                                    while ($resultat = $requete->fetch()) {
                                ?>
                                        <tr>
                                            <td><?php echo $resultat['id']; ?></td>
                                            <td><?php echo $resultat['site']; ?></td>
                                            <td><?php echo $resultat['entite']; ?></td>
                                            <td><?php echo $resultat['ville']; ?></td>
                                            <td><?php echo $resultat['nature_bail']; ?></td>
                                            <td><?php echo $resultat['nom_locataire']; ?></td>
                                            <td><?php echo $resultat['contact']; ?></td>
                                            <td><?php echo $resultat['logement']; ?></td>
                                            <td><?php echo $resultat['duree_contrat']; ?></td>
                                            <td><?php $loyer_mensuel = number_format($resultat['loyer_mensuel'], 0, ',', '.');
                                                echo $loyer_mensuel; ?>
                                            </td>
                                            <td><?php echo $resultat['frequence_paiement']; ?></td>
                                            <td><?php echo $resultat['mode_paiement']; ?></td>
                                            <td><?php $loyer_paye = number_format($resultat['nombre_mois'] * $resultat['loyer_mensuel'], 0, ',', '.');
                                                echo $loyer_paye; ?>
                                            </td>
                                            <td><?php $caution = number_format($resultat['montant_caution'], 0, ',', '.');
                                                echo $caution; ?>
                                            </td>
                                            <td><?php echo $resultat['revision_loyer']; ?></td>
                                            <td><?php echo $resultat['taux_revision']; ?></td>
                                            <td><?php echo $resultat['pénalites_retard']; ?></td>
                                            <td><?php $dateStart = new DateTime($resultat['date_debut_contrat']);
                                                $dateStart = $dateStart->format('d-m-Y');
                                                echo $dateStart; ?>
                                            </td>
                                            <td><?php $dateEnd = new DateTime($resultat['date_fin_contrat']);
                                                $dateEnd = $dateEnd->format('d-m-Y');
                                                echo $dateEnd; ?>
                                            </td>
                                            <td>
                                                <?php $datecreation = new DateTime($resultat['date_ajout']);
                                                $datecreation = $datecreation->format('d-m-Y');
                                                echo $datecreation; ?>
                                            </td>
                                            <td><?php $droit_reg = number_format($resultat['droit_enregistrement'], 0, ',', '.');
                                                echo $droit_reg; ?>
                                            </td>
                                            <td><?php echo $resultat['nom_GI']; ?></td>
                                            <td><?php echo $resultat['numero_dossier']; ?></td>
                                            <td class="ft-w <?php echo $resultat['etat']; ?>" data-idClient="<?php echo $resultat['id_operatoire']; ?>" data-ville="<?= $resultat['ville']; ?>" data-date_debut="<?= $resultat['date_debut_contrat']; ?>" data-date_resiliation="<?= $resultat['date_resiliation']; ?>">
                                                <a href="<?= ($resultat['etat'] == "En-cours" || $resultat['etat'] == "Actif") ? '#main3' : '#main4'; ?>" title="Cliquez pour voir le dossier">
                                                    <?php
                                                    $percent = 0;
                                                    if ($resultat['etat'] == "En-cours" || $resultat['etat'] == "Actif") {
                                                        $val_offre = ($resultat['validation_offre'] === 1 || $resultat['validation_offre'] === 0) ? 1 : 0;
                                                        $elab_contrat = ($resultat['elaboration_contrat'] === 1 || $resultat['elaboration_contrat'] === 0) ? 1 : 0;
                                                        $control_final = ($resultat['control_final'] === 1 || $resultat['control_final'] === 0) ? 1 : 0;
                                                        $percent = $resultat['negoce'] + $val_offre + $resultat['info_client'] + $elab_contrat + $resultat['transmition_contrat_client'] + $resultat['finalisation_dossier'] + $control_final + $resultat['validation_dossier'] + $resultat['transmition_contrat_remise'] + $resultat['transmition_decharge'] + $resultat['reception_dossier'] + $resultat['archivage'];
                                                    } else {
                                                        $control_val = ($resultat['controle_validation_dossier'] === 1 || $resultat['controle_validation_dossier'] === 0) ? 1 : 0;
                                                        $percent = $resultat['lettre_preavis'] + $control_val + $resultat['transmition_elements'] + $resultat['prevalidation_dossier'] + $resultat['validation_provisoire'] + $resultat['transmition_element_provisoire'] + $resultat['transmition_reponse'] + $resultat['etat_lieux'] + $resultat['transmition_elements_complet'] + $resultat['approbation_dossier'] + $resultat['paiement_locataire'] + $resultat['archivage_resiliation'];
                                                    }
                                                    $percent = $percent * 100 / 12;
                                                    $percent = number_format($percent, 2);
                                                    ?>
                                                    <h5><?php echo "$percent%"; ?></h5>
                                                    <div class="evolutionBar">
                                                        <div class="evolution" style="width: <?php echo $percent; ?>%;"></div>
                                                    </div>
                                                    <span><?php echo $resultat['etat']; ?></span>
                                                </a>
                                            </td>
                                            <td class="edition">
                                                <ion-icon name="create-outline" size="small" title="Modifiez" data-numDoc="numDoc<?php echo $resultat['id']; ?>" data-etat="false"></ion-icon>
                                                <ion-icon data-id_operatoire="<?= $resultat['id_operatoire']; ?>" name="remove-circle-outline" size="small" title="Résilié ce dossier"></ion-icon>
                                                <?php if (new DateTime($resultat['date_revision']) <= new DateTime()) { ?>
                                                    <ion-icon name="reload-circle-outline" size="small" title="Renouveller le contrat" data-id_operatoire="<?= $resultat['id_operatoire']; ?>"></ion-icon>
                                                <?php } ?>
                                                <form action="" method="get" class="favoris">
                                                    <button type="button" name="favori" data-id="<?= $resultat['id']; ?>" value="<?= ($resultat['favori'] == 0) ? 1 : 0; ?>">
                                                        <ion-icon name="<?= ($resultat['favori'] == 0) ? "bookmark-outline" : "bookmark"; ?>" size="small" title="Marquez comme important"></ion-icon>
                                                    </button>
                                                </form>
                                                <?php if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin") { ?>
                                                    <ion-icon name="trash-outline" size="small" title="Supprimez" data-id_contrat="<?= $resultat['id_operatoire']; ?>" data-type="<?= $resultat['etat']; ?>"></ion-icon>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo '<p align="center">Aucun resultat</p>';
                                }
                                $requete->closeCursor();
                                ?>
                            </tbody>
                        </table>
                        <div id="modification">
                            <form action="updateContrat.php" method="post" onsubmit="return veriform(this);">
                                <h2>Modification contrat<span title="fermer" onclick="closePopup(document.querySelector('#modification'));">&times;</span></h2>
                                <hr noshade="">
                                <div class="champs">
                                    <div class="input-group"><label for="siteM">Site <span class="obligatoire">*</span></label>
                                        <select name="siteM" id="siteM" required>
                                            <option disabled>Choisissez une site</option>
                                            <?php foreach ($sites as $site) {
                                            ?>
                                                <option value="<?= $site['site'] ?>"><?= $site['site'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-group"><label for="entiteM">Entite <span class="obligatoire">*</span></label>
                                        <select name="entiteM" id="entiteM" required onchange="verifEntite(this,'#erreurentiteM');" onblur="verifEntite(this,'#erreurEntiteM')">
                                            <option disabled>Choisissez une entité</option>
                                            <?php foreach ($entites as $entite) {
                                            ?>
                                                <option value="<?= $entite['entite'] ?>"><?= $entite['entite'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span id="erreurEntiteM" style="color: red; font-size: 10px;"></span>
                                    </div>
                                    <div class="input-group"><label for="villeM">Ville <span class="obligatoire">*</span></label>
                                        <select name="villeM" id="villeM" required>
                                            <option disabled>Choisissez une ville</option>
                                            <?php foreach ($villes as $ville) {
                                            ?>
                                                <option value="<?= $ville['ville'] ?>"><?= $ville['ville'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-group"><label for="natureBailM">Nature Bail <span class="obligatoire">*</span></label>
                                        <select name="natureBailM" id="natureBailM" required>
                                            <option disabled>Choisissez la ntaure du bail</option>
                                            <option value="Commercial">Commercial</option>
                                            <option value="Habitation">Habitation</option>
                                            <option value="Professionnel">Professionnel</option>
                                        </select>
                                    </div>
                                    <div class="input-group"><label for="nomM">Nom Locataire <span class="obligatoire">*</span></label><input type="text" name="nomM" id="nomM" required onblur="verifNom(this,'#erreurNomM');"><span id="erreurNomM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="contactM">Contact <span class="obligatoire">*</span></label><input type="tel" name="contactM" id="contactM" placeholder="ex: 6988776655" required onblur="verifContact(this,'#erreurContactM');"><span id="erreurContactM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="logementM">Logement <span class="obligatoire">*</span></label><input type="text" name="logementM" id="logementM" required onblur="verifChampVide(this,'#erreureLogementM');"><span id="erreureLogementM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="timeM">Durée Contrat <span class="obligatoire">*</span></label><input type="number" name="timeM" id="timeM" required onblur="verifChampVide(this,'#erreurDureeM');"><span id="erreurDureeM" style="color: red; font-size: 14px;"></span></div>
                                    <div class="input-group"><label for="loyerM">Loyer Mensuel (Fcfa) <span class="obligatoire">*</span></label><input type="number" name="loyerM" id="loyerM" required onblur="verifChampVide(this,'#erreurLoyerM');"><span id="erreurLoyerM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="frequenceM">Fréquence Paiement <span class="obligatoire">*</span> </label>
                                        <select name="frequenceM" id="frequenceM" required>
                                            <option disabled>Choisissez une fréquence de paiement</option>
                                            <option value="Annuelle">Annuelle</option>
                                            <option value="Semestrielle">Semestrielle</option>
                                            <option value="Trimestrielle">Trimestrielle</option>
                                            <option value="Autre">Autre</option>
                                        </select>
                                    </div>
                                    <div class="input-group"><label for="modePaiementM">Mode Paiement <span class="obligatoire">*</span></label>
                                        <select name="modePaiementM" id="modePaiementM" required>
                                            <option disabled>Choisissez un mode de paiement</option>
                                            <option value="Chèque">Chèque</option>
                                            <option value="Carte">Carte</option>
                                            <option value="Espèces">Espèces</option>
                                            <option value="Momo">MoMo</option>
                                            <option value="OM">OM</option>
                                            <option value="Virement">Virement</option>
                                            <option value="Tous les modes">Tous les modes</option>
                                        </select>
                                    </div>
                                    <div class="input-group"><label for="nombreMoisM">Nombre mois <span class="obligatoire">*</span></label><input type="number" name="nombreMoisM" id="nombreMoisM" required onblur="verifChampVide(this,'#erreurNbMoisM');"><span id="erreurNbMmoisM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="montantCautionM">Montant Caution <span class="obligatoire">*</span></label><input type="number" name="montantCautionM" id="montantCautionM" required onblur="verifChampVide(this,'#erreurCautionM');"><span id="erreurCautionM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="revisionM">Révision Loyer <span class="obligatoire">*</span></label>
                                        <select name="revisionM" id="revisionM" required>
                                            <option disabled>Choisissez une fréquence de révision</option>
                                            <option value="Annuelle">Annuelle</option>
                                            <option value="Biennale">Biennale</option>
                                            <option value="Triennale">Triennale</option>
                                            <option value="Autre">Autre</option>
                                        </select>
                                    </div>
                                    <div class="input-group"><label for="taux_revisionM">Taux révision (%) <span class="obligatoire">*</span></label><input type="number" name="taux_revisionM" id="taux_revisionM" required onblur="verifChampVide(this,'#erreurRevisionM');"><span id="erreurRevisionM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="penaliteM">Pénaités Retard (%) <span class="obligatoire">*</span></label><input type="number" name="penaliteM" id="penaliteM" required onblur="verifChampVide(this,'#erreurPenaliteM');"><span id="erreurPenaliteM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="debutM">Date Debut Contrat <span class="obligatoire">*</span></label><input type="date" name="debutM" id="debutM" required onblur="verifChampVide(this,'#erreurDatestartM');"><span id="erreurDatestartM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="finM">Date Fin Contrat <span class="obligatoire">*</span></label><input type="date" name="finM" id="finM" required onblur="verifChampVide(this,'#erreurDatefinM');"><span id="erreurDatefinM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="saveM">D. Enregistrement <span class="obligatoire">*</span></label><input type="number" name="saveM" id="saveM" required onblur="verifChampVide(this,'#erreurSaveM');"><span id="erreurSaveM" style="color: red; font-size: 10px;"></span></div>
                                    <div class="input-group"><label for="giM">Nom GI <span class="obligatoire">*</span></label>
                                        <select name="giM" id="giM" required>
                                            <option disabled>Choisissez un GI</option>
                                            <?php
                                            foreach ($Gis as $Gi) {
                                            ?>
                                                <option value="<?= $Gi['nom']; ?>" <?= ($_SESSION['nom'] == $Gi['nom']) ? "selected" : ""; ?>><?= $Gi['nom']; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="input-group"><label for="numDocM">Numéro Dossier</label><input type="text" name="numDocM" id="numDocM"><span id="erreurnumdocM" style="color: red; font-size: 10px;"></span></div>
                                    <input type="hidden" name="idM" id="idM">
                                </div>
                                <input type="submit" name="Modifiez" id="Modif" value="Modifiez">
                            </form>
                        </div>
                        <?php
                        if (isset($_GET['modif'])) {
                        ?>
                            <div id="modifBull" onclick="closePopup(this);">
                                <?php echo $_GET['modif']; ?>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['delete'])) {
                        ?>
                            <div id="delete" onclick="closePopup(this);">
                                <?php echo $_GET['delete']; ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <div id="main3" class="main">
                        <h1><ion-icon name="bar-chart"></ion-icon> Contrats En Cours</h1>
                        <form action="" method="get" id="form_search">
                            <select name="zone" id="zone">
                                <?php foreach ($villes as $ville) {
                                ?>
                                    <option value="<?= $ville['ville']; ?>" <?= $ville['ville'] == "Douala" ? "selected" : null; ?>>
                                        <?= $ville['ville']; ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                            <input type="month" id="month">
                            <div id="revision"><span></span><ion-icon name="eye-outline"></ion-icon></div>
                            <input type="search" name="search" id="search" value="" placeholder="search: tapez au moins 2 lettres" oninput="searchContrat();">
                        </form>
                        <div id="revise"></div>
                        <div id="allContrats" data-resultat=" <?php echo htmlspecialchars(json_encode($data)); ?>"></div>
                        <div id="table">
                            <!-- liste des contrats -->
                        </div>
                        <div id="checkliste">
                            <form action="updateAdhesion.php" method="post">
                                <h2>CHECKLIST D'ADHESION CONTRAT DE BAIL VS.N°4 <ion-icon name="pencil"></ion-icon></h2><span class="croix">&times;</span>
                                <span class="mini-title">(Voir la note de service N°30 du 15 Avril 2021 portant implémentation de la check-list d'adhésion de bail)</span>
                                <table id="table1">
                                    <tr>
                                        <td id="dateAjout">Date : </td>
                                        <td>N° D'ESPACE : </td>
                                        <td id="numEspace"></td>
                                    </tr>
                                    <tr>
                                        <td id="nomClient">Nom client : </td>
                                        <td>CONTRAT N° : </td>
                                        <td id="numContrat"><input type="text" placeholder="N° contrat" name="numContrat"></td>
                                    </tr>
                                    <tr>
                                        <td id="nomGI">Nom gestionnaire : </td>
                                        <td>SITE : </td>
                                        <td id="nomSite"></td>
                                    </tr>
                                </table>
                                <table id="table2">
                                    <tr>
                                        <th>TACHES</th>
                                        <th>PILOTES</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                    <tr>
                                        <td><label for="negoce">Négociation avec le client et recueil d'informations sur l'espace sollicité, grace à la fiche d'adhésion</label></td>
                                        <td><label for="negoce">GI</label></td>
                                        <td><input type="checkbox" name="negoce" id="negoce" value="1"></td>
                                    </tr>
                                    <tr cl>
                                        <td><label for="val_off">Validation de l'offre sur la fiche d'adhésion :</label></td>
                                        <td><label for="val_off">RGI</label></td>
                                        <td><input type="radio" name="val_off" id="val_off" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="val_off2">Offres non-conventionnelles</label></td>
                                        <td><label for="val_off2">DSF/DO</label></td>
                                        <td><input type="radio" name="val_off" id="val_off2" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="info_client">Recueillir les informations du client pour l'élaboration de son contrat</label></td>
                                        <td><label for="info_client">GI</label></td>
                                        <td><input type="checkbox" name="info_client" id="info_client" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="elab_contrat">Elaboration du contrat selon la fiche d'adhésion : Offres conventionnelles</label></td>
                                        <td><label for="elab_contrat">Assist.DSF</label></td>
                                        <td><input type="radio" name="elab_contrat" id="elab_contrat" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="elab_contrat2">Offres non-conventionnelles</label></td>
                                        <td><label for="elab_contrat2">JDQ</label></td>
                                        <td><input type="radio" name="elab_contrat" id="elab_contrat2" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="trans_contrat">Transmission du contrat au client pour signature</label></td>
                                        <td><label for="trans_contrat">GI</label></td>
                                        <td><input type="checkbox" name="trans_contrat" id="trans_contrat" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="final_dossier">Finalisation du dossier de bail : réunir tous les documents de la checklist éléments d'adhésion au contrat de bail ci-jointe</label></td>
                                        <td><label for="final_dossier">GI</label></td>
                                        <td><input type="checkbox" name="final_dossier" id="final_dossier" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2">Contrôle final du dossier de bail avant transmission à la Direction</td>
                                        <td><label for="control_final">RAF</label></td>
                                        <td><input type="radio" name="control_final" id="control_final" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="control_final2">RGI</label></td>
                                        <td><input type="radio" name="control_final" id="control_final2" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="val_doss">Validation du dossier et signature du contrat</label></td>
                                        <td><label for="val_doss">DSF/DO</label></td>
                                        <td><input type="checkbox" name="val_doss" id="val_doss" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="trans_contrat_remise">Transmission du contrat et remise des clés au client</label></td>
                                        <td><label for="trans_contrat_remise"><label for="trans_contrat_remise">GI</label></label></td>
                                        <td><input type="checkbox" name="trans_contrat_remise" id="trans_contrat_remise" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="trans_decharge">Transmission contre décharge du dossier de bail au service fiscalite et service juridique</label></td>
                                        <td><label for="trans_decharge">Assist.DSF</label></td>
                                        <td><input type="checkbox" name="trans_decharge" id="trans_decharge" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="recept_doc">Réception et contrôle du dossier (SOREPCO)</label></td>
                                        <td><label for="recept_doc">Sce.JDQ/Fisc</label></td>
                                        <td><input type="checkbox" name="recept_doc" id="recept_doc" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="archivage">Archivage du contrat enregistré et enregistrement dans le tableau de bord</label></td>
                                        <td><label for="archivage">Assist.DSF</label></td>
                                        <td><input type="checkbox" name="archivage" id="archivage" value="1"></td>
                                    </tr>
                                </table>
                                <input type="hidden" name="id_operatoire">
                                <button type="submit" name="enregistrer" id="submit" value="enregistrer"><ion-icon name="save" size="small"></ion-icon>Enregistrer</button>
                            </form>
                        </div>

                        <div id="modeOperatoire">
                            <h2>Mode operatoire <span onclick="closePopup(document.querySelector('#modeOperatoire'));" title="fermer le mode opraoire">&times;</span></h2>
                            <table>
                                <tr>
                                    <td>Site : </td>
                                    <td id="siteO"></td>
                                </tr>
                                <tr>
                                    <td>Entite : </td>
                                    <td id="entiteO"></td>
                                </tr>
                                <tr>
                                    <td>Ville : </td>
                                    <td id="villeO"></td>
                                </tr>
                                <tr>
                                    <td>Nature du bail : </td>
                                    <td id="bailO"></td>
                                </tr>
                                <tr>
                                    <td>Nom locataire : </td>
                                    <td id="locataireO"></td>
                                </tr>
                                <tr>
                                    <td>Contact : </td>
                                    <td id="contactO">
                                        </tdc>
                                </tr>
                                <tr>
                                    <td>Logement : </td>
                                    <td id="logementO"></td>
                                </tr>
                                <tr>
                                    <td>Durée du contrat : </td>
                                    <td id="duree_contratO"></td>
                                </tr>
                                <tr>
                                    <td>Loyer mensuel : </td>
                                    <td id="loyerO"></td>
                                </tr>
                                <tr>
                                    <td>Fréquence paiement : </td>
                                    <td id="frequenceO"></td>
                                </tr>
                                <tr>
                                    <td>Mode de paiement : </td>
                                    <td id="modeO"></td>
                                </tr>
                                <tr>
                                    <td>Montant loyer paiyé : </td>
                                    <td id="montant_loyerO"></td>
                                </tr>
                                <tr>
                                    <td>Montant caution : </td>
                                    <td id="montant_CautionO"></td>
                                </tr>
                                <tr>
                                    <td>Révision Loyé : </td>
                                    <td id="revision_loyerO"></td>
                                </tr>
                                <tr>
                                    <td>Pénalités retard : </td>
                                    <td id="penaliteO"></td>
                                </tr>
                                <tr>
                                    <td>Date debut contrat : </td>
                                    <td id="date_debutO"></td>
                                </tr>
                                <tr>
                                    <td>Date fin contrat : </td>
                                    <td id="date_finO"></td>
                                </tr>
                                <tr>
                                    <td>Droit d'enregistrement : </td>
                                    <td id="droit_enregistrementO"></td>
                                </tr>
                            </table>
                            <button title="appliquer le taux de révision">Réviser le contrat <ion-icon size="small" name="reload-outline"></ion-icon></button>
                        </div>

                        <!-- bloc maj checkliste -->
                        <?php
                        if (isset($_GET['maj'])) {
                        ?>
                            <div id="maj" onclick="closePopup(this);">
                                <?php echo $_GET['maj']; ?>
                            </div>
                        <?php
                        }
                        ?>
                        <audio id="notificationSound">
                            <source src="../ressources/699705__skyernaklea__notification-bell-and-water.wav" type="audio/wav">
                        </audio>
                    </div>
                    <div id="main4" class="main">
                        <h1><ion-icon name="document-text"></ion-icon> Contrats Résiliés</h1>

                        <form action="" method="get" id="formMain4">
                            <select name="zone" id="zoneR">
                                <?php foreach ($villes as $ville) {
                                ?>
                                    <option value="<?= $ville['ville']; ?>" <?= $ville['ville'] == "Douala" ? "selected" : null; ?>>
                                        <?= $ville['ville']; ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                            <input type="month" id="monthR">
                            <input type="search" name="search" id="searchResilie" value="" placeholder="search: tapez au moins 2 lettres" oninput="">
                        </form>

                        <div id="allResiliations" data-resultat="<?php echo  htmlspecialchars(json_encode($dataResiliation)); ?> "></div>

                        <div id="th">
                            <h4>Doc</h4>
                            <h4>&#8470;</h4>
                            <h4>Numero</h4>
                            <h4>Nom_Client</h4>
                            <h4>Nom_Gi</h4>
                            <h4>&#8470; Espace</h4>
                            <h4>Date de resiliation</h4>
                            <h4>Progression</h4>
                        </div>
                        <div id="tableResilie">
                            <!-- Liste des contrats Résiliés -->
                        </div>

                        <div id=checklisteResiliation>
                            <form action="updateResiliation.php" method="POST">
                                <h2>CHECKLIST DE RESILIATION CONTRAT DE BAIL VS.N°3<ion-icon name="pencil"></ion-icon></h2>
                                </h2><span class="croix">&times;</span>
                                <span class="mini-title">(Voir la note de service N°31 du 15 Avril 2021 portant implémentation de la check-list de résiliation de bail)</span>
                                <table id="table1R">
                                    <tr>
                                        <td id="dateAjoutR">Date : </td>
                                        <td>N° D'ESPACE : </td>
                                        <td id="numEspaceR"><input type="text" style="width: 100%; height: 100%;" placeholder="id"></td>
                                    </tr>
                                    <tr>
                                        <td id="nomClientR">Nom client : </td>
                                        <td>CONTRAT N° : </td>
                                        <td id="numContratR"><input type="text" placeholder="N° contrat" name="numContratR"></td>
                                    </tr>
                                    <tr>
                                        <td id="nomGIR">Nom gestionnaire : </td>
                                        <td>SITE : </td>
                                        <td id="nomSiteR"></td>
                                    </tr>
                                </table>
                                <table id="table2R">
                                    <tr>
                                        <th>TACHES</th>
                                        <th>PILOTES</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-size: 10px; font-weight: bold; text-align: center;">AVANT LA SORTIE DU CLIENT</td>
                                    </tr>
                                    <tr>
                                        <td><label for="preavis">Obtention de la lettre de préavis et transmission à l'assistante du DSF</label></td>
                                        <td><label for="preavis">GI</label></td>
                                        <td><input type="checkbox" name="preavis" id="preavis" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="const_trans_elem_repert">Constituer et transmettre les éléments de résiliation repertoriés sur la check-list éléments ci-jointe (Elements d'adhésion) et transmettre au RGI </label></td>
                                        <td><label for="const_trans_elem_repert">GI</label></td>
                                        <td><input type="checkbox" name="const_trans_elem_repert" id="const_trans_elem_repert" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="preval_dossier">Pré-validation des éléments du dossier de résiliation provisoire</label></td>
                                        <td><label for="preval_dossier">RGI/RAF</label></td>
                                        <td><input type="checkbox" name="preval_dossier" id="preval_dossier" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="val_dos_res">Validation du dossier de résiliation provisoire </label></td>
                                        <td><label for="val_dos_res">DSF/DO</label></td>
                                        <td><input type="checkbox" name="val_dos_res" id="val_dos_res" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="trans_elem_res">Transmission des éléments de résiliation provisoire au GI</label></td>
                                        <td><label for="trans_elem_res">Assist.DSF</label></td>
                                        <td><input type="checkbox" name="trans_elem_res" id="trans_elem_res" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="trans_rep_client">Transmission de la réponse au client</label></td>
                                        <td><label for="trans_rep_client">GI</label></td>
                                        <td><input type="checkbox" name="trans_rep_client" id="trans_rep_client" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-size: 10px; font-weight: bold; text-align: center;">APRES LA SORTIE DU CLIENT</td>
                                    </tr>
                                    <tr>
                                        <td><label for="etat_lieux">Etat des lieux à la sortie et récupération des clés</label></td>
                                        <td><label for="etat_lieux">GI</label></td>
                                        <td><input type="checkbox" name="etat_lieux" id="etat_lieux" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="const_trans_elem_res">Constituer et transmettre les éléments de résiliation complet repertoriés sur la check-list éléments ci-jointe (Elements de résiliation) et transmettre au RGI</label></td>
                                        <td><label for="const_trans_elem_res">GI</label></td>
                                        <td><input type="checkbox" name="const_trans_elem_res" id="const_trans_elem_res" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2">Contrôle et validation du dossier de résiliation finale</td>
                                        <td><label for="control_val">RAF</label></td>
                                        <td><input type="radio" name="control_val" id="control_val" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="control_val2">RGI</label></td>
                                        <td><input type="radio" name="control_val" id="control_val2" value="0"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="approb_doc_final">Approbation du dossier de résiliation final</label></td>
                                        <td><label for="approb_doc_final"><label for="approb_doc_final">DSF/DO</label></label></td>
                                        <td><input type="checkbox" name="approb_doc_final" id="approb_doc_final" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="paiement">Paiement du locataire ( remboursement caution)</label></td>
                                        <td><label for="paiement">Caissière</label></td>
                                        <td><input type="checkbox" name="paiement" id="paiement" value="1"></td>
                                    </tr>
                                    <tr>
                                        <td><label for="archivage_doc">Archivage du dossier de résiliation</label></td>
                                        <td><label for="archivage_doc">Assist.DSF</label></td>
                                        <td><input type="checkbox" name="archivage_doc" id="archivage_doc" value="1"></td>
                                    </tr>
                                </table>
                                <input type="hidden" name="id_mode">
                                <button type="submit" name="save" id="submitR" value="save"><ion-icon name="save" size="small"></ion-icon>Enregistrer</button>
                            </form>
                        </div>

                        <!-- bloc maj checkliste résiliation -->
                        <?php
                        if (isset($_GET['majR'])) {
                        ?>
                            <div id="majR" onclick="closePopup(this);">
                                <?php echo $_GET['majR']; ?>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['resilie'])) {
                        ?>
                            <div id="resilie" onclick="closePopup(this);">
                                <?php echo $_GET['resilie']; ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                    <?php
                    if ($_SESSION['type'] == "super admin" || $_SESSION['type'] == "admin") {
                    ?>
                        <div id="main5" class="main">
                            <h1><ion-icon name="podium"></ion-icon> Statistiques</h1>
                            <div id="dashboard">
                                <div id="statistique">
                                    <div class="stats">
                                        <div style="background-color: rgba(255, 99, 132, 0.2); border-color: rgba(255, 99, 132, 1);"><?= $actif[0]['nbactif']; ?></div> <span>Actifs</span>
                                    </div>
                                    <div class="stats">
                                        <div style="background-color: rgba(54, 162, 235, 0.2); border-color: rgba(54, 162, 235, 1);"><?= $resilie[0]['nbresilie']; ?></div> <span>Résiliés</span>
                                    </div>
                                    <div class="stats">
                                        <div style="background-color: rgba(255, 206, 86, 0.2); border-color: rgba(255, 206, 86, 1);"><?= $adhesion[0]['nbadhesion']; ?></div> <span>En cours d'adhésion</span>
                                    </div>
                                    <div class="stats">
                                        <div style="background-color: rgba(75, 192, 192, 0.2); border-color: rgba(75, 192, 192, 1);"><?= $resiliation[0]['nbresiliation']; ?></div> <span>En cours de résiliation</span>
                                    </div>
                                    <div class="stats">
                                        <div style="background-color: rgba(153, 102, 255, 0.2); border-color: rgba(153, 102, 255, 1);"><?= $favori[0]['nbfavori']; ?></div> <span>Contrats en favori</span>
                                    </div>
                                </div>
                                <div class="iframe-container" style="height: 250px">
                                    <iframe src="graphes/contrat_par_mois.php" allowfullscreen title="Iframe 1"></iframe>
                                </div>
                                <div class="iframe-container" style="height: 250px">
                                    <iframe src="graphes/contrat_par_gi.php" allowfullscreen title="Iframe 2"></iframe>
                                </div>
                                <div class="iframe-container">
                                    <iframe src="graphes/contrat_par_ville.php" allowfullscreen title="Iframe 3"></iframe>
                                </div>
                                <div class="iframe-container">
                                    <iframe src="graphes/contrat_par_site.php" allowfullscreen title="Iframe 4"></iframe>
                                </div>
                                <div class="iframe-container">
                                    <iframe src="graphes/resiliation_par_ville.php" allowfullscreen title="Iframe 4"></iframe>
                                </div>
                                <div class="iframe-container">
                                    <iframe src="graphes/resiliation_par_site.php" allowfullscreen title="Iframe 4"></iframe>
                                </div>
                            </div>
                            <div class="chat-icon" onclick="toggleChatBox()">💬</div>
                            <div class="chat-box" id="chatBox">
                                <div class="chat-question" onclick="showResponse(this);">Dans quelle ville il y a plus de contrats actifs?</div>
                                <div class="chat-question" onclick="showResponse(this);">Quel site a le plus de contrats actifs?</div>
                                <div class="chat-question" onclick="showResponse(this);">Quel est le pourcentage de résiliation en cette année?</div>
                                <div class="chat-question" onclick="showResponse(this);">Quel site ramène plus de bénéfice ctte année?</div>
                                <div class="chat-question" onclick="showResponse(this);">En quelle année a-t-on eu le plus de client?</div>
                                <div class="chat-question" onclick="showResponse(this);">Quels sont les dossiers en cours d'adhesion?</div>
                                <div class="chat-question" onclick="showResponse(this);">Quel GI gère le site le plus actif?</div>
                                <div class="chat-question" onclick="showResponse(this);">Bonjour, peux-tu me pretter ton aide?</div>
                            </div>
                            <div id="reponse"></div>
                        </div>
                    <?php
                    }
                    if ($_SESSION['type'] == "super admin") {
                    ?>
                        <div id="main6" class="main">
                            <h1><ion-icon name="desktop"></ion-icon> Audit Du Système</h1>
                            <?php
                            $requetelog = $bdd->prepare("SELECT * FROM logs ORDER BY date_logs DESC");
                            $requetelog->execute();
                            $logs = $requetelog->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <div style="display: flex">
                                <button title="Cliquez pour exporter les logs" id="exportLog" onclick="exportToExcel('#allLog','logs')"><ion-icon name="download-outline"></ion-icon>Exportez les logs</button>
                            </div>

                            <table id="allLog">
                                <thead>
                                    <th>Id_Log</th>
                                    <th>Id_User</th>
                                    <th>Nom_User</th>
                                    <th>Adresse_Ip</th>
                                    <th>Level_Log</th>
                                    <th>Message_Log</th>
                                    <th>Date_Logs</th>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($logs as $log) {
                                    ?>
                                        <tr class="log">
                                            <td><?= $log['id_log']; ?></td>
                                            <td><?= $log['id_user']; ?></td>
                                            <td><?= $log['name_user']; ?></td>
                                            <td><?= $log['adresse_ip']; ?></t>
                                            <td><?= $log['level_log']; ?></t>
                                            <td><?= $log['message_log']; ?></t>
                                            <td><?= $log['date_logs']; ?></viv>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    <?php
                    }
                    if ($_SESSION['type'] == "admin" || $_SESSION['type'] == "super admin") {
                    ?>
                        <div id="main7" class="main">
                            <h1><ion-icon name="person-circle-outline" size="large"></ion-icon> Administration</h1>
                            <div id="user">
                                <div class="info"><ion-icon name="person-circle-outline"></ion-icon></div>
                                <p><?= $_SESSION['type'] ?></p>
                                <p><a href="deconnexion.php" title="se deconnecter" id="logout">Se deconnecter</a></p>
                            </div>
                            <div style="display: flex;">
                                <button id="creatCompte"><ion-icon name="person-add" size="small"></ion-icon> Créer un compte Utilisateur</button>
                                <button id="deleteCompte"><ion-icon name="person-remove" size="small"></ion-icon> Supprimer un compte Utilisateur</button>
                            </div>
                            <form action="newUser.php" method="post" id="newUser" onsubmit="return verifFormNew(this);">
                                <h2>Créer un nouveau compte <span title="Fermer">&times;</span></h2>
                                <label for="newName">Nom <span class="obligatoire">*</span></label>
                                <input type="text" id="newName" name="newName" placeholder="Entrez le nom de l'Utilisateur" required onblur="verifNom(this,'#erreurNomNew');">
                                <span id="erreurNomNew" style="color: red; font-size: 10px;"></span>
                                <label for="newLogin">Login <span class="obligatoire">*</span></label>
                                <input type="text" id="newLogin" name="newLogin" placeholder="Entrez le login de l'utilisateur" required onblur="verifNom(this,'#erreurLoginnNew');">
                                <span id="erreurLoginnNew" style="color: red; font-size: 10px;"></span>
                                <label for="newPassword">Password <span class="obligatoire">*</span></label>
                                <input type="password" id="newPassword" name="newPassword" maxlength="9" placeholder="Créez un nouveau mot de passe" required onblur="verifPassword(this,'#erreurPasswordNew');">
                                <span id="erreurPasswordNew" style="color: red; font-size: 10px;"></span>
                                <label for="newCPassword">Confirmez le password <span class="obligatoire">*</span></label>
                                <input type="password" id="newCPassword" name="newCPassword" maxlength="9" placeholder="Rentrez le même mot de passe" required onblur="verifCpassword(this,'#erreurCpasswordNew');">
                                <span id="erreurCpasswordNew" style="color: red; font-size: 10px;"></span>
                                <input type="submit" value="Créez le compte" name="creez">
                            </form>
                            <form action="deleteUser.php" method="post" id="delUser" onsubmit="return verifDelete();">
                                <h2>Supprimer un compte <span title="fermer">&times;</span></h2>
                                <span style="font-size: 13px; text-align: center; color: red;background-color:White; font-weight:bold;">(Attention cette opération est irréversible)</span>
                                <label for="delnom">Sélectionnez le nom du compte à supprimer</label>
                                <select name="delnom" id="delnom" required>
                                    <?php
                                    foreach ($users as $user) {
                                    ?>
                                        <option value="<?= $user['nom']; ?>"><?= $user['nom']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <input type="submit" name="delete" value="Supprimer le compte">
                            </form>
                            <table id="allusers">
                                <thead>
                                    <th>Id</th>
                                    <th>Nom</th>
                                    <th>Type</th>
                                    <th>Login</th>
                                    <th>Password</th>
                                    <th>Nombre de contrats</th>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($users as $user) {
                                    ?>
                                        <tr>
                                            <td><?= $user['id']; ?></td>
                                            <td><?= $user['nom']; ?></td>
                                            <td><?= $user['type']; ?></td>
                                            <td><?= $user['login']; ?></td>
                                            <td><?= $user['password']; ?></td>
                                            <td><?= $user['nombre_contrats']; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <?php
                            if (isset($_GET['compte'])) {
                            ?>
                                <div id="compte" onclick="closePopup(this);">
                                    <?php echo $_GET['compte']; ?>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </aside>
            </div>
        </body>

        </html>

<?php
    } else if (isset($_POST["submit1"])) {
        require_once('log.php');

        $site = strip_tags(htmlspecialchars($_POST['site']));
        $entite = strip_tags(htmlspecialchars($_POST['entite']));
        $ville = strip_tags(htmlspecialchars($_POST['ville']));
        $natbail = strip_tags(htmlspecialchars($_POST['natbail']));
        $nom = strip_tags(htmlspecialchars($_POST['nom']));
        $contact = strip_tags(htmlspecialchars($_POST['contact']));
        $logement = strip_tags(htmlspecialchars($_POST['logement']));
        $time_c = strip_tags(htmlspecialchars($_POST['time_c']));
        $loy_mens = strip_tags(htmlspecialchars($_POST['loy_mens']));
        $freq_paie = strip_tags(htmlspecialchars($_POST['freq_paie']));
        $mode_paie = strip_tags(htmlspecialchars($_POST['mode_paie']));
        $nb_mois_paye = strip_tags(htmlspecialchars($_POST['nb_mois_paye']));
        $caution = strip_tags(htmlspecialchars($_POST['caution']));
        $rev_loyer = strip_tags(htmlspecialchars($_POST['rev_loyer']));
        $taux_revision = strip_tags(htmlspecialchars($_POST['taux_revision']));
        $pen_retard = strip_tags(htmlspecialchars($_POST['pen_retard']));
        $droit_reg = strip_tags(htmlspecialchars($_POST['droit_reg']));
        $date_start = strip_tags(htmlspecialchars($_POST['date_start']));
        $date_end = strip_tags(htmlspecialchars($_POST['date_end']));
        $gi = strip_tags(htmlspecialchars($_POST['gi']));
        //$num_doc = strip_tags(htmlspecialchars($_POST['num_doc']));

        $etat = "En-cours";
        $dateRevision = new DateTime($date_start);
        switch ($rev_loyer) {
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
                $dateRevision->modify('+1 year');
                break;
        }
        $newDaterevision = $dateRevision->format('Y-m-d');

        try {
            $connexion = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $requete = $connexion->prepare("INSERT INTO mode_operatoire VALUE ('',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'',?,0)");
            $requete->execute(array($site, $entite, $ville, $natbail, $nom, $contact, $logement, $time_c, $loy_mens, $freq_paie, $mode_paie, $nb_mois_paye, $caution, $rev_loyer, $taux_revision, $newDaterevision, $pen_retard, $date_start, $date_end, $droit_reg, $gi, $etat));

            //récupération de l'id du dernier élément ajouter
            $last_id = $connexion->lastInsertId();

            setlog($_SESSION['id'], 2, "Ajout du nouveau contrat № $last_id");

            $checkliste = $connexion->prepare("INSERT INTO adhesion VALUE('',?,1,NULL,0,NULL,0,0,NULL,0,0,0,0,0,NOW())");
            $checkliste->execute(array($last_id));

            $requete->closeCursor();
            $checkliste->closeCursor();
            header("location: index.php#main2");
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
} else {
    session_destroy();
    header('location: ../index.php');
}
?>