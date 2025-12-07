<?php 
namespace Model;

class Cita extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'citas';
    protected static $columnasDB = ['id', 'fecha', 'hora', 'usuarioId', 'barberia_id'];

    public $id;
    public $fecha;
    public $hora;
    public $usuarioId;
    public $barberia_id;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->fecha = $args['fecha'] ?? '';
        $this->hora = $args['hora'] ?? '';
        $this->usuarioId = $args['usuarioId'] ?? '';
        $this->barberia_id = $args['barberia_id'] ?? null;
    }

    // Validar antes de guardar
    public function validar() {
        if(!$this->fecha) {
            self::$alertas['error'][] = 'La fecha es obligatoria';
        }
        if(!$this->hora) {
            self::$alertas['error'][] = 'La hora es obligatoria';
        }
        if(!$this->usuarioId) {
            self::$alertas['error'][] = 'El ID de usuario es obligatorio';
        }
        if(!$this->barberia_id) {
            self::$alertas['error'][] = 'El ID de la barbería es obligatorio';
        }
        return self::$alertas;
    }

    // Obtener citas por barbería
    public static function porBarberia($barberia_id, $fecha = null) {
        $where = "barberia_id = {$barberia_id}";
        if ($fecha) {
            $where .= " AND fecha = '{$fecha}'";
        }
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$where}";
        return self::consultarSQL($query);
    }
}