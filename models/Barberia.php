<?php
namespace Model;

class Barberia extends ActiveRecord {
    protected static $tabla = 'barberias';
    protected static $columnasDB = [
        'id', 'nombre', 'direccion', 'telefono', 'email', 
        'descripcion', 'imagen', 'horario_apertura', 'horario_cierre',
        'estado', 'usuario_id', 'creado_en', 'latitud', 'longitud' // Agregar estos
    ];

    public $id;
    public $nombre;
    public $direccion;
    public $telefono;
    public $email;
    public $descripcion;
    public $imagen;
    public $horario_apertura;
    public $horario_cierre;
    public $estado;
    public $usuario_id;
    public $creado_en;
    public $latitud;
    public $longitud;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->horario_apertura = $args['horario_apertura'] ?? '09:00:00';
        $this->horario_cierre = $args['horario_cierre'] ?? '19:00:00';
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->creado_en = $args['creado_en'] ?? '';
        $this->latitud = $args['latitud'] ?? null;
        $this->longitud = $args['longitud'] ?? null;
    }

    public function validar() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la barbería es obligatorio';
        }
        if(!$this->direccion) {
            self::$alertas['error'][] = 'La dirección es obligatoria';
        }
        if(!$this->telefono) {
            self::$alertas['error'][] = 'El teléfono es obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        return self::$alertas;
    }

    public static function obtenerAprobadas() {
        $query = "SELECT * FROM " . static::$tabla . " WHERE estado = 'aprobada'";
        return self::consultarSQL($query);
    }

    // Agregar métodos para superadmin
    public static function todasConUsuarios() {
        $query = "SELECT b.*, u.nombre as nombre_usuario, u.apellido as apellido_usuario 
                  FROM " . static::$tabla . " b
                  LEFT JOIN usuarios u ON b.usuario_id = u.id
                  ORDER BY b.creado_en DESC";
        return self::consultarSQL($query);
    }

    public static function obtenerTodas() {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY creado_en DESC";
        return self::consultarSQL($query);
    }

    public static function contarTodas() {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla;
        $resultado = self::$db->query($query);
        if($resultado) {
            $fila = $resultado->fetch_assoc();
            return (int)$fila['total'];
        }
        return 0;
    }

    public static function contarPorEstado($estado) {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE estado = '" . self::$db->escape_string($estado) . "'";
        $resultado = self::$db->query($query);
        if($resultado) {
            $fila = $resultado->fetch_assoc();
            return (int)$fila['total'];
        }
        return 0;
    }
}