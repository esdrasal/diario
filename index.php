<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAuth();

// Usar base de datos en lugar de archivos
$versiculosPorCapitulo = getVersiculosPorCapitulo($pdo);
$capitulosPorLibro = getCapitulosPorLibro($pdo);

// Obtener lecturas del usuario desde la base de datos
$lecturasDB = obtenerLecturasUsuario($pdo, $_SESSION['usuario_id']);

// Convertir lecturas de DB al formato esperado por las funciones existentes
$lecturas = [];
foreach ($lecturasDB as $lecturaDB) {
    $favoritos_decoded = json_decode($lecturaDB['favoritos'], true) ?? [];
    $favoritos_formatted = [];
    foreach ($favoritos_decoded as $fav) {
        if (is_string($fav)) {
            $favoritos_formatted[] = $fav;
        }
    }
    
    $lecturas[] = [
        'id' => $lecturaDB['id'],
        'date' => $lecturaDB['fecha'],
        'book' => $lecturaDB['libro_nombre'],
        'from' => $lecturaDB['libro_nombre'] . ' ' . $lecturaDB['capitulo_desde'] . ':' . $lecturaDB['versiculo_desde'],
        'to' => $lecturaDB['libro_nombre'] . ' ' . $lecturaDB['capitulo_hasta'] . ':' . $lecturaDB['versiculo_hasta'],
        'notes' => $lecturaDB['notas'],
        'favorites' => $favoritos_formatted,
    ];
}

$versiculosLeidos = contarVersiculosLeidos($lecturas, $pdo);
$totalVersiculos = 0;
foreach ($versiculosPorCapitulo as $libro => $capitulos) {
    $totalVersiculos += array_sum($capitulos);
}
$totalLeidos = array_sum($versiculosLeidos);
$progresoTotal = $totalVersiculos > 0 ? round(($totalLeidos / $totalVersiculos) * 100, 2) : 0;

$errors = [];
$lecturaEditar = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book = $_POST['book'] ?? '';
    $fromCap = $_POST['from_chapter'] ?? '';
    $fromVer = $_POST['from_verse'] ?? '';
    $toCap = $_POST['to_chapter'] ?? '';
    $toVer = $_POST['to_verse'] ?? '';

    // Validaciones básicas
    if (!$book) {
        $errors[] = "Debe seleccionar un libro.";
    }
    if (!$fromCap || !ctype_digit($fromCap)) {
        $errors[] = "Debe seleccionar capítulo inicial válido.";
    }
    if (!$fromVer || !ctype_digit($fromVer)) {
        $errors[] = "Debe seleccionar versículo inicial válido.";
    }
    if (!$toCap || !ctype_digit($toCap)) {
        $errors[] = "Debe seleccionar capítulo final válido.";
    }
    if (!$toVer || !ctype_digit($toVer)) {
        $errors[] = "Debe seleccionar versículo final válido.";
    }

    $fromCap = (int)$fromCap;
    $fromVer = (int)$fromVer;
    $toCap = (int)$toCap;
    $toVer = (int)$toVer;

    if (empty($errors)) {
        // Validar libro existe
        if (!isset($versiculosPorCapitulo[$book])) {
            $errors[] = "Libro no válido.";
        } else {
            // Validar capítulos y versículos existen
            if (!isset($versiculosPorCapitulo[$book][$fromCap])) {
                $errors[] = "Capítulo inicial ($fromCap) no válido para el libro $book.";
            } else {
                if ($fromVer < 1 || $fromVer > $versiculosPorCapitulo[$book][$fromCap]) {
                    $errors[] = "Versículo inicial ($fromVer) fuera de rango para capítulo $fromCap (máximo {$versiculosPorCapitulo[$book][$fromCap]}).";
                }
            }
            if (!isset($versiculosPorCapitulo[$book][$toCap])) {
                $errors[] = "Capítulo final ($toCap) no válido para el libro $book.";
            } else {
                if ($toVer < 1 || $toVer > $versiculosPorCapitulo[$book][$toCap]) {
                    $errors[] = "Versículo final ($toVer) fuera de rango para capítulo $toCap (máximo {$versiculosPorCapitulo[$book][$toCap]}).";
                }
            }
            // Validar orden correcto
            if ($fromCap > $toCap || ($fromCap === $toCap && $fromVer > $toVer)) {
                $errors[] = "'Desde' debe ser menor o igual que 'Hasta'.";
            }
        }
    }

    if (empty($errors)) {
        $favoritesInput = array_filter(array_map('trim', explode(',', $_POST['favorites'] ?? '')));
        $favorites = [];

        foreach ($favoritesInput as $fav) {
            if (preg_match('/^\d+:\d+$/', $fav)) {
                $favorites[] = $book . ' ' . $fav;
            }
        }

        if (!empty($_POST['edit_id'])) {
            $editId = (int)$_POST['edit_id'];
            actualizarLectura($pdo, $editId, $_SESSION['usuario_id'], 
                $_POST['date'] ?? date('Y-m-d'), 
                $book, 
                $fromCap, 
                $fromVer, 
                $toCap, 
                $toVer, 
                $_POST['notes'] ?? '', 
                $favorites
            );
        } else {
            guardarLecturaDB($pdo, $_SESSION['usuario_id'], 
                $_POST['date'] ?? date('Y-m-d'), 
                $book, 
                $fromCap, 
                $fromVer, 
                $toCap, 
                $toVer, 
                $_POST['notes'] ?? '', 
                $favorites
            );
        }

        header('Location: index.php');
        exit;
    }
}

