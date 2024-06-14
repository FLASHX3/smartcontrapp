<?php
session_start();

function getIp()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function setlog($id_user, $level, $message)
{
    try {
        $bdd = new PDO("mysql:host=localhost;dbname=gestcontrapp;charset=utf8", 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    $log = $bdd->prepare("INSERT INTO logs (id_user, name_user, adresse_ip, level_log, message_log) VALUES (?,?,?,?,?)");
    $log->execute(array($id_user, $_SESSION['nom'], getIp(), $level, $message));
}
