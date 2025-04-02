<?php 
// Inicia la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'findmypet';

// Verificación de autenticación
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obtención de los datos del usuario
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nombre, apellido, LOWER(email) AS email, UPPER(pais) AS pais, LOWER(tipo_usuario) AS tipo_usuario FROM usuarios WHERE id = ?");
if (!$stmt) {
   die("Query Failure: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Error: User not found");
}

$pais = $user['pais'];
$tipo_usuario = $user['tipo_usuario'];
$email_folder = preg_replace('/[^a-zA-Z0-9._-]/', '_', strtolower($user['email']));

// Listado de países y usuarios válidos
$paises_validos = ["ARG", "BOL", "BRA", "CHL", "COL", "COR", "CUB", "COS", "REP", "ELS", "GUA", "GUY", "HON", "JAM", "MEX", "NIC", "PAN", "PAR", "URU", "VEN"];
$tipo_usuario_validos = ["dueño", "rescatista", "adoptante"];

if (!in_array($pais, $paises_validos) || !in_array($tipo_usuario, $tipo_usuario_validos)) {
    die("Error: Pais o rol no válidos");
}

// Construcción del path
$base_dir = "countries/$pais/$tipo_usuario";
$user_folder = "$base_dir/$email_folder";
$profile_folder = "$user_folder/perfil/index.php";
$user_dashboard_path = "$user_folder/index.php";

// Si la carpeta no existe, se crea
if (!file_exists($user_folder)) {
    mkdir($user_folder, 0777, true);
}
if (!file_exists($profile_folder)) {
    mkdir($profile_folder, 0777, true);
}

$profile_path = "$profile_folder/index.php";

//Generacion del contenido profile/index.php
if (!file_exists($profile_path)) {
    file_put_contents($profile_path, generate_profile_content($user));
}

// Almacenar los datos en la tabla datos_usuarios
$stmt = $conn->prepare("INSERT INTO datos_usuarios (user_id, nombre, apellido, email, pais, estado, telefono, tipo_mascota, nombre_mascota, raza_mascota) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellido = VALUES(apellido), email = VALUES(email), pais = VALUES(pais), tipo_usuario = VALUES(tipo_usuario)");
if ($stmt) {
    $stmt->bind_param('isssss', $user_id, $user['nombre'], $user['apellido'], $user['email'], $pais, $tipo_usuario);
    $stmt->execute();
    $stmt->close();
} else {
    die("Error al almacenar datos del usuario: " . $conn->error);
}
// Almacenar los datos en la la tabla perfil_dueño
$stmt = $conn->prepare("INSERT INTO perfil_dueño (usuario_id, foto_animal, raza, cantidad, nombre_animal) values(?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE foto_animal = VALUES(foto_animal), raza = VALUES(raza), cantidad = VALUES(cantidad), nombre_animal = VALUES(nombre_animal)");
if ($stmt) {
    $foto_animal = '';
    $raza = '';
    $cantidad = 0;
    $nombre_animal = '';
    $stmt->bind_param('issis', $user_id, $foto_animal, $raza, $cantidad, $nombre_animal);
    $stmt->execute();
    $stmt->close();
}else{
    die("Error al almacenar datos del usuario : " . $conn->error);
}

$conn->close();

// Generación dinámica del dashboard si no existe
if (!file_exists($user_dashboard_path)) {
    file_put_contents($user_dashboard_path, generate_dashboard_content($user, $tipo_usuario));
}
if (!file_exists($user_dashboard_path)) {
    die("Error: El archivo no se generó correctamente");
}

// Redirección al dashboard según usuario
header("Location: $user_dashboard_path");
exit();

function generate_profile_content($user) {
    return "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Perfil - " . htmlspecialchars($user['nombre']) . "</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
    <div class='container mt-5'>
        <div class='card p-4'>
            <h2>Configuración del Perfil</h2>
            <form action='guardar_mascota.php' method='POST'>
                <div class='mb-3'>
                    <label class='form-label'>Nombre de la Mascota</label>
                    <input type='text' name='nombre_animal' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label'>Raza</label>
                    <input type='text' name='raza' class='form-control' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label'>Cantidad</label>
                    <input type='number' name='cantidad' class='form-control' required>
                </div>
                <input type='hidden' name='user_id' value='" . $_SESSION['user_id'] . "'>
                <button type='submit' class='btn btn-primary'>Guardar Mascota</button>
            </form>
        </div>
    </div>
    </body>
    </html>";
}



function generate_dashboard_content($user, $tipo_usuario) {
    $contenido_dinamico = "";
    $boton_texto = "";
    $boton_link = "#";
    $boton_perfil = "Mi perfil";
    $boton_perfil_link = "#";
    
    switch ($tipo_usuario) {
        case "dueño":
            $contenido_dinamico = "<h2>Sección dueño</h2>";
            $boton_texto = "Agregar Mascota";
            $boton_link = "agregar_mascota.php";
            break;
        case "rescatista":
            $contenido_dinamico = "<h2>Sección rescatista</h2>";
            $boton_texto = "Reportar Rescate";
            $boton_link = "reportar_rescate.php";
            break;
        case "adoptante":
            $contenido_dinamico = "<h2>Sección adoptante</h2>";
            $boton_texto = "Buscar Mascota";
            $boton_link = "buscar_mascota.php";
            break;
    }

    return "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Bienvenido - " . htmlspecialchars($user['nombre']) . "</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
    <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
        <div class='container'>
            <a class='navbar-brand' href='#'>FindMyPet</a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav'>
                <span class='navbar-toggler-icon'></span>
            </button>
            <div class='collapse navbar-collapse' id='navbarNav'>
                <ul class='navbar-nav ms-auto'>
                    <li class='nav-item'><a class='nav-link' href='#'>Inicio</a></li>
                    <li class='nav-item'><a class='nav-link' href='#'>Perfil</a></li>
                    <li class='nav-item'><a class='nav-link' href='" . htmlspecialchars($boton_link) . "'>" . htmlspecialchars($boton_texto) . "</a></li> 
                    <li class='nav-item'><a class='nav-link' href='logout.php'>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class='container mt-5'>
        <div class='card shadow p-4'>
            <h1 class='text-center'>Bienvenido, " . htmlspecialchars($user['nombre']) . " " . htmlspecialchars($user['apellido']) . "</h1>
        </div>
    </div>
    
    </body>
    </html>;
}
