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
$conn->close();

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
$user_dashboard_path = "$user_folder/index.php";

// Si la carpeta no existe, se crea
if (!file_exists($user_folder)) {
    mkdir($user_folder, 0777, true);
}

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

function generate_dashboard_content($user, $tipo_usuario) {
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
                    <li class='nav-item'><a class='nav-link' href='#'>Agregar Mascota</a></li> 
                    <li class='nav-item'><a class='nav-link' href='logout.php'>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class='container mt-5'>
        <div class='card shadow p-4'>
            <h1 class='text-center'>Bienvenido, " . htmlspecialchars($user['nombre']) . " " . htmlspecialchars($user['apellido']) . "</h1>
            <p class='text-center'>Este es su perfil como <strong>" . ucfirst($tipo_usuario) . "</strong></p>
            <p class='text-center'><strong>Email: </strong> " . htmlspecialchars($user['email']) . "</p>
        </div>
        <form action=''>
            <button>Hola Mundo</button>
        </form>
    </div>

    <footer class='bg-dark text-light text-center py-3 mt-5'>
        <p>&copy; " . date('Y') . " <a href='https://pablonicolasgarcia.infinityfreeapp.com' target='blank'> Pablo Nicoals Garcia @ FindMyPet. Todos los derechos reservados.</p>
    </footer>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>
   </body>
   </html>";
}
?>
