<?php
if (!isset($_POST['login']) and !isset($_POST['password'])) {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/index.css">
        <link rel="shortcut icon" href="images/logo_sci_sotradic.png" type="image/x-icon">
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
        <script src="javascript/index.js" defer></script>
        <title>GestContrApp</title>
    </head>

    <body>
        <div id="container">
            <div id="logo" style="width: 100px; top: 10px; left: 80px; height: 120px; display: flex;  position: absolute;">
                <img src="images/logo_sci_sotradic_2.png" alt="logo_sci_scotradic">
            </div>
            <form action="index.php" method="post" onsubmit="return verifForm(this);">
                <h2>SmartContrApp</h2>
                <span class="mdi-light--account"></span>
                <input type="text" name="login" placeholder="login" required onblur="verifLogin(this);">
                <span class="formkit--password"></span>
                <input type="password" name="password" placeholder="password" maxlength="9" required onblur="verifMdp(this);">
                <span class="erreur"><?php if (isset($_GET['err'])) {
                                            echo $_GET['err'];
                                        } ?></span>
                <input type="submit" value="Sign in">
                <span class="forgot"><a href="#forgot">forgot password?</a></span>
            </form>

            <div id="forgot">
                <form action="">
                    <h2>Mot de passe oublié?<a href="#" title="Fermer">&times;</a></h2>
                    <a href="tel:+237 6 55 93 67 74" title="Cliquez pour joindre"><button type="button"><ion-icon name="call" size="small"></ion-icon>Contactez votre administrateur</button></a>
                </form>
            </div>
        </div>
    </body>

    </html>

<?php
} else if (isset($_POST['login']) and isset($_POST['password'])) {
    require_once('dashboard/log.php');

    $login = strip_tags(htmlspecialchars($_POST['login']));
    $password = strip_tags(htmlspecialchars($_POST['password']));

    if (!preg_match('#^[a-zA-Z]{6,25}$#', $login)) {
        header('Location: index.php?err=login wrong');
    }
    if (!preg_match('#^[a-zA-Z0-9.-_*@&$]{9}$#', $password)) {
        header('Location: index.php?err=password wrong');
    }

    try {
        $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $requete = $bdd->prepare('SELECT * FROM users WHERE login=? AND password=?');
        $requete->execute(array($login, $password));
        $userexist = $requete->rowCount();

        if ($userexist == 1)        #s'il existe, on ouvre sa session
        {
            $userinfo = $requete->fetch();

            session_start();

            $_SESSION['id'] = $userinfo['id'];
            $_SESSION['nom'] = $userinfo['nom'];
            $_SESSION['login'] = $userinfo['login'];
            $_SESSION['password'] = $userinfo['password'];
            $_SESSION['type'] = $userinfo['type'];

            $requete->closeCursor();

            setlog($_SESSION['id'], 1, "Connexion sur la plateforme!");
            header('location: dashboard/index.php#main2');
        } else {
            $requete->closeCursor();
            header('Location: index.php?err=identifiants wrong');
        }
    } catch (PDOException $e) {
        die('Erreur: ' . $e->getMessage());
    }
} else {
    header('Location: index.php');
}
?>