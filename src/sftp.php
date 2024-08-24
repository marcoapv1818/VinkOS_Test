<?php

/**
 * Maneja la conexión al servidor remoto y la descarga de archivos.
 */
function fetchFiles() {
    $config = require 'config.php';
    $remote_dir = $config['sftp']['remote_dir']; // Define the remote directory

    logProcess("Iniciando la conexión al servidor SFTP.");
    try {

        $connection = ssh2_connect($config['sftp']['host'], $config['sftp']['port']);
        if (!$connection) {
            throw new Exception('Connection failed');
        }
        logProcess("Conexión establecida con éxito al servidor SFTP.");

        if (!ssh2_auth_password($connection, $config['sftp']['username'], $config['sftp']['password'])) {
            throw new Exception('Authentication failed');
        }
        logProcess("Autenticación exitosa.");

        $sftp = ssh2_sftp($connection);
        if (!$sftp) {
            throw new Exception('SFTP subsystem not initialized');
        }
        
        $remoteDir = "ssh2.sftp://$sftp$remote_dir";
        $handle = opendir($remoteDir);
        if (!$handle) {
            throw new Exception('Failed to open remote directory');
        }
        logProcess("Directorio remoto abierto con éxito: $remote_dir");

        $files = [];
        while (($file = readdir($handle)) !== false) {

            if ($file === '.' || $file === '..') {
                continue;
            }
            if (preg_match('/^report_\d+\.txt$/', $file)) {
                $files[] = $file;
            }
        }
        closedir($handle);

        $successfulDownloads = [];
        foreach ($files as $file) {
            $local_file = __DIR__."/../tmp/$file";
            $remote_file = "$remoteDir/$file";

            try {
                
                $fileContent = file_get_contents($remote_file);
                if ($fileContent === false) {
                    throw new Exception("Failed to download $file");
                }

                if (file_put_contents($local_file, $fileContent) === false) {
                    throw new Exception("Failed to save $file locally");
                }

                logProcess("Archivo descargado y guardado localmente: $file");
                $successfulDownloads[] = $file;
            } catch (Exception $e) {
                logProcess("Error al descargar o guardar el archivo $file: " . $e->getMessage());
            }
        }

        logProcess("Proceso de descarga finalizado. Archivos descargados: " . implode(", ", $successfulDownloads));
        return $successfulDownloads;
        
    } catch (Exception $e) {
        
        logProcess("Error en el proceso de SFTP: " . $e->getMessage());
        return [];
    }
}


//EOF