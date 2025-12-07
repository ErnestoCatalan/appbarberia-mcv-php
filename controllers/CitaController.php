<?php 
namespace Controllers;

use Model\Cita;
use MVC\Router;

class CitaController {
    public static function index(Router $router) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    isAuth();

    $barberia_id = $_GET['barberia_id'] ?? null;
    
    // DEPURACIÓN: Verificar datos del usuario
    error_log("=== DEPURACIÓN CitaController ===");
    error_log("Usuario ID: " . $_SESSION['id']);
    error_log("Usuario Nombre: " . $_SESSION['nombre']);
    error_log("Barbería ID desde URL: " . ($barberia_id ?? 'NULL'));
    
    // Si no hay barbería seleccionada, mostrar citas existentes del usuario
    if (!$barberia_id) {
        $citas = self::obtenerCitasUsuario($_SESSION['id']);
        
        // DEPURACIÓN: Verificar datos obtenidos
        error_log("Total de citas encontradas: " . count($citas));
        
        $router->render('cita/mis-citas', [
            'nombre' => $_SESSION['nombre'],
            'id' => $_SESSION['id'],
            'citas' => $citas
        ]);
        return;
    }

    // Si hay barbería seleccionada, mostrar formulario para crear nueva cita
    $router->render('cita/index', [
        'nombre' => $_SESSION['nombre'],
        'id' => $_SESSION['id']
    ]);
}
    
    private static function obtenerCitasUsuario($usuario_id) {
        if (!is_numeric($usuario_id)) {
            error_log("ERROR: usuario_id no es numérico: " . $usuario_id);
            return [];
        }
        
        // CONSULTA CORREGIDA: Usar subconsultas para evitar problemas de GROUP BY
        $consulta = "SELECT 
                        c.id, 
                        c.fecha, 
                        c.hora, 
                        c.usuarioId,
                        c.barberia_id,
                        
                        -- Información de la barbería
                        b.nombre as barberia_nombre,
                        b.direccion as barberia_direccion,
                        b.telefono as barberia_telefono,
                        b.email as barberia_email,
                        
                        -- Servicios como string separado por comas
                        (SELECT GROUP_CONCAT(DISTINCT s2.nombre ORDER BY s2.nombre SEPARATOR ', ')
                        FROM citasServicios cs2
                        LEFT JOIN servicios s2 ON cs2.servicioId = s2.id
                        WHERE cs2.citaId = c.id
                        GROUP BY cs2.citaId) as servicios,
                        
                        -- Total calculado
                        (SELECT COALESCE(SUM(s3.precio), 0)
                        FROM citasServicios cs3
                        LEFT JOIN servicios s3 ON cs3.servicioId = s3.id
                        WHERE cs3.citaId = c.id) as total,
                        
                        -- Cantidad de servicios
                        (SELECT COUNT(DISTINCT cs4.servicioId)
                        FROM citasServicios cs4
                        WHERE cs4.citaId = c.id) as cantidad_servicios
                        
                    FROM citas c
                    
                    -- Barbería (LEFT JOIN para incluir citas incluso si la barbería fue eliminada)
                    LEFT JOIN barberias b ON c.barberia_id = b.id
                    
                    WHERE c.usuarioId = {$usuario_id}
                    
                    ORDER BY c.fecha DESC, c.hora DESC";
        
        error_log("Consulta SQL mejorada: " . $consulta);
        
        try {
            // Usar el nuevo modelo CitaCompleta
            $citas = \Model\CitaCompleta::SQL($consulta);
            
            error_log("Citas encontradas con nueva consulta: " . count($citas));
            
            // Depurar resultados
            foreach($citas as $cita) {
                error_log("Cita procesada - ID: {$cita->id}");
                error_log("  Fecha: {$cita->fecha}");
                error_log("  Hora: {$cita->hora}");
                error_log("  Barbería: {$cita->barberia_nombre}");
                error_log("  Servicios: " . ($cita->servicios ?? 'NULL'));
                error_log("  Total: {$cita->total}");
                error_log("  Cantidad: {$cita->cantidad_servicios}");
            }
            
            return $citas;
        } catch (\Exception $e) {
            error_log("ERROR en consulta mejorada: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            // Fallback: método simple
            return self::obtenerCitasUsuarioSimple($usuario_id);
        }
    }

    // Método de fallback simple
    private static function obtenerCitasUsuarioSimple($usuario_id) {
        // Consulta básica sin JOINs complejos
        $consulta = "SELECT c.* FROM citas c WHERE c.usuarioId = {$usuario_id} ORDER BY c.fecha DESC, c.hora DESC";
        
        $citasBasicas = \Model\Cita::SQL($consulta);
        $citasCompletas = [];
        
        foreach($citasBasicas as $cita) {
            // Crear objeto stdClass para evitar warnings
            $citaCompleta = new \stdClass();
            $citaCompleta->id = $cita->id;
            $citaCompleta->fecha = $cita->fecha;
            $citaCompleta->hora = $cita->hora;
            $citaCompleta->barberia_id = $cita->barberia_id;
            
            // Obtener información de la barbería
            if ($cita->barberia_id) {
                $barberia = \Model\Barberia::find($cita->barberia_id);
                if ($barberia) {
                    $citaCompleta->barberia_nombre = $barberia->nombre;
                    $citaCompleta->barberia_direccion = $barberia->direccion;
                    $citaCompleta->barberia_telefono = $barberia->telefono;
                    $citaCompleta->barberia_email = $barberia->email;
                }
            }
            
            // Obtener servicios
            $serviciosConsulta = "SELECT s.nombre, s.precio 
                                FROM citasServicios cs 
                                LEFT JOIN servicios s ON cs.servicioId = s.id 
                                WHERE cs.citaId = {$cita->id}";
            
            $serviciosData = \Model\Cita::SQL($serviciosConsulta);
            
            $serviciosNombres = [];
            $total = 0;
            foreach($serviciosData as $servicio) {
                if (!empty($servicio->nombre)) {
                    $serviciosNombres[] = $servicio->nombre;
                    $total += $servicio->precio ?? 0;
                }
            }
            
            $citaCompleta->servicios = !empty($serviciosNombres) ? implode(', ', $serviciosNombres) : null;
            $citaCompleta->total = $total;
            $citaCompleta->cantidad_servicios = count($serviciosNombres);
            
            $citasCompletas[] = $citaCompleta;
        }
        
        return $citasCompletas;
    }
}