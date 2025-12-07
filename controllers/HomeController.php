<?php 
namespace Controllers;

use Model\Barberia;
use MVC\Router;

class HomeController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Obtener barberías aprobadas
        $barberias = Barberia::obtenerAprobadas();

        // Determinar si el usuario está logueado
        $usuarioLogueado = isset($_SESSION['login']);
        $tipoUsuario = $_SESSION['tipo'] ?? null;

        $router->render('home/index', [
            'nombre' => $_SESSION['nombre'] ?? null,
            'barberias' => $barberias,
            'usuarioLogueado' => $usuarioLogueado,
            'tipoUsuario' => $tipoUsuario
        ], true); // Último parámetro true para usar home-layout
    }
}