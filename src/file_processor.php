<?php

require 'validation.php';
/**
 * Se encarga de leer los archivos, validar su contenido, y preparar los datos 
 */
function processFile($filePath) {
    $lines = file(__DIR__ . "/../tmp/".$filePath, FILE_IGNORE_NEW_LINES);
    $data = [];
    
    foreach ($lines as $key => $line) {
        if($key == 0 ) continue;

        $fields = str_getcsv($line);
        if( count($fields) != 15 ){
            logError($filePath, "Error en el layout de renglon: ->".$line);
            continue;
        }

        if ( !validateEmail( $fields[0] ) ){ 
            logError($filePath, "Error en el formato de email: ->".$line);
            continue;
        }
        
        if ( !validateVisitDateTime( $fields[4] ) ) { 
            logError($filePath, "Error en el formato fecha visita: ->".$line);
            continue;
        }
        
        if ( !validateDateTime($fields[5]) ){
            logError($filePath, "Error en el formato fecha Open: ->".$line);
            continue;
        }

        if ( !validateDateTime( $fields[8] ) ){
            logError($filePath, "Error en el formato fecha Click: ->".$line);
            continue;
        }
        
        $data[] = $fields;
    }
    
    return $data;
}
