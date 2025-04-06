<?php 

function normalizarEmail($email) {
    return preg_replace("/[^a-zA-Z0-9]/", "_", strtolower($email));
}

function crearCarpetaUsuario($pais, $tipo_usuario, $email) {
    $email_dir = normalizarEmail($email);
    $dir_path  = "countries/$pais/$tipo_usuario/$email_dir";

    if (!file_exists($dir_path)) {
        mkdir($dir_path, 0777, true);
    }
    return $dir_path;
}



function generarIndexPorRol($dir_path, $email, $tipo_usuario, $pais) {
    $tipo_usuario_lower = strtolower($tipo_usuario);

    $indexContent = <<<PHP
<?php 
session_start();
echo "<h1>Bienvenido, $email</h1>";
echo "<p>Rol: $tipo_usuario</p>";
echo "<p>País: $pais</p>";

switch($tipo_usuario_lower) {
    case 'adoptante':
        echo "<p>Ver mascotas disponibles</p>";
        break;
    case 'rescatista':
        echo "<p>Ver alertas recientes</p>";
        break;
    case 'transito':
        echo "<p>Ver mascotas asignadas a tu cuidado</p>";
        break;
    case 'dueño':
        echo "<p>Panel de administración de tu mascota</p>";
        break;
}
?>
PHP;

    file_put_contents("$dir_path/index.php", $indexContent);
}
