<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($bookName) ?> - Diario Bíblico</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="/assets/book.css">
</head>
<body>
    <div class="book-header">
        <h1>📖 <?= htmlspecialchars($bookName) ?></h1>
        <div class="book-nav">
            <a href="/">🏠 Inicio</a>
            <a href="/logout">Cerrar Sesión</a>
        </div>
    </div>

    <div class="user-info">
        <span>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</span>
    </div>

    <!-- Información del libro -->
    <div class="book-info">
        <h2>Información del Libro</h2>
        <p><strong>Testamento:</strong> <?= htmlspecialchars($libro['testamento']) ?></p>
        <p><strong>Capítulos:</strong> <?= $libro['capitulos'] ?></p>
        <p><strong>Versículos totales:</strong> <?= $libro['versiculos'] ?></p>
        <p><strong>Progreso:</strong> <?= $porcentajeLibro ?>% (<?= $versiculosLeidosLibro ?>/<?= $totalVersiculosLibro ?> versículos)</p>
        
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $porcentajeLibro ?>%;"></div>
        </div>
    </div>

    <!-- Lista de capítulos -->
    <div class="chapters-grid">
        <?php foreach ($capitulos as $capitulo): ?>
            <?php 
                $capNum = $capitulo['numero'];
                $versiculosCapitulo = $capitulo['versiculos'];
                $versiculosLeidosCapitulo = isset($capitulosLeidos[$capNum]) ? count(array_unique($capitulosLeidos[$capNum])) : 0;
                $porcentajeCapitulo = $versiculosCapitulo > 0 ? round(($versiculosLeidosCapitulo / $versiculosCapitulo) * 100) : 0;
                $claseCapitulo = $porcentajeCapitulo == 100 ? 'chapter-complete' : ($porcentajeCapitulo > 0 ? 'chapter-partial' : 'chapter-unread');
            ?>
            <div class="chapter-box <?= $claseCapitulo ?>" 
                 onclick="toggleChapterDetails(<?= $capNum ?>)"
                 title="Capítulo <?= $capNum ?>: <?= $porcentajeCapitulo ?>% completado (<?= $versiculosLeidosCapitulo ?>/<?= $versiculosCapitulo ?> versículos)">
                <div><strong><?= $capNum ?></strong></div>
                <div class="chapter-percentage"><?= $porcentajeCapitulo ?>%</div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Lecturas del libro -->
    <?php if (!empty($lecturasLibro)): ?>
        <div class="collapsible-section">
            <h2 onclick="toggleSection('lecturas-libro')">
                📚 Lecturas de <?= htmlspecialchars($bookName) ?> (<?= count($lecturasLibro) ?>)
            </h2>
            <div id="lecturas-libro" class="collapsible-content" style="display: none;">
                <ul>
                    <?php foreach ($lecturasLibro as $lectura): ?>
                        <li class="reading-item">
                            <strong><?= htmlspecialchars($lectura['fecha']) ?></strong><br />
                            📘 <?= htmlspecialchars($lectura['libro_nombre']) ?> 
                            <?= $lectura['capitulo_desde'] ?>:<?= $lectura['versiculo_desde'] ?> - 
                            <?= $lectura['capitulo_hasta'] ?>:<?= $lectura['versiculo_hasta'] ?><br />
                            
                            <?php if (!empty($lectura['notas'])): ?>
                                <div class="reading-notes">
                                    <em>Notas:</em> <?= nl2br(htmlspecialchars($lectura['notas'])) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($lectura['favoritos'])): ?>
                                <div class="reading-favorites">
                                    ⭐ <em>Favoritos:</em> 
                                    <?php
                                        $favoritos_display = [];
                                        $favoritos_decoded = json_decode($lectura['favoritos'], true) ?? [];
                                        foreach ($favoritos_decoded as $fav) {
                                            if (is_string($fav)) {
                                                $favoritos_display[] = str_replace($lectura['libro_nombre'] . ' ', '', $fav);
                                            }
                                        }
                                        echo implode(', ', $favoritos_display);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <script>
    function toggleSection(id) {
        const content = document.getElementById(id);
        if (content.style.display === 'none') {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    }

    function toggleChapterDetails(chapter) {
        // Por ahora solo muestra información, se puede expandir para mostrar detalles del capítulo
        console.log('Capítulo ' + chapter + ' seleccionado');
    }
    </script>
</body>
</html>