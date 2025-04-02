<?php 
// Inicia la sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'findmypet';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Manejo de autenticación y redirección
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, pais, tipo_usuario FROM usuarios WHERE id = ?");
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

// Crear nombre de carpeta único
$email = strtolower(str_replace(['@','.' ], '_', $user['email']));
$pais = strtoupper(substr($user['pais'], 0, 3)); // Primeras tres letras en mayúsculas
$tipo_usuario = strtolower($user['tipo_usuario']);

// Definir la ruta base de la carpeta del usuario
$base_path = "countries/$pais/$tipo_usuario/$email";

// Verificar si la carpeta existe, si no, crearla
if (!is_dir($base_path)) {
    mkdir($base_path, 0777, true);
}

// Definir la ruta del archivo index.php dentro de la carpeta del usuario
$index_path = "$base_path/index.php";

if (!file_exists($index_path)) {
    $index_content = "<!DOCTYPE html>\n<html lang='es'>\n<head>\n";
    $index_content .= "<meta charset='UTF-8'>\n<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n<title>Dashboard</title>\n";
    $index_content .= "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'>\n</head>\n<body>\n";
    
    $index_content .= "<nav class='navbar navbar-expand-lg navbar-light bg-light'>\n";
    $index_content .= "<div class='container-fluid'>\n<a class='navbar-brand' href='#'>Find My Pet</a>\n";
    $index_content .= "<button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>\n";
    $index_content .= "<span class='navbar-toggler-icon'></span>\n</button>\n<div class='collapse navbar-collapse' id='navbarNav'>\n<ul class='navbar-nav'>\n";
    $index_content .= "<li class='nav-item'><a class='nav-link' href='#'>Inicio</a></li>\n";
    $index_content .= "<li class='nav-item'><a class='nav-link' href='perfil.php'>Perfil</a></li>\n";
    $index_content .= "<li class='nav-item'><a class='nav-link' href='#'>Configuración</a></li>\n";
    $index_content .= "</ul>\n</div>\n</div>\n</nav>\n";

    $index_content .= "<div class='container mt-5'>\n";

    if ($tipo_usuario == 'rescatista') {
        $index_content .= "<h1 class='mb-3'>Bienvenido Rescatista</h1>\n";
        $index_content .= "<p>Aquí puedes gestionar reportes de mascotas perdidas.</p>\n";
        $index_content .= "<a class='btn btn-danger' href='#'> Emitir una alerta</a>\n";
    } elseif ($tipo_usuario == 'dueño') {
        $index_content .= "<h1 class='mb-3'>Bienvenido Dueño</h1>\n";
        $index_content .= "<p>Aquí puedes ver el estado de tus mascotas registradas.</p>\n";
        $index_content .= "<a class='btn btn-success' href='#'>Ver mis mascotas</a>\n";
        $index_content .= "<br>\n<a class='btn btn-danger' href='#'>Emitir una alerta Rescatistas</a>\n";
    } elseif ($tipo_usuario == 'adoptante') {
        $index_content .= "<h1 class='mb-3'>Bienvenido Adoptante</h1>\n";
        $index_content .= "<p>Aquí puedes buscar mascotas disponibles para adopción.</p>\n";
        $index_content .= "<a class='btn btn-success' href='#'>Ver animales en adopción</a>\n";
        $index_content .= "<br>\n<a class='btn btn-primary' href='#'>Contactar con Rescatistas</a>\n";
    }

    $index_content .= "</div>\n"; // Cierre del container
    $index_content .= "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>\n";
    $index_content .= "</body>\n</html>";

    file_put_contents($index_path, $index_content);
}

// Redirigir al usuario a su carpeta (debería abrir automáticamente index.php)
header("Location: $base_path/");
exit();
