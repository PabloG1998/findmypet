<?php 
require_once('config/Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_completo = trim($_POST['nombre_completo']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $pais = trim($_POST['pais']);
    $tipo_usuario = trim($_POST['tipo_usuario']);
    $provincia_estado = trim($_POST['provincia_estado']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);

    // Validación de campos obligatorios
    if (empty($nombre_completo) || empty($apellido) || empty($email) || empty($_POST['password']) || empty($pais) || empty($tipo_usuario) || empty($telefono)) {
        die("Todos los campos obligatorios deben ser completados.");
    }

    // Verificar si el correo ya está registrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("El correo ya está registrado.");
    }

    // Insertar el usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre_completo, apellido, email, password, pais, provincia_estado, direccion, telefono, tipo_usuario) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$nombre_completo, $apellido, $email, $password, $pais, $provincia_estado, $direccion, $telefono, $tipo_usuario])) {
        
        // Normalizar el email para el nombre de carpeta
        $email_dir = preg_replace("/[^a-zA-Z0-9]/", "_", $email);
        $dir_path = "countries/$pais/$tipo_usuario/$email_dir";

        // Crear directorio si no existe
        if (!file_exists($dir_path)) {
            mkdir($dir_path, 0777, true);
        }

        
        header("refresh:2; url=login.php"); // Redirigir después de 2 segundos
        exit;
    } else {
        echo "Error al registrar el usuario.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find My Pet | Registro</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a href="#" class="navbar-brand">Find My Pet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a href="login.php" class="nav-link">Iniciar Sesion</a></li>
                <li class="nav-item"><a href="register.php" class="nav-link">Registrarse</a></li>
            </ul>
        </div>

    </div>
</nav>

    <div class="container mt-5">
        <h2 class="text-center">Registrarse</h2>
        <form action="register.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <input type="text" name="nombre_completo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">País</label>
                <select name="pais" class="form-select" required>
                    <option value="ARG">Argentina</option>
                    <option value="BOL">Bolivia</option>
                    <option value="BRA">Brasil</option>
                    <option value="CHL">Chile</option>
                    <option value="COL">Colombia</option>
                    <option value="CRI">Costa Rica</option>
                    <option value="CUB">Cuba</option>
                    <option value="DOM">República Dominicana</option>
                    <option value="ECU">Ecuador</option>
                    <option value="SLV">El Salvador</option>
                    <option value="GTM">Guatemala</option>
                    <option value="GUY">Guyana</option>
                    <option value="HND">Honduras</option>
                    <option value="JAM">Jamaica</option>
                    <option value="MEX">México</option>
                    <option value="NIC">Nicaragua</option>
                    <option value="PAN">Panamá</option>
                    <option value="PRY">Paraguay</option>
                    <option value="URY">Uruguay</option>
                    <option value="VEN">Venezuela</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Provincia/Estado</label>
                <input type="text" name="provincia_estado" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Direccion (Opcional)</label>
                <input type="text" name="direccion" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input type="phone" name="telefono" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Usuario</label>
                <select name="tipo_usuario" class="form-select">
                    <option value="Dueño">Dueño</option>
                    <option value="Rescatista">Rescatista</option>
                    <option value="Adoptante">Adoptante</option>
                    <option value="Transito">Transito</option>
</select>
</div>
<button type="submit" class="btn btn-primary w-100">Registrarse</button>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>