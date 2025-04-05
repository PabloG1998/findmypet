<?php 

function generarContenidoIndex($email, $rol, $pais) {
    $formulario = "";

    switch ($rol) {
        case 'adoptante':
            $formulario = "
                <form action='buscar_mascotas.php' method='GET'>
                    <div class='mb-3'>
                        <label for='zona' class='form-label'>Zona</label>
                        <input type='text' name='zona' id='zona' class='form-control' placeholder='Ej: Palermo, CABA'>
                    </div>
                    <button type='submit' class='btn btn-primary'>Buscar Mascotas</button>
                </form>
            ";
            break;

        case 'rescatista':
            $formulario = "
                <form action='publicar_mascota.php' method='POST'>
                    <div class='mb-3'>
                        <label for='nombre' class='form-label'>Nombre del Animal</label>
                        <input type='text' name='nombre' class='form-control' required>
                    </div>
                    <div class='mb-3'>
                        <label for='descripcion' class='form-label'>Descripción</label>
                        <textarea name='descripcion' class='form-control' required></textarea>
                    </div>
                    <div class='mb-3'>
                        <label for='foto' class='form-label'>Foto (URL)</label>
                        <input type='text' name='foto' class='form-control'>
                    </div>
                    <button type='submit' class='btn btn-success'>Publicar Mascota</button>
                </form>
            ";
            break;

        case 'transito':
            $formulario = "
                <form action='ofrecer_transito.php' method='POST'>
                    <div class='mb-3'>
                        <label for='capacidad' class='form-label'>Cantidad de Mascotas que Podés Alojar</label>
                        <input type='number' name='capacidad' class='form-control' min='1' required>
                    </div>
                    <div class='mb-3'>
                        <label for='notas' class='form-label'>Notas Adicionales</label>
                        <textarea name='notas' class='form-control'></textarea>
                    </div>
                    <button type='submit' class='btn btn-warning'>Ofrecer Hogar de Tránsito</button>
                </form>
            ";
            break;

        case 'admin':
            $formulario = "
                <form action='gestionar_usuarios.php' method='GET'>
                    <button type='submit' class='btn btn-dark'>Ir al Panel de Administración</button>
                </form>
            ";
            break;

        default:
            $formulario = "<p>Rol desconocido. No se puede cargar un formulario.</p>";
            break;
    }

    // Contenido completo
    $html = "<?php
session_start();
\$email = \$_SESSION['email'] ?? '$email';
\$pais = \$_SESSION['pais'] ?? '$pais';
\$rol = \$_SESSION['tipo_usuario'] ?? '$rol';
?>
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Inicio - <?= htmlspecialchars(\$rol) ?></title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
        <div class='container-fluid'>
            <a class='navbar-brand' href='#'>FindMyPet</a>
            <div class='collapse navbar-collapse'>
                <ul class='navbar-nav me-auto'>
                    <li class='nav-item'>
                        <a class='nav-link active'>Inicio</a>
                    </li>
                </ul>
                <span class='navbar-text text-white me-3'><?= htmlspecialchars(\$email) ?> | <?= htmlspecialchars(\$pais) ?></span>
                <a href='../../../logout.php' class='btn btn-outline-light'>Cerrar sesión</a>
            </div>
        </div>
    </nav>

    <div class='container mt-5'>
        <div class='card shadow p-4'>
            <h3 class='mb-4'>Bienvenido/a <?= htmlspecialchars(\$email) ?> (<?= htmlspecialchars(\$rol) ?>)</h3>
            $formulario
        </div>
    </div>
</body>
</html>";

    return $html;
}


?>