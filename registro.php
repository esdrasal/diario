<?php
session_start();

require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Por favor complete todos los campos';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        try {
            if (crearUsuario($pdo, $nombre, $email, $password)) {
                $success = 'Usuario registrado exitosamente. <a href="login.php">Iniciar sesión</a>';
            } else {
                $error = 'Error al crear el usuario';
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $error = 'El email ya está registrado';
            } else {
                $error = 'Error en el servidor';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Diario Bíblico</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .register-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }
        .success {
            color: green;
            margin-bottom: 1rem;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>📖 Registro</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">Registrarse</button>
        </form>
        
        <div style="text-align: center; margin-top: 1rem;">
            <a href="login.php">¿Ya tienes cuenta? Inicia sesión aquí</a>
        </div>
    </div>
</body>
</html>