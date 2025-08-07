<?php

// Detectar si es local o producción
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0) {
    // Configuración local
    $host = 'localhost';
    $dbname = 'diario_biblico';
    $username = 'diario_user';
    $password = 'diario_password';
} else {
    // Configuración InfinityFree
    $host = 'sql106.infinityfree.com';
    $dbname = 'if0_39655750_diario';
    $username = 'if0_39655750';
    $password = 'CMYVnWgmZXA8FYQ';
}

try {
    // Forzar conexión TCP/IP para servidores compartidos
    $dsn = "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

function getLibros($pdo) {
    $stmt = $pdo->query("SELECT id, nombre, testamento, capitulos, versiculos, abreviatura FROM libros ORDER BY id");
    return $stmt->fetchAll();
}

function getLibroByName($pdo, $nombre) {
    $stmt = $pdo->prepare("SELECT id, nombre, testamento, capitulos, versiculos, abreviatura FROM libros WHERE nombre = ?");
    $stmt->execute([$nombre]);
    return $stmt->fetch();
}

function getCapitulosByLibro($pdo, $libro_id) {
    $stmt = $pdo->prepare("SELECT numero, versiculos FROM capitulos WHERE libro_id = ? ORDER BY numero");
    $stmt->execute([$libro_id]);
    return $stmt->fetchAll();
}

function getTestamentos($pdo) {
    $stmt = $pdo->query("SELECT nombre, testamento FROM libros ORDER BY id");
    $libros = $stmt->fetchAll();
    
    $testamentos = ['antiguo' => [], 'nuevo' => []];
    foreach ($libros as $libro) {
        if ($libro['testamento'] === 'Antiguo') {
            $testamentos['antiguo'][] = $libro['nombre'];
        } else {
            $testamentos['nuevo'][] = $libro['nombre'];
        }
    }
    
    return $testamentos;
}

function getVersiculosPorCapitulo($pdo) {
    $stmt = $pdo->query("
        SELECT l.nombre as libro, c.numero as capitulo, c.versiculos 
        FROM libros l 
        JOIN capitulos c ON l.id = c.libro_id 
        ORDER BY l.id, c.numero
    ");
    
    $versiculos = [];
    while ($row = $stmt->fetch()) {
        $versiculos[$row['libro']][$row['capitulo']] = $row['versiculos'];
    }
    
    return $versiculos;
}

function getCapitulosPorLibro($pdo) {
    $stmt = $pdo->query("SELECT nombre, capitulos FROM libros ORDER BY id");
    $capitulos = [];
    while ($row = $stmt->fetch()) {
        $capitulos[$row['nombre']] = $row['capitulos'];
    }
    return $capitulos;
}

function crearUsuario($pdo, $nombre, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$nombre, $email, $hashedPassword]);
}

function verificarUsuario($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = ? AND activo = 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario && password_verify($password, $usuario['password'])) {
        return $usuario;
    }
    return false;
}

function guardarLecturaDB($pdo, $usuario_id, $fecha, $libro_nombre, $capitulo_desde, $versiculo_desde, $capitulo_hasta, $versiculo_hasta, $notas = '', $favoritos = []) {
    // Obtener el ID del libro
    $libro = getLibroByName($pdo, $libro_nombre);
    if (!$libro) {
        return false;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO lecturas (usuario_id, fecha, libro_id, capitulo_desde, versiculo_desde, capitulo_hasta, versiculo_hasta, notas, favoritos) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([
        $usuario_id,
        $fecha,
        $libro['id'],
        $capitulo_desde,
        $versiculo_desde,
        $capitulo_hasta,
        $versiculo_hasta,
        $notas,
        json_encode($favoritos)
    ]);
}

function obtenerLecturasUsuario($pdo, $usuario_id) {
    $stmt = $pdo->prepare("
        SELECT l.*, lib.nombre as libro_nombre 
        FROM lecturas l 
        JOIN libros lib ON l.libro_id = lib.id 
        WHERE l.usuario_id = ? 
        ORDER BY l.fecha DESC, l.fecha_creacion DESC
    ");
    $stmt->execute([$usuario_id]);
    return $stmt->fetchAll();
}

function actualizarLectura($pdo, $lectura_id, $usuario_id, $fecha, $libro_nombre, $capitulo_desde, $versiculo_desde, $capitulo_hasta, $versiculo_hasta, $notas = '', $favoritos = []) {
    $libro = getLibroByName($pdo, $libro_nombre);
    if (!$libro) {
        return false;
    }
    
    $stmt = $pdo->prepare("
        UPDATE lecturas 
        SET fecha = ?, libro_id = ?, capitulo_desde = ?, versiculo_desde = ?, capitulo_hasta = ?, versiculo_hasta = ?, notas = ?, favoritos = ?
        WHERE id = ? AND usuario_id = ?
    ");
    
    return $stmt->execute([
        $fecha,
        $libro['id'],
        $capitulo_desde,
        $versiculo_desde,
        $capitulo_hasta,
        $versiculo_hasta,
        $notas,
        json_encode($favoritos),
        $lectura_id,
        $usuario_id
    ]);
}

function eliminarLectura($pdo, $lectura_id, $usuario_id) {
    $stmt = $pdo->prepare("DELETE FROM lecturas WHERE id = ? AND usuario_id = ?");
    return $stmt->execute([$lectura_id, $usuario_id]);
}