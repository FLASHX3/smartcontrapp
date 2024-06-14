<?php
    require_once('log.php');
    setlog($_SESSION['id'], -1, "Déconnexion de la plateforme!");
    session_destroy();
    header('location: ../index.php');
?>