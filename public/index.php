<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\HomeController;
use Controllers\AdminController;
use Controllers\AdminBarberiaController;
use Controllers\APIController;
use Controllers\BarberiaController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\ServicioController;
use Controllers\SolicitudController;
use Controllers\SuperAdminController;
use MVC\Router;

$router = new Router();
// Homepage (usa todo el ancho)
$router->get('/', [HomeController::class, 'index']);
$router->get('/home', [HomeController::class, 'index']);
$router->post('/', [LoginController::class, 'login']); 

// Login (usa el layout normal con dos columnas)
$router->get('/login', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Super Admin Routes
$router->get('/superadmin', [SuperAdminController::class, 'index']);
$router->get('/superadmin/barberias', [SuperAdminController::class, 'barberias']);
$router->post('/superadmin/eliminar-barberia', [SuperAdminController::class, 'eliminarBarberia']);
$router->post('/superadmin/cambiar-estado-barberia', [SuperAdminController::class, 'cambiarEstadoBarberia']);
$router->get('/superadmin/solicitudes', [SuperAdminController::class, 'solicitudes']);
$router->post('/superadmin/eliminar-solicitud', [SuperAdminController::class, 'eliminarSolicitud']);

// Actualizar dirección de barbería
$router->post('/admin-barberia/actualizar-direccion', [AdminBarberiaController::class, 'actualizarDireccion']);

// Recuperar Password
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);
$router->get('/recuperar', [LoginController::class, 'recuperar']);
$router->post('/recuperar', [LoginController::class, 'recuperar']);

// Crear Cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);

// Confirmar cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// BARBERÍAS 
$router->get('/barberias', [BarberiaController::class, 'index']);
$router->get('/barberia', [BarberiaController::class, 'mostrar']);

// SOLICITUDES 
$router->get('/solicitud', [SolicitudController::class, 'crear']);
$router->post('/solicitud', [SolicitudController::class, 'crear']);
$router->get('/solicitudes/gestionar', [SolicitudController::class, 'gestionar']);
$router->post('/solicitudes/aprobar', [SolicitudController::class, 'aprobar']);
$router->post('/solicitudes/rechazar', [SolicitudController::class, 'rechazar']);
$router->get('/solicitud/mensaje', [SolicitudController::class, 'mensaje']);

// Area privada
$router->get('/cita', [CitaController::class, 'index']);
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin-barberia', [AdminBarberiaController::class, 'index']); 
$router->post('/admin-barberia', [AdminBarberiaController::class, 'index']);

// API de Citas
$router->get('/api/servicios', [APIController::class, 'index']);
$router->post('/api/citas', [APIController::class, 'guardar']);
$router->post('/api/eliminar', [APIController::class, 'eliminar']);

// CRUD de servicios
$router->get('/servicios', [ServicioController::class, 'index']);
$router->get('/servicios/crear', [ServicioController::class, 'crear']);
$router->post('/servicios/crear', [ServicioController::class, 'crear']);
$router->get('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/eliminar', [ServicioController::class, 'eliminar']);

// Comprueba y valida las rutas
$router->comprobarRutas();