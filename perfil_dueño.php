<?php 
    session_start();
    $host = 'localhost';
    $user = 'root';
    $password =  '';
    $dbname = 'findmypet';
    
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexion fallida: " . $conn->connect_error);
    }

    
?>