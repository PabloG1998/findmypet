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
    $ciudad = trim($_POST['ciudad']);

    if (empty($nombre_completo) || empty($apellido) || empty($email) || empty($_POST['password']) || empty($pais) || empty($tipo_usuario) || empty($telefono) || empty($ciudad)) {
        die("Todos los campos obligatorios deben ser completados.");
    }

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("El correo ya está registrado.");
    }

    $sql = "INSERT INTO usuarios (nombre_completo, apellido, email, password, pais, provincia_estado, direccion, telefono, tipo_usuario, ciudad) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$nombre_completo, $apellido, $email, $password, $pais, $provincia_estado, $direccion, $telefono, $tipo_usuario, $ciudad])) {

        $dir_path = crearCarpetaUsuario($pais, $tipo_usuario, $email);
        $indexContent = generateIndex($email, $tipo_usuario, $pais);
        $profileContent = generateProfile($nombre_completo, $apellido, $email, $tipo_usuario, $pais);
        $petContent = generatePetForm($email, $tipo_usuario, $pais);
        $veterinarioContent = generateVeterinario();
        $perdidaContent = generateLostForm();
        file_put_contents("$dir_path/index.php", $indexContent);
        file_put_contents("$dir_path/perfil.php", $profileContent);
        file_put_contents("$dir_path/misMascotas.php", $petContent);
        file_put_contents("$dir_path/veterinarios.php", $veterinarioContent);
        file_put_contents("$dir_path/denunciarPerdida.php", $perdidaContent);
        

        header("refresh:2; url=login.php");
        exit;
    } else {
        echo "Error al registrar el usuario.";
    }
}

function crearCarpetaUsuario($pais, $tipo_usuario, $email) {
    $email_dir = preg_replace("/[^a-zA-Z0-9]/", "_", $email);
    $dir_path = "countries/$pais/$tipo_usuario/$email_dir";

    if (!file_exists($dir_path)) {
        mkdir($dir_path, 0777, true);
    }

    return $dir_path;
}

function crearCarpetaImg() {
  $dirPathImg = "countries/$pais/$tipo_usuario/$email_dir";
  if (!file_exists($dirPathImg)) {
    mkdir($dirPathImg, 0777, true);
  }
  return $dirPathImg;
}

