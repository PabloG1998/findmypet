<?php 
session_start();
require('Database.php');
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        //Guarda datos en sesion
        $_SESSION['email'] = $user['email'];
        $_SESSION['pais'] = $user['pais'];
        $_SESSION['tipo_usuario'] = $user['tipo_usuario'];

        //Cookie
        if (isset($_POST['remember'])) {
            setcookie('email', $user['email'], time() + (86400 * 30), "/"); //Un mes(30 días)
        }

        //Redireccion
        $pais = $user['pais'];
        $tipo_usuario = $user['tipo_usuario'];
        $email_dir = str_replace(['@', '.'], '_', $user['email']);
        $path = "../countries/$pais/$tipo_usuario/$email_dir/";
        header("Location: $path");
        exit;
    }else{
        "<script>alert('Credenciales inválidas');</script>";
    }

}
?>