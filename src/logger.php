<?php
/**
 * Manejo de Errores y logs de Proceso
 */
function logError($sourceFile, $message) {
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO errores (source_file, error_message, timestamp)
        VALUES (:source_file, :error_message, NOW())
    ");
    $stmt->execute([
        'source_file' => $sourceFile,
        'error_message' => $message
    ]);

    file_put_contents('error_data_log.txt', "$sourceFile - $message\n", FILE_APPEND);
}

function logProcess($message) {
    $logFile = __DIR__ . '/../logs/process_log.txt';  
    $currentTime = date('Y-m-d H:i:s');
    
    $formattedMessage = "[$currentTime] $message" . PHP_EOL;
    echo "[$currentTime] $message"."\n";
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}
