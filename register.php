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
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $telefono = trim($_POST['telefono']);
        $pais = strtoupper(trim($_POST['pais'])); 
        $provincia_estado = trim($_POST['provincia_estado']);
        $direccion = !empty($_POST['direccion']) ? trim($_POST['direccion']) : null;
        $email = strtolower(trim($_POST['email']));
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $tipo_usuario = ucfirst(strtolower(trim($_POST['tipo_usuario'])));

        $stmt = $conn->prepare("INSERT INTO usuarios(nombre, apellido, telefono, pais, provincia_estado, direccion, email, password, tipo_usuario) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $nombre, $apellido, $telefono, $pais, $provincia_estado, $direccion, $email, $password, $tipo_usuario);
        
        if ($stmt->execute()) {
            $email_folder = str_replace(['@', '.'], '_', $email);
            $dir_path = "countries/$pais/$tipo_usuario/$email_folder";
            
            if (!file_exists($dir_path)) {
                mkdir($dir_path, 0777, true);
            }

            // Dependiendo del tipo de usuario, creamos un index.php diferente
            switch ($tipo_usuario) {
                case 'Dueño':
                    $index_content = "<?php echo 'Bienvenido, dueño de mascota'; ?>";
                    break;
                case 'Rescatista':
                    $index_content = "<?php echo 'Bienvenido, rescatista de mascotas'; ?>";
                    break;
                case 'Adoptante':
                    $index_content = "<?php echo 'Bienvenido, adoptante de mascotas'; ?>";
                    break;
                default:
                    $index_content = "<?php echo 'Bienvenido a tu perfil'; ?>";
                    break;
            }
            
            // Guardar el contenido del index.php en el directorio del usuario
            file_put_contents("$dir_path/index.php", $index_content);
            
            // Redirigir a la página del directorio correspondiente
            header("Location: $dir_path/index.php");
            exit();
        } else {
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
    <title> Registro | Find My Pet</title>
    
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
        <form action="register.php" method="post" class="mt-4">
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
                    <option value="PER">Perú</option>
                    <option value="URY">Uruguay</option>
                    <option value="VEN">Venezuela</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Provincia/Estado</label>
                <input type="text" name="provincia_estado" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Dirección (Opcional)</label>
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
