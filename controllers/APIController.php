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

        // VALIDACIÓN 1: Verificar que no haya una cita en el mismo horario
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $barberia_id = $_POST['barberia_id'];
        
        error_log("Validando cita para fecha: {$fecha}, hora: {$hora}, barbería: {$barberia_id}");
        
        // Consultar si ya existe una cita en ese horario
        $consultaCitas = "SELECT COUNT(*) as total FROM citas 
                        WHERE fecha = '" . self::$db->escape_string($fecha) . "' 
                        AND hora = '" . self::$db->escape_string($hora) . "' 
                        AND barberia_id = '" . self::$db->escape_string($barberia_id) . "'";
        
        $resultadoCitas = self::$db->query($consultaCitas);
        if($resultadoCitas) {
            $fila = $resultadoCitas->fetch_assoc();
            $citasExistentes = (int)$fila['total'];
            
            if($citasExistentes > 0) {
                error_log("ERROR: Ya existe una cita en ese horario para la barbería {$barberia_id}");
                echo json_encode([
                    'resultado' => false,
                    'error' => "Ya existe una cita agendada para este horario. Por favor, selecciona otro horario."
                ]);
                return;
            }
        }
        
        // VALIDACIÓN 2: Validar que la hora esté dentro del horario de la barbería
        $consultaHorario = "SELECT horario_apertura, horario_cierre FROM barberias WHERE id = '" . self::$db->escape_string($barberia_id) . "'";
        $resultadoHorario = self::$db->query($consultaHorario);
        
        if($resultadoHorario && $filaHorario = $resultadoHorario->fetch_assoc()) {
            $horaCita = strtotime($hora);
            $apertura = strtotime($filaHorario['horario_apertura']);
            $cierre = strtotime($filaHorario['horario_cierre']);
            
            if($horaCita && $apertura && $cierre) {
                // Convertir a formato comparable (solo hora, sin fecha)
                $horaCitaTime = date('H:i:s', $horaCita);
                $aperturaTime = date('H:i:s', $apertura);
                $cierreTime = date('H:i:s', $cierre);
                
                error_log("Horario validación - Cita: {$horaCitaTime}, Apertura: {$aperturaTime}, Cierre: {$cierreTime}");
                
                if($horaCitaTime < $aperturaTime || $horaCitaTime >= $cierreTime) {
                    error_log("ERROR: Hora fuera del horario de atención");
                    echo json_encode([
                        'resultado' => false,
                        'error' => "La hora seleccionada está fuera del horario de atención de la barbería. Horario: " . 
                                date('g:i A', $apertura) . " - " . date('g:i A', $cierre)
                    ]);
                    return;
                }
                
                // Validar que no sea un tiempo pasado (solo si la fecha es hoy)
                $fechaCita = strtotime($fecha);
                $hoy = strtotime(date('Y-m-d'));
                
                if($fechaCita == $hoy && $horaCitaTime < date('H:i:s')) {
                    error_log("ERROR: Hora en el pasado");
                    echo json_encode([
                        'resultado' => false,
                        'error' => "No puedes agendar citas en horas pasadas. Por favor, selecciona una hora futura."
                    ]);
                    return;
                }
            }
        } else {
            error_log("ERROR: No se encontró información de horario para la barbería {$barberia_id}");
            echo json_encode([
                'resultado' => false,
                'error' => "No se pudo obtener información del horario de la barbería."
            ]);
            return;
        }

        // Preparar datos para la cita
        $datosCita = [
            'fecha' => $fecha,
            'hora' => $hora,
            'usuarioId' => $usuarioId, // Usar ID de sesión
            'barberia_id' => $barberia_id
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

            $citaId = $resultado['id'];
            error_log("Cita creada con ID: " . $citaId);

            // Almacena los servicios con el ID de la cita
            $idServicios = explode(",", $_POST['servicios']);
            
            $serviciosGuardados = [];
            $totalServicios = 0;
            $serviciosNombres = [];
            
            foreach($idServicios as $idServicio) {
                if(!is_numeric($idServicio)) {
                    error_log("ERROR: ID de servicio no numérico: " . $idServicio);
                    continue;
                }
                
                $args = [
                    'citaId' => $citaId,
                    'servicioId' => $idServicio
                ];
                
                error_log("Guardando servicio con args: " . print_r($args, true));
                
                $citaServicio = new CitaServicio($args);
                $resultadoServicio = $citaServicio->guardar();
                
                if($resultadoServicio) {
                    $serviciosGuardados[] = $idServicio;
                    error_log("Servicio {$idServicio} guardado exitosamente");
                    
                    // Obtener información del servicio para el email
                    $servicioInfo = Servicio::find($idServicio);
                    if($servicioInfo) {
                        $serviciosNombres[] = $servicioInfo->nombre;
                        $totalServicios += $servicioInfo->precio;
                    }
                } else {
                    error_log("ERROR al guardar servicio {$idServicio}");
                }
            }

            // ENVIAR EMAIL DE NOTIFICACIÓN AL BARBERO
            error_log("=== Enviando notificación al barbero ===");
            try {
                // 1. Obtener información del barbero
                $consultaBarberia = "SELECT b.*, u.email as barbero_email, u.nombre as barbero_nombre 
                                    FROM barberias b
                                    LEFT JOIN usuarios u ON b.usuario_id = u.id
                                    WHERE b.id = '" . self::$db->escape_string($barberia_id) . "'";
                
                $resultadoBarberia = self::$db->query($consultaBarberia);
                if($resultadoBarberia && $barberia = $resultadoBarberia->fetch_assoc()) {
                    if(!empty($barberia['barbero_email'])) {
                        // 2. Obtener información del cliente
                        $consultaCliente = "SELECT nombre, apellido, telefono FROM usuarios WHERE id = '" . self::$db->escape_string($usuarioId) . "'";
                        $resultadoCliente = self::$db->query($consultaCliente);
                        $cliente = $resultadoCliente->fetch_assoc();
                        
                        $nombreCliente = $cliente ? ($cliente['nombre'] . ' ' . $cliente['apellido']) : 'Cliente';
                        $telefonoCliente = $cliente['telefono'] ?? '';
                        
                        $serviciosTexto = !empty($serviciosNombres) ? implode(', ', $serviciosNombres) : 'Servicio no especificado';
                        
                        error_log("Preparando email para barbero: " . $barberia['barbero_email']);
                        
                        // 3. Enviar email al barbero
                        require_once __DIR__ . '/../classes/Email.php';
                        $emailBarbero = new \Classes\Email(
                            $barberia['barbero_email'],
                            $barberia['barbero_nombre'],
                            ''
                        );
                        
                        $envioExitoso = $emailBarbero->enviarNotificacionNuevaCita(
                            $nombreCliente,
                            $fecha,
                            $hora,
                            $serviciosTexto,
                            $totalServicios,
                            $telefonoCliente
                        );
                        
                        if($envioExitoso) {
                            error_log("✓ Notificación de cita enviada al barbero: " . $barberia['barbero_email']);
                        } else {
                            error_log("✗ Error al enviar notificación al barbero");
                        }
                        
                        // 4. Opcional: Enviar confirmación al cliente
                        try {
                            $clienteEmail = $_SESSION['email'] ?? '';
                            $clienteNombre = $_SESSION['nombre'] ?? '';
                            
                            if($clienteEmail && $clienteNombre) {
                                $emailCliente = new \Classes\Email($clienteEmail, $clienteNombre, '');
                                $emailCliente->enviarConfirmacionCita(
                                    $fecha,
                                    $hora,
                                    $serviciosTexto,
                                    $totalServicios,
                                    $barberia['nombre']
                                );
                                error_log("✓ Confirmación enviada al cliente: " . $clienteEmail);
                            }
                        } catch (\Exception $e) {
                            error_log("Error al enviar confirmación al cliente: " . $e->getMessage());
                            // No detener el proceso
                        }
                        
                    } else {
                        error_log("INFO: Barbero no tiene email configurado para la barbería {$barberia_id}");
                    }
                }
            } catch (\Exception $e) {
                error_log("ERROR al procesar notificaciones: " . $e->getMessage());
                error_log("Trace: " . $e->getTraceAsString());
                // No detener el proceso si falla el email
            }

            error_log("=== FIN guardar() - Éxito ===");
            
            echo json_encode([
                'resultado' => true,
                'id' => $citaId,
                'servicios_guardados' => $serviciosGuardados,
                'total' => $totalServicios,
                'notificacion_enviada' => true
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