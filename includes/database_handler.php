<?php

$dbname = "myteam2";
$host = 'localhost';
$dsn = "mysql:dbname=" . $dbname . ";host=" . $host;

$user = "root";
$password = "";

try {
    $dbh = new PDO($dsn, $user, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
    die();
}
