<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Find My Pet</title>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-ligth">
        <div class="container">
            <a href="#" class="navbar-brand">Find My Pet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="login.php" class="nav-link">Iniciar Sesion</a></li>
                    <li class="nav-item"><a href="register.php" class="nav-link">Registrarse</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Iniciar Sesion</h2>
        <form action="config/login-process.php" class="mt-4" method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email"  class="form-control" required>
            </div>
            <div class="mb-3">
                <label  class="form-label">Contraseña</label>
                <input type="password" name="password"  class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label for="remember" class="form-check-label">Recordarme</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>