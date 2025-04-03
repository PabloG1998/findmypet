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
            
            
        // Crear el directorio del usuario
if (!file_exists($dir_path)) {
    mkdir($dir_path, 0777, true);
}
    //Crear archivo perfil
    $perfil_path = "$dir_path/perfil.php";

    //Verificacion de la existencia del fichero
    if (!file_exists($perfil_path)) {
        //Contenido
        $perfil_content = <<<HTML
    <!DOCTYPE html>
    <html lang='es'>
    <head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dashboard</title>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'>
    </head>
    <body>
    <nav class='navbar navbar-expand-lg navbar-light bg-light'>
    <div class='container-fluid'>
    <a class='navbar-brand' href='#'>Find My Pet</a>
    <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>
    <span class='navbar-toggler-icon'></span>
    </button>
    <div class='collapse navbar-collapse' id='navbarNav'>
    <ul class='navbar-nav'>
    <li class='nav-item'><a class='nav-link' href='./index.php'>Inicio</a></li>
    <li class='nav-item'><a class='nav-link' href='./perfil.php'>Perfil</a></li>
    <li class='nav-item'><a class='nav-link' href='#'>Configuración</a></li>
    </ul>
    </div>
    </div>
    </nav>

        <div class="container vh-100 d-flex justify-content-center align-items-center">
            
            <form action="agregar_animal.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Nombre de la Mascota:</label>
                    <input class="form-control" type="text" name="nombre" placeholder="Ingrese su nombre" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Mascota:</label>
                    <input class="form-control" type="text" name="tMascota" placeholder="PERRO, GATO">
                  </div>

                <div class="mb-3">
                <label class="form-label">Raza de la mascota</label>
                <input class="form-control" type="text" name="" placeholder="Ingrese la raza" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input class="form-control" type="number" name="cMascota" placeholder="Ingrese la cantidad de mascotas">
                
            </div>
                <div class="mb-3">
                    <label class="form-label">Foto de la mascota</label>
                    <input class="form-control" type="file" name="fMascota" id="fMascota">
                </div>
                <button class="btn btn-success" type="submit">Agregar Mascota</button>
            </form>
        </div>

    </body>
    </html>
    HTML;
    }
        //Dump en el archivo
        file_put_contents($perfil_path, $perfil_content);
// Plantilla base del dashboard
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

// Agregar contenido específico según el tipo de usuario
switch ($tipo_usuario) {
    case 'Rescatista':
        $index_content .= "<h2>Bienvenido, Rescatista</h2>\n";
        $index_content .= "<p>Aquí podrás gestionar reportes de mascotas encontradas.</p>\n";
        break;

    case 'Adoptante':
        $index_content .= "<h2>Bienvenido, Adoptante</h2>\n";
        $index_content .= "<p>Aquí podrás ver y adoptar mascotas que necesitan un hogar.</p>\n";
        break;

    default:
        $index_content .= "<h2>Bienvenido a tu perfil</h2>\n";
        break;
}

$index_content .= "</div>\n</body>\n</html>";

// Guardar el contenido en el archivo index.php del usuario
file_put_contents("$dir_path/index.php", $index_content);

            
            // Redirigir a la página del directorio correspondiente
           // header("Location: $dir_path/index.php");
           //Redirigir al login.php
           header("Location: login.php");
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
