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
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $pais = $_POST['pais'];
        $provincia_estado = $_POST['provincia_estado'];
        $direccion = !empty($_POST['direccion']) ? $_POST['direccion'] : null;
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $tipo_usuario = $_POST['tipo_usuario'];

        $stmt = $conn->prepare("INSERT INTO usuarios(nombre, apellido, telefono, pais, provincia_estado, direccion, email, password, tipo_usuario) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $nombre, $apellido, $telefono, $pais, $provincia_estado, $direccion, $email, $password, $tipo_usuario);
        $stmt->execute();
        if ($stmt-execute()) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $json = file_get_contents("http://ip-api.com/json/$ip");
            $data = json_decode($json, true);
            $countryCode = strtolower($date['countryCode']);

            $allowedCountries = [
                "ar",
                "bo",
                "br",
                "cl",
                "co",
                "cr",
                "cu",
                "ec",
                "es",
                "gu",
                "guy",
                "hai",
                "hon",
                "jam",
                "mx",
                "nic",
                "pa",
                "par",
                "pe",
                "rd",
            ];
            if (in_array($countryCode, $allowedCountries)) {
                header("Location: /" . $countryCode . "/index.php");
            }else{
                header("Location: /index.php");
            }
            exit();
        }else{
            echo "Error al registrar usuario";
        }
        $stmt->close();
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registro |Find My Pet</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    
    <style>
        #map {
            height: 500px;
        }
    </style>
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
            <h2 class="text-center">Registrarse</h2>
            <form action="" method="post" class="mt-4">
                <div class="mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">País</label>
                    <input type="text" name="pais" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Provincia/Estado</label>
                    <input type="text" name="provincia_estado" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Direccion(Opcional)</label>
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
                    <label class="form-label">Telefono</label>
                    <input type="phone" name="telefono" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Tipo de Usuario</label>
                    <select name="tipo_usuario" class="form-select" required>
                        <option value="Dueño">Dueño</option>
                        <option value="Rescatista">Rescatista</option>
                        <option value="Adoptante">Adoptante</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
            </form>
           </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>