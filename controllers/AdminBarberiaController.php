<?php 
namespace Controllers;

use Model\AdminCita;
use Model\Barberia;
use MVC\Router;

class AdminBarberiaController {
    public static function index(Router $router) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdminBarberia();

        // DEBUG: Verificar sesión
        error_log("DEBUG - AdminBarberiaController - barberia_id: " . ($_SESSION['barberia_id'] ?? 'NULL'));
        error_log("DEBUG - AdminBarberiaController - usuario ID: " . ($_SESSION['id'] ?? 'NULL'));
        error_log("DEBUG - AdminBarberiaController - tipo: " . ($_SESSION['tipo'] ?? 'NULL'));

        // Verificar que barberia_id esté definido
        if (!isset($_SESSION['barberia_id']) || empty($_SESSION['barberia_id'])) {
            error_log("ERROR: barberia_id no definido en sesión");
            
            // Intentar obtener la barbería del usuario
            if (isset($_SESSION['id'])) {
                $usuarioId = $_SESSION['id'];
                
                // Buscar al usuario para obtener su barberia_id
                require_once __DIR__ . '/../models/Usuario.php';
                $usuario = \Model\Usuario::find($usuarioId);
                
                if ($usuario && $usuario->barberia_id) {
                    $_SESSION['barberia_id'] = $usuario->barberia_id;
                    error_log("INFO: barberia_id obtenido de usuario: " . $usuario->barberia_id);
                } else {
                    // Si no tiene barbería, redirigir
                    $_SESSION['error'] = 'No tienes una barbería asignada. Contacta al administrador.';
                    header('Location: /barberias');
                    return;
                }
            } else {
                // Si no hay usuario, redirigir al login
                header('Location: /login');
                return;
            }
        }

        $barberia_id = $_SESSION['barberia_id'];
        
        // Validar que barberia_id sea numérico
        if (!is_numeric($barberia_id)) {
            error_log("ERROR: barberia_id no es numérico: " . $barberia_id);
            $_SESSION['error'] = 'ID de barbería inválido';
            header('Location: /barberias');
            return;
        }

        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $fechas = explode('-', $fecha);

        if( !checkdate( $fechas[1], $fechas[2], $fechas[0] )) {
            header('Location: /404');
            return;
        }

        // Obtener información de la barbería
        error_log("DEBUG: Buscando barbería con ID: " . $barberia_id);
        $barberia = Barberia::find($barberia_id);
        
        if (!$barberia) {
            error_log("ERROR: Barbería no encontrada con ID: " . $barberia_id);
            $_SESSION['error'] = 'Barbería no encontrada';
            unset($_SESSION['barberia_id']); // Limpiar sesión inválida
            header('Location: /barberias');
            return;
        }

        // Consulta para las citas
        $consulta = "SELECT citas.id, citas.hora, CONCAT( usuarios.nombre, ' ', usuarios.apellido) as cliente, ";
        $consulta .= " usuarios.email, usuarios.telefono, servicios.nombre as servicio, servicios.precio  ";
        $consulta .= " FROM citas  ";
        $consulta .= " LEFT OUTER JOIN usuarios ";
        $consulta .= " ON citas.usuarioId=usuarios.id  ";
        $consulta .= " LEFT OUTER JOIN citasServicios ";
        $consulta .= " ON citasServicios.citaId=citas.id ";
        $consulta .= " LEFT OUTER JOIN servicios ";
        $consulta .= " ON servicios.id=citasServicios.servicioId ";
        $consulta .= " WHERE fecha =  '{$fecha}' AND citas.barberia_id = '{$barberia_id}'";

        $citas = AdminCita::SQL($consulta);
        $alertas = [];

        $router->render('admin-barberia/index', [
            'nombre' => $_SESSION['nombre'],
            'citas' => $citas,
            'fecha' => $fecha,
            'barberia' => $barberia,
            'alertas' => $alertas
        ]);
    }

    public static function actualizarDireccion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        isAdminBarberia();

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $barberia_id = $_SESSION['barberia_id'] ?? null;
            
            if (!$barberia_id) {
                $_SESSION['error'] = 'No tienes una barbería asignada';
                header('Location: /admin-barberia');
                return;
            }
            
            $direccion = $_POST['direccion'] ?? '';
            
            $barberia = Barberia::find($barberia_id);
            
            if($barberia) {
                $barberia->direccion = $direccion;
                $resultado = $barberia->actualizar();
                
                if($resultado) {
                    $_SESSION['exito'] = 'Dirección actualizada correctamente';
                } else {
                    $_SESSION['error'] = 'Error al actualizar la dirección';
                }
            } else {
                $_SESSION['error'] = 'Barbería no encontrada';
            }
            header('Location: /admin-barberia');
        }
    }
}