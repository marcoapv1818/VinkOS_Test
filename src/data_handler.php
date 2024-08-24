<?php

require 'db.php'; 

function handleData($data,$fileName) {
    global $pdo;
    $recordsProcessed = 0;
    
    foreach ($data as $key => $register) {

        $email = $register[0];
        $fecha = $register[4];
    
        try {
            $pdo->beginTransaction();
    
            processVisitante($pdo, $email, $fecha);
            insertEstadistica($pdo, $register);
    
            $pdo->commit();
            $recordsProcessed++;
        } catch (Exception $e) {
            $pdo->rollBack();
            //processLog($email, $e->getMessage());
        }
    }

    logBitacora($pdo, $fileName, $recordsProcessed);
}

function processVisitante($pdo, $email, $fecha) {
    $year = date('Y', strtotime($fecha));
    $month = date('m', strtotime($fecha));

    $stmt = $pdo->prepare("SELECT * FROM visitante WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $visitante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($visitante) {
        $stmt = $pdo->prepare("
            UPDATE visitante 
            SET 
                fechaUltimaVisita = :fechaUltimaVisita,
                visitasTotales = visitasTotales + 1,
                visitasAnioActual = visitasAnioActual + IF(YEAR(fechaUltimaVisita) = :anioActual, 1, 0),
                visitasMesActual = visitasMesActual + IF(YEAR(fechaUltimaVisita) = :anioActual AND MONTH(fechaUltimaVisita) = :mesActual, 1, 0)
            WHERE 
                email = :email
        ");
        $stmt->execute([
            'fechaUltimaVisita' => date('Ymd', strtotime($fecha)),
            'anioActual' => $year,
            'mesActual' => $month,
            'email' => $email
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO visitante (email, fechaPrimeraVisita, fechaUltimaVisita, visitasTotales, visitasAnioActual, visitasMesActual)
            VALUES (:email, :fechaPrimeraVisita, :fechaUltimaVisita, 1, IF(:anioActual = YEAR(:fechaUltimaVisita), 1, 0), IF(:anioActual = YEAR(:fechaUltimaVisita) AND :mesActual = MONTH(:fechaUltimaVisita), 1, 0))
        ");
        $stmt->execute([
            'email' => $email,
            'fechaPrimeraVisita' => date('Ymd', strtotime($fecha)),
            'fechaUltimaVisita' => date('Ymd', strtotime($fecha)),
            'anioActual' => $year,
            'mesActual' => $month
        ]);
    }
}

function insertEstadistica($pdo, $data) {
    $email = $data[0];
    $jyv = (isset( $data[1] ) )? (int)trim($data[1]):0;
    $Badmail = (isset( $data[2] ) )? (int)trim($data[2]):0;
    $Baja = (isset( $data[3] ) )? (int)trim($data[3]):0;
    $fechaEnvio = (isset( $data[4] ) )? convertDateFormat($data[4]):'1991-03-21 12:45:00';
    $fechaOpen = (isset( $data[5] ) )? convertDateFormat($data[5]):'1991-03-21 12:45:00';
    $opens = (isset( $data[6] ) )? (int)trim($data[6]):0;
    $opensVirales = (isset( $data[7] ) )? (int)trim($data[7]):0;
    $fechaClick = (isset( $data[8] ) )? convertDateFormat($data[8]):'1991-03-21 12:45:00';
    $clicks = (isset( $data[9] ) )? (int)trim($data[9]):0;
    $clicksVirales = (isset( $data[10] ) )? (int)trim($data[10]):0;
    $links = (isset( $data[11] ) )? (int)trim($data[11],'"'):0;
    $ips = (isset( $data[12] ) )? cleanIP($data[12]):null;
    $navegadores = (isset( $data[13] ) )? (string)trim($data[13]):'';
    $plataformas = (isset( $data[14] ) )? (string)trim($data[14]):'';

    $stmt = $pdo->prepare("
        INSERT INTO estadistica (email, jyv, Badmail, Baja, fechaEnvio, fechaOpen, opens, opensVirales, fechaClick, clicks, clicksVirales, links, ips, navegadores, plataformas)
        VALUES (:email, :jyv, :Badmail, :Baja, :fechaEnvio, :fechaOpen, :opens, :opensVirales, :fechaClick, :clicks, :clicksVirales, :links, :ips, :navegadores, :plataformas)
    ");
    $stmt->execute([
        'email' => $email,
        'jyv' => $jyv,
        'Badmail' => $Badmail,
        'Baja' => $Baja,
        'fechaEnvio' => $fechaEnvio,
        'fechaOpen' => $fechaOpen,
        'opens' => $opens,
        'opensVirales' => $opensVirales,
        'fechaClick' => $fechaClick,
        'clicks' => $clicks,
        'clicksVirales' => $clicksVirales,
        'links' => $links,
        'ips' => $ips,
        'navegadores' => $navegadores,
        'plataformas' => $plataformas
    ]);
}

function convertDateFormat($date) {
    $dateTime = DateTime::createFromFormat('d/m/Y H:i', $date);
    return $dateTime ? $dateTime->format('Y-m-d H:i:s') : null;
}

function cleanIP($ip) {
    // 39 caracteres (para IPv4 e IPv6)
    $ip = trim($ip, '"');
    return (strlen($ip) <= 39) ? $ip : substr($ip, 0, 39);
}

