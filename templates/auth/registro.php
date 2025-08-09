<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Diario Bíblico</title>
    <link rel="stylesheet" href="/assets/auth.css">
</head>
<body>
    <div class="login-container">
        <h1>📖 Diario de Lectura Bíblica</h1>
        <h2>Crear Cuenta</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-row">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($nombre ?? '') ?>">
            </div>
            
            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            
            <div class="form-row">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Crear Cuenta</button>
        </form>
        
        <p><a href="/login">¿Ya tienes cuenta? Iniciar Sesión</a></p>
    </div>
</body>
</html>