function generateIndex($email, $tipo_usuario, $pais) {
    $menuItems = '';

    switch ($tipo_usuario) {
        case 'Dueño':
            $menuItems = '<li class="nav-item"><a class="nav-link" href="misMascotas.php">Mis Mascotas</a></li>
            <li class="nav-item"><a class="nav-link" href="veterinarios.php">Veterinarios</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Reportar Perdida</a></li>';
            break;
        case 'Adoptante':
            $menuItems = '<li class="nav-item"><a class="nav-link" href="#">Mascotas Disponibles</a>
            </li><li class="nav-item"><a class="nav-link" href="#">Mi historial de Adopcion</a></li>
            <li class="nav-item"><a class="nav-link" href="veterinarios.php">Veterinarios</a></li>';
            break;
        case 'Rescatista':
            $menuItems = '<li class="nav-item"><a class="nav-link" href="#">Registrar Rescate</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Lista de Mascotas Rescatadas</a></li>
            <li class="nav-item"><a class="nav-link" href="veterinarios.php">Veterinarios</a></li>';
            break;
        case 'Transito':
            $menuItems = '<li class="nav-item"><a class="nav-link" href="#">Mascotas en Tránsito</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Solicitudes de Adopcion</a></li>
            <li class="nav-item"><a class="nav-link" href="veterinarios.php">Veterinarios</a></li>';
            break;
        default:
            $menuItems = '<li class="nav-item">
            <a class="nav-link" href="#">Inicio</a></li>';
            break;
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
      <a class="navbar-brand" href="#">FindMyPet</a>
      <div class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              $menuItems
          </ul>
          <span class="navbar-text">
              $email - $tipo_usuario - $pais
          </span>
      </div>
  </div>
</nav>
<div class="container mt-4">
  <h1>Bienvenido, $tipo_usuario</h1>
  <p>Banco de Datos - Mascotas</p>
</div>
</body>
</html>
HTML;
}
//Formulario para denunciar una pérdida de un animal
function generateLostForm() {
  return null;
}

function generateProfile($nombre_completo, $apellido, $email, $tipo_usuario, $pais) {
    return <<<HTML
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido - $apellido, $nombre_completo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Find My Pet</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="perfil.php">Perfil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="misMascotas.php"> Mis Mascotas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="denunciarPerdida.php">Denunciar Perdida</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar Sesion</a>
        </li>
    </ul>
      <span class="navbar-text">
        $email ($tipo_usuario - $pais)
      </span>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;
}

function generateVeterinario() {
  return <<<HTML
<?php
  session_start();
  require_once('../../../../config/Database.php');

  \$database = new Database();
  \$conn = \$database->getConnection();

  \$user_id = \$_SESSION['user_id'] ?? null;
  if (!\$user_id) {
      die("Acceso Denegado, se debe iniciar sesión");
  }

  // Obtener los datos del usuario actual
  \$stmt = \$conn->prepare("SELECT pais, provincia_estado, ciudad FROM usuarios WHERE id = :id");
  \$stmt->execute([':id' => \$user_id]);
  \$user = \$stmt->fetch(PDO::FETCH_ASSOC);

  if (!\$user) {
      die("Usuario no encontrado");
  }

  \$pais_usuario = \$user['pais'];
  \$provincia_estado_usuario = \$user['provincia_estado'];
  \$ciudad_usuario = \$user['ciudad'];

  // Buscar veterinarias que coincidan con los datos del usuario
  \$stmt = \$conn->prepare("
      SELECT * FROM veterinarias 
      WHERE pais = :pais 
      AND ciudad = :ciudad 
      AND (provincia = :provincia_estado OR estado = :provincia_estado)
  ");

  \$stmt->execute([
      ':pais' => \$pais_usuario,
      ':provincia_estado' => \$provincia_estado_usuario,
      ':ciudad' => \$ciudad_usuario,
  ]);

  \$veterinarias = \$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Find My Pet | Veterinarias</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Find My Pet</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="../index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="perfil.php">Perfil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="misMascotas.php"> Mis Mascotas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="denunciarPerdida.php">Denunciar Perdida</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar Sesion</a>
        </li>
    </ul>
      <span class="navbar-text">
        $email ($tipo_usuario - $pais)
      </span>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h2 class="text-center mb-4">Veterinarias</h2>

  <?php if (!empty(\$veterinarias)): ?>
    <table class="table table-bordered table-striped" id="tabla-veterinarias">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Teléfono</th>
          <th>Dirección</th>
          <th>Página Web</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (\$veterinarias as \$v): ?>
          <tr>
            <td><?= htmlspecialchars(\$v['nombre_veterinaria']) ?></td>
            <td><?= htmlspecialchars(\$v['telefono']) ?></td>
            <td><?= htmlspecialchars(\$v['direccion']) ?></td>
            <td>
              <?php if (!empty(\$v['pagina_web'])): ?>
                <a class="btn btn-danger" href="<?= htmlspecialchars(\$v['pagina_web']) ?>" target="_blank">Ver Web</a>
              <?php else: ?>
                No disponible
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-warning text-center">
      No se encontraron veterinarias en la zona
    </div>
  <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  \$(document).ready(function () {
    \$('#tabla-veterinarias').DataTable({
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
      }
    });
  });
</script>

</body>
</html>
HTML;
}


function generatePetForm($email, $tipo_usuario, $pais) {
return <<<HTML
 <?php
     require('../../../../config/Database.php');
     \$database = new Database();
     \$conn = \$database->getConnection();
    if (\$_SERVER["REQUEST_METHOD"] == "POST") {
        \$nombre_mascota = trim(\$_POST['nombre_mascota']);
        \$tipo_mascota = trim(\$_POST['tipo_mascota']);
        \$raza_mascota = trim(\$_POST['raza_mascota']);
        \$edad_mascota = (\$_POST['edad_mascota']);
        \$caracteristicas_mascota = trim(\$_POST['caracteristicas_mascota']);
        \$detalles_utiles = trim(\$_POST['detalles_utiles']);
        \$vacunas = trim(\$_POST['vacunas']);
        \$fecha_vacunacion = trim(\$_POST['fecha_vacunacion']);
        
        if (
        empty(\$nombre_mascota) || empty(\$tipo_mascota) || empty(\$raza_mascota) || empty(\$edad_mascota) ||
        empty(\$caracteristicas_mascota) || empty(\$detalles_utiles) || empty(\$vacunas) || empty(\$fecha_vacunacion)
    ) {
        die("Por favor, completá todos los campos.");
    }
        
    try {
        \$sql = "INSERT INTO mascotas (nombre_mascota, tipo_mascota, raza_mascota, edad_mascota, caracteristicas_mascota, detalles_utiles, vacunas, fecha_vacunacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        \$stmt = \$conn->prepare(\$sql);
        \$stmt->execute([
            \$nombre_mascota,
            \$tipo_mascota,
            \$raza_mascota,
            \$edad_mascota,
            \$caracteristicas_mascota,
            \$detalles_utiles,
            \$vacunas,
            \$fecha_vacunacion
        ]);

        header("Location: misMascotas.php?success=1");
        exit();
    } catch (PDOException \$e) {
        die("Error al registrar la mascota: " . \$e->getMessage());
    }
}
    

     ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <title>Find My Pet | Registrar Mascota</title>
    </head>
    <body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Find My Pet</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="perfil.php">Perfil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="misMascotas.php"> Mis Mascotas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="denunciarPerdida.php">Denunciar Perdida</a>
        </li>
         <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar Sesion</a>
        </li>
    </ul>
      <span class="navbar-text">
        $email ($tipo_usuario - $pais)
      </span>
    </div>
  </div>
</nav>

        <div class="container mt-5">
            <h2 class="text-center">Registrar una Mascota</h2>
            <form action="misMascotas.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nombre de la Mascota</label>
                    <input class="form-control" type="text" name="nombre_mascota" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipo Mascota</label>
                    <select name="tipo_mascota" required>
                        <option value="Perro">Perro</option>
                        <option value="Gato">Gato</option>
                        <option value="Ave">Ave</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Raza</label>
                    <input class="form-control" type="text" name="raza_mascota" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Edad</label>
                    <input type="text" class="form-control" name="edad_mascota" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Características</label>
                    <textarea class="form-control" name="caracteristicas_mascota" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Detalles Utiles</label>
                    <textarea class="form-control" name="detalles_utiles" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vacunas</label>
                    <textarea class="form-control" name="vacunas" required></textarea>
                </div>
                <div class="form-label">Fecha Vacunacion</div>
                <textarea class="form-control" name="fecha_vacunacion" cols="30" required></textarea>
                <button type="submit" class="btn btn-success">Registrar Mascota</button>
            </form>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </html>     
     
HTML;

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
                <li class="nav-item"><a href="login.php" class="nav-link">Iniciar Sesión</a></li>
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
            <label class="form-label">Dirección (Opcional)</label>
            <input type="text" name="direccion" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Ciudad</label>
          <input type="text" name="ciudad" class="form-control" required>
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
                <option value="Transito">Transito</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
    </form>
    <br>
    <form action="register-veterinaria.php" method="post">
        <button type="submit" class="btn btn-danger w-100">Registrar Veterinaria</button>
    </form>
</div>

<!-- Footer -->
<footer class="text-center text-lg-start bg-body-tertiary text-muted">
  <!-- Section: Social media -->
  <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
    <!-- Left -->
    <div class="me-5 d-none d-lg-block">
      <span>Get connected with us on social networks:</span>
    </div>
    <!-- Left -->

    <!-- Right -->
    <div>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-twitter"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-google"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-linkedin"></i>
      </a>
      <a href="" class="me-4 text-reset">
        <i class="fab fa-github"></i>
      </a>
    </div>
    <!-- Right -->
  </section>
  <!-- Section: Social media -->

  <!-- Section: Links  -->
  <section class="">
    <div class="container text-center text-md-start mt-5">
      <!-- Grid row -->
      <div class="row mt-3">
        <!-- Grid column -->
        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
          <!-- Content -->
          <h6 class="text-uppercase fw-bold mb-4">
            <i class="fas fa-gem me-3"></i>
          </h6>
          <p>
            
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">
            
          </h6>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">
           
          </h6>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
          <p>
            <a href="#!" class="text-reset"></a>
          </p>
        </div>
        <!-- Grid column -->

        <!-- Grid column -->
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
          <!-- Links -->
          <h6 class="text-uppercase fw-bold mb-4">Contacto</h6>
          <p><i class="fas fa-home me-3"></i> Buenos Aires, Argentina</p>
          <p>
            <i class="fas fa-envelope me-3"></i>
            contacto.findmypet@gmail.com
          </p>
          <p><i class="fas fa-phone me-3"></i></p>
          <p><i class="fas fa-print me-3"></i></p>
        </div>
        <!-- Grid column -->
      </div>
      <!-- Grid row -->
    </div>
  </section>
  <!-- Section: Links  -->

  <!-- Copyright -->
  <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
    © 2025 Copyright:
    <a class="text-reset fw-bold" href="https://pablonicolasgarcia.infinityfreeapp.com" target="_blank">Pablo Nicolas Garcia</a>
  </div>
  <!-- Copyright -->
</footer>
<!-- Footer -->
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
