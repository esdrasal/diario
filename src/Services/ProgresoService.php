<?php

namespace App\Services;

use App\Models\Libro;

class ProgresoService
{
    private $libroModel;

    public function __construct()
    {
        $this->libroModel = new Libro();
    }

    public function contarVersiculosLeidos(array $lecturas): array
    {
        $versiculosPorCapitulo = $this->libroModel->getVersiculosPorCapitulo();
        $versosUnicos = [];
        $contadorPorLibro = [];

        foreach ($lecturas as $lectura) {
            $book = $lectura['libro_nombre'];
            $chapterFrom = $lectura['capitulo_desde'];
            $verseFrom = $lectura['versiculo_desde'];
            $chapterTo = $lectura['capitulo_hasta'];
            $verseTo = $lectura['versiculo_hasta'];

            $expanded = $this->expandVerses($book, $chapterFrom, $verseFrom, $chapterTo, $verseTo, $versiculosPorCapitulo);

            foreach ($expanded as $verseKey) {
                if (!isset($versosUnicos[$verseKey])) {
                    $versosUnicos[$verseKey] = true;

                    if (!isset($contadorPorLibro[$book])) {
                        $contadorPorLibro[$book] = 0;
                    }
                    $contadorPorLibro[$book]++;
                }
            }
        }

        return $contadorPorLibro;
    }

    private function expandVerses(string $book, int $chapterFrom, int $verseFrom, int $chapterTo, int $verseTo, array $versiculosPorCapitulo): array
    {
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

    public function calcularProgresoTotal(array $versiculosLeidos): array
    {
        $versiculosPorCapitulo = $this->libroModel->getVersiculosPorCapitulo();
        
        $totalVersiculos = 0;
        foreach ($versiculosPorCapitulo as $libro => $capitulos) {
            $totalVersiculos += array_sum($capitulos);
        }
        
        $totalLeidos = array_sum($versiculosLeidos);
        $porcentaje = $totalVersiculos > 0 ? round(($totalLeidos / $totalVersiculos) * 100, 2) : 0;
        
        return [
            'total_versiculos' => $totalVersiculos,
            'total_leidos' => $totalLeidos,
            'porcentaje' => $porcentaje
        ];
    }
}