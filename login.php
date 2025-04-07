<?php

session_start();

require_once 'config/Database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $error = "Complete todos los campos";
    }else{
        $stmt = $conn->prepare("SELECT id,  email, password, pais, tipo_usuario FROM usuarios where email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["pais"] = $user["pais"];
                $_SESSION["tipo_usuario"] = $user["tipo_usuario"];

                $pais = strtolower($user["pais"]);
                $tipo = strtolower($user['tipo_usuario']);
                $email_dir = preg_replace("/[^a-zA-Z0-9]/" , "_", $email);
                $url = "countries/{$pais}/{$tipo}/{$email_dir}/index.php";
                header("Location: $url");
                exit;

            }else{
                $error = "Contraseña incorrecta";
            }
        }else{
            $error = "Usuario no encontrado";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2 class="text-center">Login</h2>

    <div class="container mt-5">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="login.php">
        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>
    </div>
</body>
</html>