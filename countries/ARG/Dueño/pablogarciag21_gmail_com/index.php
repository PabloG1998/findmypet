    <?php 
    session_start();
    echo "<h1>Bienvenido, pablogarciag21@gmail.com</h1>";
    echo "<p>Rol: Dueño</p>";
    echo "<p>País: ARG</p>";

    switch('Dueño') {
    case 'adoptante':
        echo "<p>Ver mascotas disponibles</p>";
        break;
    case 'rescatista':
        echo "<p>Ver alertas recientes</p>";
        break;
    case 'transito':
        echo "<p>Ver mascotas asignadas a tu cuidado</p>";
         break;
    }
?>