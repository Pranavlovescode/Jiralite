<?php
function createConnection(): PDO
{
    $username = "pranav";
    $pass = "pranav";
    $dbname = "jiralite";

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$dbname", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
        // echo "Connected successfully!";
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
