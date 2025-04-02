<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .perfil-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="perfil-container">
        <h2>Perfil de Usuario</h2>
        <form action="update_profile.php" method="POST">
            <label>Nombre:</label>
            <input type="text" name="nombre" placeholder="Ingrese su nombre" required>

            <label>Email:</label>
            <input type="email" name="email" placeholder="Ingrese su email" required>

            <label>Contraseña:</label>
            <input type="password" name="password" placeholder="Ingrese su contraseña" required>

            <button type="submit">Actualizar Perfil</button>
        </form>
    </div>

</body>
</html>