<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'findmypet';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); // Se corrigió el error tipográfico
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true); // Seguridad contra fijación de sesión
                
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['nombre'];

                // Redirigir al dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Credenciales incorrectas.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Find My Pet</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a href="#" class="navbar-brand">Find My Pet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="login.php">Iniciar Sesión</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Registrarse</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Iniciar Sesión</h2>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form action="login.php" method="post"> <!-- Se mantiene la validación en el mismo archivo -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>
    <p class="mt-3 text-center">¿No tiene cuenta? <a href="register.php">Regístrese</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

