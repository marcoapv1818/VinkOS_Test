<?php

//Este archivo contiene las credenciales SFTP, la configuración de la base de datos y otros parámetros globales.
return [
    'sftp' => [
        'host' => 'localhost',
        'username' => 'vinkOS',
        'port'=>'2222',
        'password' => 'password',
        'remote_dir' => '/archivosVisitas',
    ],
    'db' => [
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'my-secret-pw',
        'dbname' => 'vinkOS',
        'port'=> '3306'
    ],
];


//EOF