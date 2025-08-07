<?php

function loadLecturas() {
    if (!file_exists(LECTURAS_FILE)) {
        return [];
    }
    $json = file_get_contents(LECTURAS_FILE);
    return json_decode($json, true) ?: [];
}

function guardarLectura(array $lectura) {
    $lecturas = loadLecturas();
    $lecturas[] = $lectura;
    guardarTodasLasLecturas($lecturas);
}

function contarVersiculosLeidos(array $lecturas, $pdo): array {
    $versiculosPorCapitulo = getVersiculosPorCapitulo($pdo);

    $versosUnicos = [];
    $contadorPorLibro = [];

    foreach ($lecturas as $lectura) {
        $book = $lectura['book'];
        $fromParts = explode(':', str_replace($book . ' ', '', $lectura['from']));
        $toParts = explode(':', str_replace($book . ' ', '', $lectura['to']));

        if (count($fromParts) !== 2 || count($toParts) !== 2) {
            continue;
        }

        $chapterFrom = (int)$fromParts[0];
        $verseFrom = (int)$fromParts[1];
        $chapterTo = (int)$toParts[0];
        $verseTo = (int)$toParts[1];

        $expanded = expandVerses($book, $chapterFrom, $verseFrom, $chapterTo, $verseTo, $versiculosPorCapitulo);

        foreach ($expanded as $verseKey) {
            if (!isset($versosUnicos[$verseKey])) {
                $versosUnicos[$verseKey] = true;

                // Sumar por libro
                if (!isset($contadorPorLibro[$book])) {
                    $contadorPorLibro[$book] = 0;
                }
                $contadorPorLibro[$book]++;
            }
        }
    }

    return $contadorPorLibro;
}



// Convierte "Génesis 1:1" en un número lineal por versículo
function versiculoANumero($pasaje) {
    // Extrae solo la parte 1:1
    if (preg_match('/(\d+):(\d+)/', $pasaje, $matches)) {
        $cap = (int)$matches[1];
        $ver = (int)$matches[2];
        return ($cap * 1000) + $ver; // clave simple para orden
    }
    return 0;
}

function guardarTodasLasLecturas(array $lecturas) {
    file_put_contents(LECTURAS_FILE, json_encode(array_values($lecturas), JSON_PRETTY_PRINT));
}

function expandVerses($book, $chapterFrom, $verseFrom, $chapterTo, $verseTo, $versiculosPorCapitulo) {
    $verses = [];

    for ($c = $chapterFrom; $c <= $chapterTo; $c++) {
        $vStart = ($c == $chapterFrom) ? $verseFrom : 1;
        $vEnd = ($c == $chapterTo) ? $verseTo : $versiculosPorCapitulo[$book][$c] ?? 0;

        for ($v = $vStart; $v <= $vEnd; $v++) {
            $verses[] = "$book $c:$v";
        }
    }

    return $verses;
}
