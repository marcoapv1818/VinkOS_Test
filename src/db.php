<?php

/***
 * Establece la conexión a MySQL utilizando PDO.
 */
function getConnection() {
    $config = require 'config.php'; // Cargar la configuración

    $host = $config['db']['host'];
    $dbname = $config['db']['dbname'];
    $username = $config['db']['username'];
    $password = $config['db']['password'];
    $port = $config['db']['port'];

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

    try {
        // Crear la conexión PDO
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Registrar el error y lanzar una excepción
        error_log('Error de conexión: ' . $e->getMessage());
        throw new Exception('No se pudo conectar a la base de datos.');
    }
}


//EOF