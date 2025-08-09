<?php

namespace App\Controllers;

use App\Models\Libro;
use App\Models\Lectura;
use App\Services\ProgresoService;

class ChaptersController extends BaseController
{
    private $libroModel;
    private $lecturaModel;
    private $progresoService;

    public function __construct()
    {
        parent::__construct();
        $this->libroModel = new Libro();
        $this->lecturaModel = new Lectura();
        $this->progresoService = new ProgresoService();
    }

    public function index(): void
    {
        $this->authService->requiereAuth();
        
        $usuario = $this->authService->getUsuarioActual();

        // Obtener todos los libros y testamentos
        $libros = $this->libroModel->getAll();
        $testamentos = $this->libroModel->getTestamentos();
        $versiculosPorCapitulo = $this->libroModel->getVersiculosPorCapitulo();

        // Obtener lecturas del usuario
        $lecturasDB = $this->lecturaModel->obtenerPorUsuario($usuario['id']);
        
        // Calcular progreso por libro
        $versiculosLeidos = $this->progresoService->contarVersiculosLeidos($lecturasDB);
        $progresoTotal = $this->progresoService->calcularProgresoTotal($versiculosLeidos);

        // Organizar libros por testamento con progreso
        $librosPorTestamento = [
            'antiguo' => [],
            'nuevo' => []
        ];

        foreach ($libros as $libro) {
            $nombreLibro = $libro['nombre'];
            $versiculosLeidosLibro = $versiculosLeidos[$nombreLibro] ?? 0;
            $totalVersiculosLibro = $libro['versiculos'];
            $porcentajeLibro = $totalVersiculosLibro > 0 ? round(($versiculosLeidosLibro / $totalVersiculosLibro) * 100, 1) : 0;

            $libroData = [
                'info' => $libro,
                'versiculos_leidos' => $versiculosLeidosLibro,
                'porcentaje' => $porcentajeLibro,
                'completado' => $porcentajeLibro == 100
            ];

            if ($libro['testamento'] === 'Antiguo') {
                $librosPorTestamento['antiguo'][] = $libroData;
            } else {
                $librosPorTestamento['nuevo'][] = $libroData;
            }
        }

        $this->render('chapters/index', [
            'usuario' => $usuario,
            'librosPorTestamento' => $librosPorTestamento,
            'progresoTotal' => $progresoTotal,
            'versiculosLeidos' => $versiculosLeidos
        ]);
    }
}