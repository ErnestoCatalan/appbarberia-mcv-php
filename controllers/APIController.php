<?php 
namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index() {
        // Deshabilitar salida de errores en producción
        ini_set('display_errors', 0);
        
        $barberia_id = $_GET['barberia_id'] ?? null;
        
        if (!$barberia_id) {
            echo json_encode([]);
            return;
        }

        $servicios = Servicio::porBarberia($barberia_id);
        
        // Asegurar que todos los campos estén presentes
        $serviciosArray = array_map(function($servicio) {
            return [
                'id' => $servicio->id,
                'nombre' => $servicio->nombre,
                'precio' => $servicio->precio,
                'descripcion' => $servicio->descripcion ?? '',
                'duracion' => $servicio->duracion ?? 30,
                'imagen' => $servicio->imagen ?? '',
                'barberia_id' => $servicio->barberia_id
            ];
        }, $servicios);
        
        // Establecer header JSON explícitamente
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($serviciosArray);
    }

    public static function guardar() {
        // Deshabilitar salida de errores
        ini_set('display_errors', 0);
        
        // Establecer header JSON desde el principio
        header('Content-Type: application/json; charset=utf-8');
        
        // Verificar si hay salida antes de empezar
        if (ob_get_length() > 0) {
            ob_clean(); // Limpiar cualquier salida previa
        }

        error_log("=== INICIO guardar() ===");
        error_log("Datos POST recibidos: " . print_r($_POST, true));
        
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Usar ID de sesión en lugar del que viene del cliente (por seguridad)
        if (!isset($_SESSION['id'])) {
            error_log("ERROR: Usuario no autenticado en sesión");
            echo json_encode([
                'resultado' => false,
                'error' => "Usuario no autenticado"
            ]);
            return;
        }
        
        $usuarioId = $_SESSION['id'];
        
        // Validar campos requeridos (excepto usuarioId que viene de sesión)
        $camposRequeridos = ['fecha', 'hora', 'servicios', 'barberia_id'];
        foreach($camposRequeridos as $campo) {
            if(!isset($_POST[$campo]) || empty($_POST[$campo])) {
                error_log("ERROR: Campo requerido faltante: {$campo}");
                echo json_encode([
                    'resultado' => false,
                    'error' => "El campo {$campo} es requerido"
                ]);
                return;
            }
        }

        // Validar que barberia_id sea numérico
        if(!is_numeric($_POST['barberia_id'])) {
            error_log("ERROR: barberia_id no es numérico: " . $_POST['barberia_id']);
            echo json_encode([
                'resultado' => false,
                'error' => "El ID de la barbería no es válido"
            ]);
            return;
        }

        // Preparar datos para la cita
        $datosCita = [
            'fecha' => $_POST['fecha'],
            'hora' => $_POST['hora'],
            'usuarioId' => $usuarioId, // Usar ID de sesión
            'barberia_id' => $_POST['barberia_id']
        ];
        
        error_log("Datos para crear cita: " . print_r($datosCita, true));
        
        try {
            // Almacena la cita y devuelve el ID
            $cita = new Cita($datosCita);
            $resultado = $cita->guardar();
            
            error_log("Resultado de guardar cita: " . print_r($resultado, true));

            // Verificar si hubo error al guardar
            if(!$resultado['resultado']) {
                error_log("ERROR al guardar cita en BD");
                echo json_encode([
                    'resultado' => false,
                    'error' => "Error al guardar la cita en la base de datos"
                ]);
                return;
            }

            $id = $resultado['id'];
            error_log("Cita creada con ID: " . $id);

            // Almacena los servicios con el ID de la cita
            $idServicios = explode(",", $_POST['servicios']);
            
            $serviciosGuardados = [];
            foreach($idServicios as $idServicio) {
                if(!is_numeric($idServicio)) {
                    error_log("ERROR: ID de servicio no numérico: " . $idServicio);
                    continue;
                }
                
                $args = [
                    'citaId' => $id,
                    'servicioId' => $idServicio
                ];
                
                error_log("Guardando servicio con args: " . print_r($args, true));
                
                $citaServicio = new CitaServicio($args);
                $resultadoServicio = $citaServicio->guardar();
                
                if($resultadoServicio) {
                    $serviciosGuardados[] = $idServicio;
                    error_log("Servicio {$idServicio} guardado exitosamente");
                } else {
                    error_log("ERROR al guardar servicio {$idServicio}");
                }
            }

            error_log("=== FIN guardar() - Éxito ===");
            
            echo json_encode([
                'resultado' => true,
                'id' => $id,
                'servicios_guardados' => $serviciosGuardados
            ]);
            
        } catch (\Exception $e) {
            error_log("EXCEPCIÓN en guardar(): " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            
            echo json_encode([
                'resultado' => false,
                'error' => "Error del sistema: " . $e->getMessage()
            ]);
        }
    }

    public static function eliminar() {
        // Deshabilitar salida de errores
        ini_set('display_errors', 0);
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            try {
                $cita = Cita::find($id);
                if ($cita) {
                    $cita->eliminar();
                    error_log("Cita {$id} eliminada exitosamente");
                }
            } catch (\Exception $e) {
                error_log("ERROR al eliminar cita {$id}: " . $e->getMessage());
            }
            
            // Redireccionar a la página anterior
            if (isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                header('Location: /cita');
            }
        }
    }
}