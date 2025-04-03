    <?php 
        require_once('../../../../db.php');
        require_once('../../../../funciones.php');
        
        session_start();
        $usuario_id = $_SESSION['id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST['mNombre'];
            $especie = $_POST['mMascota'];
            $raza = $_POST['mRaza'];
            $cantidad = intval($_POST['mCantidad']);
            
            // Manejo de la imagen
            $foto_nombre = $_FILES['mFoto']['name'];
            $foto_tmp = $_FILES['mFoto']['tmp_name'];
            $foto_destino = "../../../../uploads/" . basename($foto_nombre);
        
            if (move_uploaded_file($foto_tmp, $foto_destino)) {
                if (addPet($conexion, $usuario_id, $nombre, $especie, $raza, $cantidad, $foto_destino)) {
                    echo "<script>alert('Mascota agregada con éxito');</script>";
                } else {
                    echo "<script>alert('Error al agregar la mascota');</script>";
                }
            } else {
                echo "<script>alert('Error al subir la imagen');</script>";
            }
        }
        ?>   
   
    
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
        
        <form action="perfil.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Nombre de la Mascota:</label>
                <input class="form-control" type="text" name="mNombre" placeholder="Ingrese su nombre" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de Mascota:</label>
                <input class="form-control" type="text" name="mMascota" placeholder="PERRO, GATO">
              </div>

            <div class="mb-3">
            <label class="form-label">Raza de la mascota</label>
            <input class="form-control" type="text" name="mRaza" placeholder="Ingrese la raza" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cantidad</label>
            <input class="form-control" type="number" name="mCantidad" placeholder="Ingrese la cantidad de mascotas">
            
        </div>
            <div class="mb-3">
                <label class="form-label">Foto de la mascota</label>
                <input class="form-control" type="file" name="mFoto" id="fMascota" required>
            </div>
            <button class="btn btn-success" type="submit">Agregar Mascota</button>
        </form>
    </div>

</body>
</html>