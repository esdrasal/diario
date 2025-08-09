<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Progreso por Libros - Diario Bíblico</title>
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="/assets/chapters.css">
</head>
<body>
    <div class="chapters-header">
        <h1>📊 Progreso por Libros</h1>
        <div class="chapters-nav">
            <a href="/">🏠 Inicio</a>
            <a href="/logout">Cerrar Sesión</a>
        </div>
    </div>

    <div class="user-info">
        <span>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</span>
    </div>

    <!-- Progreso Total -->
    <div class="progress-total">
        <h2>📈 Progreso Total: <?= $progresoTotal['porcentaje'] ?>%</h2>
        <div class="progress-bar">
            <div class="progress-gradient" style="width: <?= $progresoTotal['porcentaje'] ?>%;"></div>
        </div>
        <p><?= $progresoTotal['total_leidos'] ?> de <?= $progresoTotal['total_versiculos'] ?> versículos leídos</p>
        <p><strong><?= $progresoTotal['total_versiculos'] - $progresoTotal['total_leidos'] ?></strong> versículos restantes</p>
    </div>

    <!-- Antiguo Testamento -->
    <div class="testament-section">
        <h2 onclick="toggleTestament('antiguo')" class="testament-header antiguo">
            📜 Antiguo Testamento (<?= count(array_filter($librosPorTestamento['antiguo'], function($l) { return $l['completado']; })) ?>/<?= count($librosPorTestamento['antiguo']) ?> completos)
        </h2>
        <div id="antiguo" class="testament-content">
            <div class="books-grid">
                <?php foreach ($librosPorTestamento['antiguo'] as $libro): ?>
                    <div class="book-card <?= $libro['completado'] ? 'completed' : ($libro['porcentaje'] > 0 ? 'started' : 'unread') ?>" 
                         style="border: 2px solid #ddd; border-radius: 8px; padding: 15px; background-color: white;">
                        
                        <div class="book-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h3 style="margin: 0;">
                                <a href="/book?book=<?= urlencode($libro['info']['nombre']) ?>" style="text-decoration: none; color: inherit;">
                                    <?= htmlspecialchars($libro['info']['nombre']) ?>
                                </a>
                                <?= $libro['completado'] ? ' ✅' : '' ?>
                            </h3>
                            <span class="percentage" style="font-weight: bold; color: <?= $libro['completado'] ? '#28a745' : ($libro['porcentaje'] > 0 ? '#ffc107' : '#6c757d') ?>;">
                                <?= $libro['porcentaje'] ?>%
                            </span>
                        </div>
                        
                        <div class="progress-bar" style="width: 100%; height: 8px; background-color: #e9ecef; border-radius: 4px; margin-bottom: 10px;">
                            <div style="width: <?= $libro['porcentaje'] ?>%; height: 100%; background-color: <?= $libro['completado'] ? '#28a745' : ($libro['porcentaje'] > 0 ? '#ffc107' : '#6c757d') ?>; border-radius: 4px;"></div>
                        </div>
                        
                        <div class="book-stats" style="display: flex; justify-content: space-between; font-size: 14px; color: #666;">
                            <span><?= $libro['info']['capitulos'] ?> capítulos</span>
                            <span><?= $libro['versiculos_leidos'] ?>/<?= $libro['info']['versiculos'] ?> versículos</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Nuevo Testamento -->
    <div class="testament-section">
        <h2 onclick="toggleTestament('nuevo')" style="cursor: pointer; padding: 15px; background-color: #4169E1; color: white; margin: 0; border-radius: 8px;">
            ✝️ Nuevo Testamento (<?= count(array_filter($librosPorTestamento['nuevo'], function($l) { return $l['completado']; })) ?>/<?= count($librosPorTestamento['nuevo']) ?> completos)
        </h2>
        <div id="nuevo" class="testament-content" style="display: block;">
            <div class="books-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; padding: 20px;">
                <?php foreach ($librosPorTestamento['nuevo'] as $libro): ?>
                    <div class="book-card <?= $libro['completado'] ? 'completed' : ($libro['porcentaje'] > 0 ? 'started' : 'unread') ?>" 
                         style="border: 2px solid #ddd; border-radius: 8px; padding: 15px; background-color: white;">
                        
                        <div class="book-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h3 style="margin: 0;">
                                <a href="/book?book=<?= urlencode($libro['info']['nombre']) ?>" style="text-decoration: none; color: inherit;">
                                    <?= htmlspecialchars($libro['info']['nombre']) ?>
                                </a>
                                <?= $libro['completado'] ? ' ✅' : '' ?>
                            </h3>
                            <span class="percentage" style="font-weight: bold; color: <?= $libro['completado'] ? '#28a745' : ($libro['porcentaje'] > 0 ? '#ffc107' : '#6c757d') ?>;">
                                <?= $libro['porcentaje'] ?>%
                            </span>
                        </div>
                        
                        <div class="progress-bar" style="width: 100%; height: 8px; background-color: #e9ecef; border-radius: 4px; margin-bottom: 10px;">
                            <div style="width: <?= $libro['porcentaje'] ?>%; height: 100%; background-color: <?= $libro['completado'] ? '#28a745' : ($libro['porcentaje'] > 0 ? '#ffc107' : '#6c757d') ?>; border-radius: 4px;"></div>
                        </div>
                        
                        <div class="book-stats" style="display: flex; justify-content: space-between; font-size: 14px; color: #666;">
                            <span><?= $libro['info']['capitulos'] ?> capítulos</span>
                            <span><?= $libro['versiculos_leidos'] ?>/<?= $libro['info']['versiculos'] ?> versículos</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <style>
        .book-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .book-card.completed {
            border-color: #28a745 !important;
            background: linear-gradient(145deg, #ffffff, #f8fff8);
        }
        
        .book-card.started {
            border-color: #ffc107 !important;
            background: linear-gradient(145deg, #ffffff, #fffef8);
        }
        
        .book-card.unread {
            border-color: #dee2e6 !important;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
        }
        
        .book-card h3 a:hover {
            color: #007bff;
        }
    </style>

    <script>
    function toggleTestament(testament) {
        const content = document.getElementById(testament);
        if (content.style.display === 'none') {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    }
    </script>
</body>
</html>