if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    
    if ($action === 'delete') {
        eliminarLectura($pdo, $id, $_SESSION['usuario_id']);
        header('Location: index.php');
        exit;
    } elseif ($action === 'edit') {
        foreach ($lecturas as $lectura) {
            if ($lectura['id'] === $id) {
                $lecturaEditar = $lectura;
                break;
            }
        }
        if (!isset($lecturaEditar)) {
            header('Location: index.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Diario de Lectura Bíblica</title>
    <link rel="stylesheet" href="assets/style.css" />
    <style>
      .form-pasaje select {
        margin-right: 10px;
        min-width: 100px;
      }
      .separator {
        margin: 0 5px;
        font-weight: bold;
      }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>📖 Diario de Lectura Bíblica</h1>
        <div>
            <span>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>!</span> | 
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <form method="POST" novalidate>
        <input type="hidden" name="edit_id" value="<?= isset($lecturaEditar) ? $lecturaEditar['id'] : '' ?>">

        <div class="form-row">
            <label for="date">📅 Fecha:</label>
            <input
                type="date"
                id="date"
                name="date"
                required
                value="<?= isset($lecturaEditar) ? htmlspecialchars($lecturaEditar['date']) : date('Y-m-d') ?>"
            />
        </div>

        <div class="form-row">
            <label for="book">Pasaje:</label>
            <div class="form-pasaje">
                <select name="book" id="book" required>
                    <option value="">-- Selecciona un libro --</option>
                    <?php foreach ($versiculosPorCapitulo as $libro => $capitulos): ?>
                        <option value="<?= htmlspecialchars($libro) ?>" <?= (isset($lecturaEditar) && $lecturaEditar['book'] === $libro) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($libro) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="from_chapter" id="from_chapter" required disabled>
                    <option value="">Capítulo desde</option>
                </select>

                <select name="from_verse" id="from_verse" required disabled>
                    <option value="">Versículo desde</option>
                </select>

                <span class="separator">-</span>

                <select name="to_chapter" id="to_chapter" required disabled>
                    <option value="">Capítulo hasta</option>
                </select>

                <select name="to_verse" id="to_verse" required disabled>
                    <option value="">Versículo hasta</option>
                </select>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-messages" style="color: red; margin-bottom: 1em;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-row" style="flex-direction: column; align-items: flex-start;">
            <label for="notes">📝 Notas:</label>
            <textarea name="notes" id="notes" rows="5"><?= isset($lecturaEditar) ? htmlspecialchars($lecturaEditar['notes']) : '' ?></textarea>
        </div>

        <div class="form-row">
            <label for="favorites">⭐ Favoritos (capítulo:versículo):</label>
            <input
                type="text"
                name="favorites"
                id="favorites"
                placeholder="1:3, 1:7, 2:1"
                value="<?= isset($lecturaEditar) ? htmlspecialchars(implode(', ', array_map(function($f) use ($lecturaEditar) {
                    return trim(str_replace($lecturaEditar['book'] . ' ', '', $f));
                }, $lecturaEditar['favorites'] ?? []))) : '' ?>"
                style="flex: 1;"
            />
        </div>

        <div class="centered" style="margin-top: 1em;">
            <button type="submit"><?= isset($lecturaEditar) ? 'Actualizar Lectura' : 'Guardar Lectura' ?></button>
        </div>
    </form>

    <hr />

    <!-- Progreso -->
    <div class="collapsible-section">
        <h2 onclick="toggleSection('progreso')">📈 Progreso: <?= $progresoTotal ?>%</h2>
        <div id="progreso" class="collapsible-content" style="display: none;">
            <p><strong>Total leído:</strong> <?= $totalLeidos ?> / <?= $totalVersiculos ?> versículos</p>
            <h3>📘 <a href="chapters.php" style="text-decoration:none; color:inherit;">Progreso por libros</a></h3>
            <ul>
                <?php foreach ($versiculosLeidos as $libro => $cantidad): 
                    $porcentaje = round(($cantidad / array_sum($versiculosPorCapitulo[$libro])) * 100, 1);
                ?>
                    <li>
                        <a href="book.php?book=<?= urlencode($libro) ?>">
                            <?= htmlspecialchars($libro) ?>:
                        </a>
                        <?= $cantidad ?> / <?= array_sum($versiculosPorCapitulo[$libro]) ?> (<?= $porcentaje ?>%)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Lecturas -->
    <div class="collapsible-section">
        <h2 onclick="toggleSection('lecturas')">
            📚 Lecturas Registradas (<?= count($lecturas) ?>)
        </h2>
        <div id="lecturas" class="collapsible-content" style="display: none;">
            <ul>
                <?php foreach ($lecturas as $lectura): ?>
                    <li>
                        <strong><?= htmlspecialchars($lectura['date']) ?></strong><br />
                        📘 <strong><?= htmlspecialchars($lectura['book']) ?></strong>:
                        <?= htmlspecialchars($lectura['from']) ?> - <?= htmlspecialchars($lectura['to']) ?><br />
                        <?= nl2br(htmlspecialchars($lectura['notes'])) ?><br />
                        <?php if (!empty($lectura['favorites'])): ?>
                            ⭐ <em>Favoritos:</em> <?= implode(', ', $lectura['favorites']) ?>
                        <?php endif; ?>
                        <br />
                        <a href="index.php?action=edit&id=<?= $lectura['id'] ?>">✏️ Editar</a> |
                        <a href="index.php?action=delete&id=<?= $lectura['id'] ?>" onclick="return confirm('¿Eliminar esta lectura?')">🗑️ Eliminar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
    function toggleSection(id) {
        const content = document.getElementById(id);
        if (content.style.display === 'none') {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    }

    const versiculosPorCapitulo = <?= json_encode($versiculosPorCapitulo, JSON_UNESCAPED_UNICODE) ?>;

    const bookSelect = document.getElementById('book');
    const fromChapterSelect = document.getElementById('from_chapter');
    const fromVerseSelect = document.getElementById('from_verse');
    const toChapterSelect = document.getElementById('to_chapter');
    const toVerseSelect = document.getElementById('to_verse');

    // Llenar capítulos en un select
    function fillChapters(select, capsObj) {
        select.innerHTML = '<option value="">Capítulo</option>';
        // capsObj es objeto {cap: versiculos}
        for (const cap in capsObj) {
            const option = document.createElement('option');
            option.value = cap;
            option.textContent = cap;
            select.appendChild(option);
        }
        select.disabled = false;
    }

    // Llenar versículos en un select
    function fillVerses(select, cantidad) {
        select.innerHTML = '<option value="">Versículo</option>';
        for (let i = 1; i <= cantidad; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            select.appendChild(option);
        }
        select.disabled = false;
    }

    // Cuando se selecciona libro
    bookSelect.addEventListener('change', () => {
        const libro = bookSelect.value;
        if (!versiculosPorCapitulo[libro]) {
            fromChapterSelect.innerHTML = '<option value="">Capítulo desde</option>';
            toChapterSelect.innerHTML = '<option value="">Capítulo hasta</option>';
            fromChapterSelect.disabled = true;
            toChapterSelect.disabled = true;

            fromVerseSelect.innerHTML = '<option value="">Versículo desde</option>';
            toVerseSelect.innerHTML = '<option value="">Versículo hasta</option>';
            fromVerseSelect.disabled = true;
            toVerseSelect.disabled = true;
            return;
        }

        const caps = versiculosPorCapitulo[libro];

        fillChapters(fromChapterSelect, caps);

        // Para "hasta" mostrar inicialmente todos los capítulos también (igual que desde)
        fillChapters(toChapterSelect, caps);

        fromVerseSelect.innerHTML = '<option value="">Versículo desde</option>';
        toVerseSelect.innerHTML = '<option value="">Versículo hasta</option>';
        fromVerseSelect.disabled = true;
        toVerseSelect.disabled = true;
    });

    fromChapterSelect.addEventListener('change', () => {
        const libro = bookSelect.value;
        const fromCap = parseInt(fromChapterSelect.value);
        if (fromCap && versiculosPorCapitulo[libro]?.[fromCap]) {
            fillVerses(fromVerseSelect, versiculosPorCapitulo[libro][fromCap]);
        } else {
            fromVerseSelect.innerHTML = '<option value="">Versículo desde</option>';
            fromVerseSelect.disabled = true;
        }

        // Actualizar capítulos "hasta" para que no sea menor que "desde"
        if (!libro) return;
        const caps = versiculosPorCapitulo[libro];
        toChapterSelect.innerHTML = '<option value="">Capítulo hasta</option>';

        // Mostrar solo capítulos desde fromCap en adelante
        for (const cap in caps) {
            if (parseInt(cap) >= fromCap) {
                const option = document.createElement('option');
                option.value = cap;
                option.textContent = cap;
                toChapterSelect.appendChild(option);
            }
        }
        toChapterSelect.disabled = false;

        // Si capítulo "hasta" es menor que "desde", actualizarlo al mínimo (fromCap)
        const currentToCap = parseInt(toChapterSelect.value);
        if (!currentToCap || currentToCap < fromCap) {
            toChapterSelect.value = fromCap.toString();
            // Actualizar versículos "hasta" al capítulo seleccionado
            fillVerses(toVerseSelect, caps[fromCap]);
            // Opcional: resetear versículo "hasta"
            toVerseSelect.value = '';
        }

        // Si capítulo "desde" y "hasta" son iguales, limitar versículos "hasta" para que sea >= versículo desde
        // Lo hacemos también en el evento de fromVerseSelect más abajo
    });

    toChapterSelect.addEventListener('change', () => {
        const libro = bookSelect.value;
        const toCap = parseInt(toChapterSelect.value);
        if (toCap && versiculosPorCapitulo[libro]?.[toCap]) {
            fillVerses(toVerseSelect, versiculosPorCapitulo[libro][toCap]);
        } else {
            toVerseSelect.innerHTML = '<option value="">Versículo hasta</option>';
            toVerseSelect.disabled = true;
        }
    });

    // Evento para limitar versículo "hasta" cuando capítulo desde y hasta son iguales
    fromVerseSelect.addEventListener('change', () => {
        const libro = bookSelect.value;
        const fromCap = parseInt(fromChapterSelect.value);
        const toCap = parseInt(toChapterSelect.value);
        const fromVer = parseInt(fromVerseSelect.value);

        if (fromCap && toCap && fromVer && fromCap === toCap) {
            // Limitar versículos "hasta" para que no sea menor que fromVer
            const maxVerse = versiculosPorCapitulo[libro][toCap];
            toVerseSelect.innerHTML = '<option value="">Versículo hasta</option>';
            for (let i = fromVer; i <= maxVerse; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                toVerseSelect.appendChild(option);
            }
            toVerseSelect.disabled = false;

            // Si versículo hasta actual es menor que fromVer, resetearlo
            const currentToVer = parseInt(toVerseSelect.value);
            if (!currentToVer || currentToVer < fromVer) {
                toVerseSelect.value = '';
            }
        } else if (toCap && versiculosPorCapitulo[libro]?.[toCap]) {
            // Si capítulos diferentes, simplemente llenar todos los versículos "hasta"
            fillVerses(toVerseSelect, versiculosPorCapitulo[libro][toCap]);
        }
    });

    // Función para seleccionar option por valor
    function selectOptionByValue(select, value) {
        if (!value) return;
        const option = Array.from(select.options).find(o => o.value === value.toString());
        if (option) select.value = value.toString();
    }

    // Precargar datos al editar
    function precargarLectura() {
        const libro = '<?= isset($lecturaEditar) ? addslashes($lecturaEditar['book']) : '' ?>';
        const from = '<?= isset($lecturaEditar) ? addslashes(str_replace($lecturaEditar['book'] . ' ', '', $lecturaEditar['from'])) : '' ?>'; // ej: "1:3"
        const to = '<?= isset($lecturaEditar) ? addslashes(str_replace($lecturaEditar['book'] . ' ', '', $lecturaEditar['to'])) : '' ?>';     // ej: "1:10"

        if (!libro) return;

        bookSelect.value = libro;

        const caps = versiculosPorCapitulo[libro];
        fillChapters(fromChapterSelect, caps);
        fillChapters(toChapterSelect, caps);

        setTimeout(() => {
            if (from) {
                const [fromCap, fromVer] = from.split(':');
                selectOptionByValue(fromChapterSelect, fromCap);
                fillVerses(fromVerseSelect, versiculosPorCapitulo[libro][fromCap]);
                selectOptionByValue(fromVerseSelect, fromVer);
            }
            if (to) {
                const [toCap, toVer] = to.split(':');
                selectOptionByValue(toChapterSelect, toCap);
                fillVerses(toVerseSelect, versiculosPorCapitulo[libro][toCap]);
                selectOptionByValue(toVerseSelect, toVer);
            }
        }, 100);
    }

    precargarLectura();
    </script>
</body>

</html>
