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

$email = strtolower($user['email']);
$pais = strtoupper(substr($user['pais'], 0, 3)); // Primeras tres letras en mayúsculas
$tipo_usuario = strtolower($user['tipo_usuario']);

// Definir la ruta de la carpeta personalizada
$base_path = "./countries/$pais/$email/$tipo_usuario";

// Verificar si la carpeta existe, si no, crearla
if (!is_dir($base_path)) {
    mkdir($base_path, 0777, true);
}

// Crear un index personalizado si no existe
$index_path = "$base_path/index.php";
if (!file_exists($index_path)) {
    $index_content; 
    $index_content .= "<!DOCTYPE html>\n<html lang='es'>\n<head>\n<meta charset='UTF-8'>\n<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n<title>Dashboard</title>\n";
    $index_content .= "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'>\n</head>\n<body>\n";
    $index_content .= "<nav class='navbar navbar-expand-lg navbar-light bg-light'>\n<div class='container-fluid'>\n<a class='navbar-brand' href='#'>Find My Pet</a>\n<button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>\n<span class='navbar-toggler-icon'></span>\n</button>\n<div class='collapse navbar-collapse' id='navbarNav'>\n<ul class='navbar-nav'>\n<li class='nav-item'><a class='nav-link' href='#'>Inicio</a></li>\n<li class='nav-item'><a class='nav-link' href='#'>Perfil</a></li>\n<li class='nav-item'><a class='nav-link' href='#'>Configuración</a></li>\n</ul>\n</div>\n</div>\n</nav>\n</body>\n</html>";
    file_put_contents($index_path, $index_content);
}

// Redirigir al usuario a su carpeta correspondiente
header("Location: $index_path");
exit();

