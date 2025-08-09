<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAuth();

// Usar base de datos en lugar de archivos
$testamentos = getTestamentos($pdo);
$antiguoTestamento = $testamentos['antiguo'];
$nuevoTestamento = $testamentos['nuevo'];
$capitulosPorLibro = getCapitulosPorLibro($pdo);
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

$capitulosLeidos = [];

foreach ($lecturas as $lectura) {
    $book = $lectura['book'];
    if (!isset($capitulosLeidos[$book])) {
        $capitulosLeidos[$book] = [];
    }
    
    // Extraer capítulos de forma más robusta
    preg_match('/(\d+):(\d+)/', $lectura['from'], $fromParts);
    preg_match('/(\d+):(\d+)/', $lectura['to'], $toParts);
    
    if (!empty($fromParts) && !empty($toParts)) {
        $start = (int)$fromParts[1];
        $end = (int)$toParts[1];

        for ($c = $start; $c <= $end; $c++) {
            $capitulosLeidos[$book][$c] = true;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_all'], $_POST['book'])) {
        $book = $_POST['book'];
        if (array_key_exists($book, $capitulosPorLibro)) {
            $totalChapters = $capitulosPorLibro[$book];
            
            // Versículos del primer y último capítulo
            $firstChapter = 1;
            $firstVerse = 1;
            $lastChapter = $totalChapters;
            $lastVerse = $versiculosPorCapitulo[$book][$lastChapter] ?? 1;

            // Guardar UNA sola lectura con todo el rango
            guardarLecturaDB(
                $pdo,
                $_SESSION['usuario_id'],
                date('Y-m-d'),
                $book,
                $firstChapter,
                $firstVerse,
                $lastChapter,
                $lastVerse,
                '',
                []
            );
        }
        header('Location: chapters.php');
        exit;
    }

    if (isset($_POST['book'], $_POST['chapter'], $_POST['action'])) {
        $book = $_POST['book'];
        $chapter = (int)$_POST['chapter'];
        $action = $_POST['action'];

        if (array_key_exists($book, $capitulosPorLibro) && $chapter >= 1 && $chapter <= $capitulosPorLibro[$book]) {
            if ($action === 'marcar') {
                // Obtener el número real de versículos del capítulo
                $maxVersiculo = $versiculosPorCapitulo[$book][$chapter] ?? 1;
                guardarLecturaDB($pdo, $_SESSION['usuario_id'], date('Y-m-d'), $book, $chapter, 1, $chapter, $maxVersiculo, '', []);
            } elseif ($action === 'desmarcar') {
                foreach ($lecturas as $lectura) {
                    if ($lectura['book'] === $book) {
                        preg_match('/(\d+):(\d+)/', $lectura['from'], $fromParts);
                        preg_match('/(\d+):(\d+)/', $lectura['to'], $toParts);
                        
                        if (!empty($fromParts) && !empty($toParts)) {
                            $start = (int)$fromParts[1];
                            $end = (int)$toParts[1];
                            if ($start <= $chapter && $chapter <= $end) {
                                // Si el capítulo está en el rango, eliminar la lectura
                                eliminarLectura($pdo, $lectura['id'], $_SESSION['usuario_id']);
                            }
                        }
                    }
                }
            }
        }
    }
    header('Location: chapters.php');
    exit;
}

function renderBookChapters($books, $capitulosPorLibro, $capitulosLeidos) {
    foreach ($books as $book) {
        if (!isset($capitulosPorLibro[$book])) continue;
        $totalChapters = $capitulosPorLibro[$book];

        // Contar capítulos leídos
        $readCount = !empty($capitulosLeidos[$book]) ? count($capitulosLeidos[$book]) : 0;
        $isCompleted = ($readCount === $totalChapters);
        ?>
        <div class="book-section">
            <div class="book-title">
                <a href="book.php?book=<?= urlencode($book) ?>"><?= htmlspecialchars($book) ?></a>
                
                <?php if ($isCompleted): ?>
                    <button type="button" class="toggle-chapters" onclick="toggleChapters(this)">
                        ✅
                    </button>
                <?php else: ?>
                    <form method="POST" style="display:inline;" 
                          onsubmit="return confirmMarkBook('<?= htmlspecialchars($book) ?>')">
                        <input type="hidden" name="book" value="<?= htmlspecialchars($book) ?>">
                        <input type="hidden" name="mark_all" value="1">
                        <button type="submit" class="mark-all-button">✅</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="chapters" style="<?= $isCompleted ? 'display:none;' : '' ?>">
                <?php 
                for ($i = 1; $i <= $totalChapters; $i++): ?>
                    <form method="POST" class="chapter-form" title="<?= $book ?> capítulo <?= $i ?>">
                        <input type="hidden" name="book" value="<?= htmlspecialchars($book) ?>">
                        <input type="hidden" name="chapter" value="<?= $i ?>">
                        <?php if (!empty($capitulosLeidos[$book][$i])): ?>
                            <input type="hidden" name="action" value="desmarcar">
                            <button type="submit" class="chapter-button read"><?= $i ?></button>
                        <?php else: ?>
                            <input type="hidden" name="action" value="marcar">
                            <button type="submit" class="chapter-button not-read"><?= $i ?></button>
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
<link rel="stylesheet" href="assets/chapters.css">
<title>Progreso por Libros y Capítulos</title>

</head>
<body>
 <div class="container">
    <a href="index.php" class="back-link">⬅️ Volver al Diario</a>
    <h1>Progreso por Libros y Capítulos</h1>

    <details open>
        <summary>📜 Antiguo Testamento</summary>
        <?php renderBookChapters($antiguoTestamento, $capitulosPorLibro, $capitulosLeidos); ?>
    </details>

    <details open>
        <summary>📖 Nuevo Testamento</summary>
        <?php renderBookChapters($nuevoTestamento, $capitulosPorLibro, $capitulosLeidos); ?>
    </details>
 </div>
<script>
    function toggleChapters(button) {
        const chaptersDiv = button.closest('.book-section').querySelector('.chapters');
        if (chaptersDiv.style.display === 'none') {
            chaptersDiv.style.display = 'flex';
        } else {
            chaptersDiv.style.display = 'none';
        }
    }

    function confirmMarkBook(bookName) {
        return confirm(`Estás a punto de marcar todo el libro "${bookName}" como leído.\n¿Quieres continuar?`);
    }
</script>
</body>
</html>
