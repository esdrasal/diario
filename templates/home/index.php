<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Diario de Lectura Bíblica</title>
    <link rel="stylesheet" href="/assets/style.css" />
</head>
<body>
    <div class="app-header">
        <h1>📖 Diario de Lectura Bíblica</h1>
    </div>
    <div class="user-info">
        <span>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</span> | 
        <a href="/logout">Cerrar Sesión</a>
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
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-row form-notes">
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
                class="form-favorites"
            />
        </div>

        <div class="centered">
            <button type="submit"><?= isset($lecturaEditar) ? 'Actualizar Lectura' : 'Guardar' ?></button>
        </div>
    </form>

    <hr />

    <!-- Progreso -->
    <div class="collapsible-section">
        <h2 onclick="toggleSection('progreso')">📈 Progreso: <?= $progreso['porcentaje'] ?>%</h2>
        <div id="progreso" class="collapsible-content" style="display: none;">
            <p><strong>Versículos leídos:</strong> <?= $progreso['total_leidos'] ?>, faltan por leer <?= $progreso['total_versiculos'] - $progreso['total_leidos'] ?> versículos</p>
            <h3>📘 <a href="/chapters" class="inherit-color">Progreso por libros</a></h3>
            <ul>
                <?php foreach ($versiculosPorCapitulo as $libro => $capitulos): 
                    $cantidad = $versiculosLeidos[$libro] ?? 0;
                    if ($cantidad == 0) continue; // Ocultar libros sin lecturas
                    $totalVersiculosLibro = array_sum($capitulos);
                    $porcentaje = $totalVersiculosLibro > 0 ? round(($cantidad / $totalVersiculosLibro) * 100, 1) : 0;
                ?>
                    <li>
                        <?php if ($porcentaje == 100) { ?>
                            <a href="/book?book=<?= urlencode($libro) ?>">
                                <?= htmlspecialchars($libro) ?>
                            </a> ✅
                        <?php } else { ?>
                            <a href="/book?book=<?= urlencode($libro) ?>">
                            <?= htmlspecialchars($libro) ?></a>: <?= $porcentaje ?>%, has leído
                            <?= $cantidad ?> versículos de <?= $totalVersiculosLibro ?> 
                        <?php } ?>  
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
                        <a href="/?action=edit&id=<?= $lectura['id'] ?>">✏️ Editar</a> |
                        <a href="/?action=delete&id=<?= $lectura['id'] ?>" onclick="return confirm('¿Eliminar esta lectura?')">🗑️ Eliminar</a>
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

        // Si capítulo "hasta" es menor que "desde", actualizarlo
        const currentToCap = parseInt(toChapterSelect.value);
        if (!currentToCap || currentToCap < fromCap) {
            toChapterSelect.value = fromCap.toString();
            fillVerses(toVerseSelect, caps[fromCap]);
            toVerseSelect.value = '';
        }
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

            const currentToVer = parseInt(toVerseSelect.value);
            if (!currentToVer || currentToVer < fromVer) {
                toVerseSelect.value = '';
            }
        } else if (toCap && versiculosPorCapitulo[libro]?.[toCap]) {
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
        const from = '<?= isset($lecturaEditar) ? addslashes(str_replace($lecturaEditar['book'] . ' ', '', $lecturaEditar['from'])) : '' ?>';
        const to = '<?= isset($lecturaEditar) ? addslashes(str_replace($lecturaEditar['book'] . ' ', '', $lecturaEditar['to'])) : '' ?>';

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