<?php

require 'sftp.php';
require 'file_processor.php';
require 'data_handler.php';
require 'logger.php';

echo "************************************************************************************\n";

$zipFilePath = __DIR__ . '/../home/etl/visitas/bckp/all_files_backup.zip';
$zip = new ZipArchive();

if ($zip->open($zipFilePath, ZipArchive::CREATE) !== TRUE) {
    logProcess("Error: No se pudo crear el archivo ZIP.");
    exit("Error: No se pudo crear el archivo ZIP.");
}

$pdo = getConnection();

$files = fetchFiles();
foreach ($files as $file) {

    if (isFileProcessed($pdo, $file)) {
        logProcess("Archivo ya procesado: $file");
        continue;
    }
    
    // Procesar archivo
    $data = processFile($file);
   
    handleData($data);

    markFileAsProcessed($pdo, $file);

   
    if (!$zip->addFile(__DIR__ ."/../tmp/".$file, basename($file))) {
        logProcess("Error: No se pudo agregar el archivo $file al ZIP.");
        continue;
    }
        
    
    logProcess("Archivo procesado: $file");

}

if ($zip->close() !== TRUE) {
    logProcess("Error: No se pudo cerrar el archivo ZIP: $zipFilePath");
}

foreach ($files as $file) {
    unlink(__DIR__ . "/../tmp/".$file);
}

logProcess("Borramos archivos respaldados.");

logProcess("Archivos procesados, respaldados en un solo archivo ZIP y movidos al directorio de backup.");

echo "************************************************************************************\n";
//EOF