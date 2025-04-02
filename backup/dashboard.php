<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'findmypet';

// Verifica la autenticación
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtención de datos del usuario
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nombre, apellido, LOWER(email) AS email, UPPER(pais) AS pais, LOWER(tipo_usuario) AS tipo_usuario FROM usuarios WHERE id = ?");
if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Error: Usuario no encontrado.");
}

$pais = $user['pais'];
$tipo_usuario = $user['tipo_usuario'];
$email = strtolower($user['email']); // Se usará como nombre de carpeta
$email_folder = preg_replace('/[^a-zA-Z0-9._-]/', '_', $email); // Sanitiza el nombre del directorio

// Listado de países y tipos de usuario válidos
$paises_validos = ['ARG', 'BOL', 'BRA', 'CHL', 'COL', 'COR', 'CUB', 'ECU', 'COS', 'REP', 'ELS', 'GUA', 'GUY', 'HON', 'JAM', 'MEX', 'NIC', 'PAN', 'PAR', 'URU', 'VEN'];
$tipo_usuario_validos = ['dueño', 'rescatista', 'adoptante'];

if (in_array($pais, $paises_validos) && in_array($tipo_usuario, $tipo_usuario_validos)) {
    // Ruta dinámica basada en email
    $base_dir = "countries/" . $pais . "/" . $tipo_usuario;
    $user_folder = $base_dir . "/" . $email_folder; // Usa el email como nombre de directorio

    if (!file_exists($user_folder)) {
        mkdir($user_folder, 0777, true);
    }

    $user_dashboard_path = $user_folder . "/index.php";

    // Verifica si ya existe el archivo index.php para el usuario
    if (!file_exists($user_dashboard_path)) {
        $index_file_content = generate_dashboard_content($user, $tipo_usuario);
        file_put_contents($user_dashboard_path, $index_file_content);
    }

    header("Location: " . $user_dashboard_path);
    exit();
} else {
    die("Error: País o rol no válidos.");
}

$conn->close();

/**
 * Genera el contenido del archivo index.php para el usuario.
 */
function generate_dashboard_content($user, $tipo_usuario) {
    $contenido = "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Bienvenido - " . htmlspecialchars($user['nombre']) . "</title>
    <!-- Aquí va el CSS para el navbar -->
    <link rel='stylesheet' href='navbar.css'>
</head>
<body>
    <header>
        <nav class='navbar'>
            <ul class='navbar-nav'>
                <li><a href='#'>Inicio</a></li>
                <li><a href='#'>Perfil</a></li>
                <li><a href='#'>Mis Mascotas</a></li>
                <li><a href='#'>Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class='container'>
        <h1>Bienvenido, " . htmlspecialchars($user['nombre']) . " " . htmlspecialchars($user['apellido']) . "</h1>
        <p>Este es tu perfil como <strong>" . ucfirst($tipo_usuario) . "</strong></p>
        <p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";

    switch ($tipo_usuario) {
        case 'dueño':
            $contenido .= "<h3>Gestión de Mascotas</h3><p>Ver y administrar tus mascotas.</p>";
            break;
        case 'rescatista':
            $contenido .= "<h3>Gestión de Rescates</h3><p>Visualiza tus rescates realizados.</p>";
            break;
        case 'adoptante':
            $contenido .= "<h3>Gestión de Adopciones</h3><p>Solicitar adopciones o ver tus adopciones anteriores.</p>";
            break;
    }

    $contenido .= "</div>
</body>
</html>";

    return $contenido;
}
?>
