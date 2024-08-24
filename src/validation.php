<?php

/**
 * Validación de los datos
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateVisitDateTime($dateTime) {
    $format = 'd/m/Y H:i';
    $dt = DateTime::createFromFormat($format, $dateTime);
    return $dt && $dt->format($format) === $dateTime;
}

function validateDateTime($dateTime) {
    if (empty($dateTime)) {
        return true; 
    }

    $format = 'd/m/Y H:i';
    $_date_time = DateTime::createFromFormat($format, $dateTime);
    return $_date_time && $_date_time->format($format) === $dateTime;
}

function isFileProcessed($pdo, $filename) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM processed_files WHERE filename = :filename");
    $stmt->execute(['filename' => $filename]);
    return $stmt->fetchColumn() > 0;
}

function markFileAsProcessed($pdo, $filename) {
    $stmt = $pdo->prepare("INSERT INTO processed_files (filename) VALUES (:filename)");
    $stmt->execute(['filename' => $filename]);
}


//EOF