<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verificar si el nombre de usuario ya está registrado
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        // Si el nombre de usuario ya existe, mostrar un mensaje de alerta
        echo "<script>alert('El nombre de usuario ya está en uso. Por favor elige otro.');</script>";
    } else {
        // Insertar nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO users (username, name, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $name, $password])) {
            // Crear una carpeta para el usuario
            $userFolder = "C:/xampp/htdocs/linktree/" . $username;

            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }

            // Redirigir a la página de inicio de sesión
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Registro</title>
</head>
<body>
    <form method="POST">
        <h2>Registro</h2>
        <input type="text" name="username" placeholder="Usuario" required>
        <input type="text" name="name" placeholder="Nombre" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrar</button>
    </form>
    <p>Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>
