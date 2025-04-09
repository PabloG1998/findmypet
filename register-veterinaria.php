<?php 
require_once('config/Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_veterinaria = isset($_POST['nombre_veterinaria']) ? trim($_POST['nombre_veterinaria']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $pais = isset($_POST['pais']) ? trim($_POST['pais']) : '';
    $provincia = isset($_POST['provincia']) ? trim($_POST['provincia']) : '';
    $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';
    $ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : '';
    $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
    $pagina_web = isset($_POST['pagina_web']) ? trim($_POST['pagina_web']) : '';
    $publicidad = isset($_POST['publicidad']) ? trim($_POST['publicidad']) : '';

    if (empty($telefono) || empty($pais) || empty($ciudad) || empty($direccion)) {
        echo '<div class="p-3 mb-2 bg-danger text-white">Todos los campos con (*) son obligatorios.</div>';
    } else {
        $sql = "INSERT INTO veterinarias (nombre_veterinaria, telefono, pais, provincia, estado, ciudad, direccion, pagina_web, publicidad) 
                VALUES (:nombre_veterinaria, :telefono, :pais, :provincia, :estado, :ciudad, :direccion, :pagina_web, :publicidad)";
        $stmt = $conn->prepare($sql);

        $params = [
            ':nombre_veterinaria' => $nombre_veterinaria,
            ':telefono' => $telefono,
            ':pais' => $pais,
            ':provincia' => $provincia,
            ':estado' => $estado,
            ':ciudad' => $ciudad,
            ':direccion' => $direccion,
            ':pagina_web' => $pagina_web,
            ':publicidad' => $publicidad
        ];

        if ($stmt->execute($params)) {
            echo '<div class="p-3 mb-2 bg-success text-white">Veterinaria registrada exitosamente.</div>';
        } else {
            echo '<div class="p-3 mb-2 bg-danger text-white">Error al registrar la veterinaria.</div>';
        }
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Registrarse como Veterinaria</title>
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
        <h2 class="text-center">Registrar Veterinaria</h2>

    <form class="text-center" action="register-veterinaria.php" method="post" class="mt-4">
    
    <div class="mb-3">
        <label class="form-label">Nombre de la Veterinaria</label>
        <input type="text" class="form-control" name="nombre_veterinaria" required>
        </div>

        <div class="mb-3"> 
            <label class="form-label">Telefono *</label>
            <input class="form-control" type="text" name="telefono" required>
        </div>

        <div class="mb-3">
            <label class="from-label">Pais * </label>
            <select class="form-select" name="pais" required>
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
            <label class="form-label">Provincia (Si su país se rige por este modelo)</label>
            <input type="text" name= "provincia" class="form-control">
        </div>
        
        <div class="mb-3">
            <label class="form-label">Estado (Si su país se rige por este modelo)</label>
            <input type="text" name="estado" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Ciudad * </label>
            <input type="text" name="ciudad" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Dirección *</label>
            <input type="text" name="direccion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pagina Web (Si la tiene)</label>
            <input type="text" name="pagina_web" class="form-control">
        </div>
        
        <div class="mb-3">
            <label class="form-label">¿Desea publicitarse en esta pagina?</label>
            <select name="publicidad" class="form-select">
                <option value="1">Si</option>
                <option value="0">No</option>
            </select>
        </div>

         <button type="submit" class="btn btn-success w-100" >Registrarme</button>   
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>