<?php
// book.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAuth();

// Usar base de datos en lugar de archivos
$versiculosPorCapitulo = getVersiculosPorCapitulo($pdo);

// Obtener lecturas del usuario desde la base de datos
$lecturasDB = obtenerLecturasUsuario($pdo, $_SESSION['usuario_id']);

// Convertir lecturas de DB al formato esperado por las funciones existentes
$lecturas = [];
foreach ($lecturasDB as $lecturaDB) {
    $lecturas[] = [
        'id' => $lecturaDB['id'],
        'date' => $lecturaDB['fecha'],
        'book' => $lecturaDB['libro_nombre'],
        'from' => $lecturaDB['libro_nombre'] . ' ' . $lecturaDB['capitulo_desde'] . ':' . $lecturaDB['versiculo_desde'],
        'to' => $lecturaDB['libro_nombre'] . ' ' . $lecturaDB['capitulo_hasta'] . ':' . $lecturaDB['versiculo_hasta'],
        'notes' => $lecturaDB['notas'],
        'favorites' => json_decode($lecturaDB['favoritos'], true) ?? [],
    ];
}

if (!isset($_GET['book'])) {
    die('No se especificó un libro.');
}

$book = $_GET['book'];

if (!isset($versiculosPorCapitulo[$book])) {
    die('Libro no válido.' . print_r($versiculosPorCapitulo, true));
}

// Crear un array para marcar los versículos leídos
$versiculosLeidos = [];

foreach ($lecturas as $lectura) {
    if ($lectura['book'] !== $book) continue;

    // Extraer capítulos y versículos desde y hasta
    preg_match('/(\d+):(\d+)/', $lectura['from'], $fromParts);
    preg_match('/(\d+):(\d+)/', $lectura['to'], $toParts);

    $startCap = (int)$fromParts[1];
    $startVerse = (int)$fromParts[2];
    $endCap = (int)$toParts[1];
    $endVerse = (int)$toParts[2];

    if ($startCap === $endCap) {
        for ($v = $startVerse; $v <= $endVerse; $v++) {
            $versiculosLeidos[$startCap][$v] = true;
        }
    } else {
        // De capítulo inicial, desde versículo inicial hasta fin del capítulo
        for ($v = $startVerse; $v <= $versiculosPorCapitulo[$book][$startCap]; $v++) {
            $versiculosLeidos[$startCap][$v] = true;
        }
        // Capítulos intermedios completos
        for ($c = $startCap + 1; $c < $endCap; $c++) {
            for ($v = 1; $v <= $versiculosPorCapitulo[$book][$c]; $v++) {
                $versiculosLeidos[$c][$v] = true;
            }
        }
        // Último capítulo desde 1 hasta versículo final
        for ($v = 1; $v <= $endVerse; $v++) {
            $versiculosLeidos[$endCap][$v] = true;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book'], $_POST['chapter'], $_POST['verse'], $_POST['action'])) {
        $bookPost = $_POST['book'];
        $chapter = (int)$_POST['chapter'];
        $verse = (int)$_POST['verse'];
        $action = $_POST['action'];

        if ($bookPost === $book &&
            isset($versiculosPorCapitulo[$book][$chapter]) &&
            $verse >= 1 && $verse <= $versiculosPorCapitulo[$book][$chapter]
        ) {
            if ($action === 'marcar') {
                guardarLecturaDB($pdo, $_SESSION['usuario_id'], date('Y-m-d'), $book, $chapter, $verse, $chapter, $verse, '', []);
            } elseif ($action === 'desmarcar') {
                foreach ($lecturas as $lectura) {
                    if ($lectura['book'] === $book) {
                        preg_match('/(\d+):(\d+)/', $lectura['from'], $fromParts);
                        preg_match('/(\d+):(\d+)/', $lectura['to'], $toParts);
                        $startCap = (int)$fromParts[1];
                        $startVerse = (int)$fromParts[2];
                        $endCap = (int)$toParts[1];
                        $endVerse = (int)$toParts[2];

                        $isVerseInRange = false;
                        if ($startCap === $endCap) {
                            if ($chapter === $startCap && $verse >= $startVerse && $verse <= $endVerse) {
                                $isVerseInRange = true;
                            }
                        } else {
                            if ($chapter === $startCap && $verse >= $startVerse) $isVerseInRange = true;
                            if ($chapter === $endCap && $verse <= $endVerse) $isVerseInRange = true;
                            if ($chapter > $startCap && $chapter < $endCap) $isVerseInRange = true;
                        }

                        if ($isVerseInRange) {
                            // Si coincide con el versículo a desmarcar, eliminar de la base de datos
                            eliminarLectura($pdo, $lectura['id'], $_SESSION['usuario_id']);
                        }
                    }
                }
            }
        }
    }
    header("Location: book.php?book=" . urlencode($book));
    exit;
}

function renderVersiculos($book, $versiculosPorCapitulo, $versiculosLeidos) {
    foreach ($versiculosPorCapitulo[$book] as $chapter => $versiculosCount) {
        ?>
        <div class="chapter-section">
            <h3>Capítulo <?= $chapter ?></h3>
            <div class="versiculos">
                <?php for ($v = 1; $v <= $versiculosCount; $v++): ?>
                    <form method="POST" class="verse-form" title="Versículo <?= $v ?>">
                        <input type="hidden" name="book" value="<?= htmlspecialchars($book) ?>">
                        <input type="hidden" name="chapter" value="<?= $chapter ?>">
                        <input type="hidden" name="verse" value="<?= $v ?>">
                        <?php if (!empty($versiculosLeidos[$chapter][$v])): ?>
                            <input type="hidden" name="action" value="desmarcar">
                            <button type="submit" class="verse-button read"><?= $v ?></button>
                        <?php else: ?>
                            <input type="hidden" name="action" value="marcar">
                            <button type="submit" class="verse-button not-read"><?= $v ?></button>
                        <?php endif; ?>
                    </form>
                <?php endfor; ?>
            </div>
        </div>
        <?php
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Libro: <?= htmlspecialchars($book) ?></title>
<link rel="stylesheet" href="assets/book.css">
<style>
.chapter-section {
    margin-bottom: 24px;
}
.chapter-section h3 {
    margin-bottom: 8px;
}
.versiculos {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.verse-form {
    margin: 0;
}
.verse-button {
    width: 32px;
    height: 32px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #eee;
    cursor: pointer;
    font-size: 0.8em;
    user-select: none;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background-color 0.3s, border-color 0.3s;
}
.verse-button.read {
    background-color: #a8e6a1;
    border-color: #3c9d0e;
    font-weight: bold;
}
.verse-button.not-read:hover {
    background-color: #ddd;
}
.back-link {
    display: inline-block;
    margin-bottom: 16px;
    font-weight: bold;
    color: #0366d6;
    text-decoration: none;
}
.container {
    max-width: 900px;
    margin: 0 auto;
    padding: 16px;
}
</style>
</head>
<body>
    <div class="container">
        <a href="chapters.php" class="back-link">⬅️ Volver a Progreso por Libros</a>
        <h1>Libro: <?= htmlspecialchars($book) ?></h1>
        <?php renderVersiculos($book, $versiculosPorCapitulo, $versiculosLeidos); ?>
    </div>
</body>
</html>
