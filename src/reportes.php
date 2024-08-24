<?php

require 'config.php';
require 'db.php';

$pdo = getConnection();

function generarReporteMensual($pdo, $mes, $anio) {
    $stmt = $pdo->prepare("
        SELECT 
            fecha, 
            COUNT(DISTINCT archivo_nombre) AS archivos_procesados, 
            SUM(registros_procesados) AS total_registros 
        FROM bitacora_carga 
        WHERE MONTH(fecha) = :mes AND YEAR(fecha) = :anio 
        GROUP BY fecha
    ");
    $stmt->execute([
        ':mes' => $mes,
        ':anio' => $anio
    ]);
    
    $report = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
    foreach ($report as $row) {
        echo "Fecha: {$row['fecha']}, Archivos Procesados: {$row['archivos_procesados']}, Total Registros: {$row['total_registros']}\n";
    }
}


$mes = date('m'); 
$anio = date('Y'); 
generarReporteMensual($pdo, $mes, $anio);
