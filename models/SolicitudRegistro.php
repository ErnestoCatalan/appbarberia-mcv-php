<?php
namespace Model;

class SolicitudRegistro extends ActiveRecord {
    protected static $tabla = 'solicitudes_registro';
    protected static $columnasDB = [
        'id', 'nombre_barberia', 'direccion', 'telefono', 
        'email', 'nombre_propietario', 'documentos', 
        'estado', 'usuario_id', 'creado_en'
    ];

    public $id;
    public $nombre_barberia;
    public $direccion;
    public $telefono;
    public $email;
    public $nombre_propietario;
    public $documentos;
    public $estado;
    public $usuario_id;
    public $creado_en;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre_barberia = $args['nombre_barberia'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->nombre_propietario = $args['nombre_propietario'] ?? '';
        $this->documentos = $args['documentos'] ?? '';
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->creado_en = $args['creado_en'] ?? date('Y-m-d H:i:s');
    }

    public function validar() {
        if(!$this->nombre_barberia) {
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
        if(!$this->nombre_propietario) {
            self::$alertas['error'][] = 'El nombre del propietario es obligatorio';
        }
        return self::$alertas;
    }

    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value ) {
            if ($key === 'creado_en' && empty($value)) {
                $sanitizado[$key] = 'NOW()';
            } else {
                $sanitizado[$key] = self::$db->escape_string($value ?? '');
            }
        }
        return $sanitizado;
    }

    public static function obtenerConUsuario() {
        $query = "SELECT sr.*, u.nombre as nombre_usuario, u.apellido as apellido_usuario, u.email as email_usuario
                  FROM " . static::$tabla . " sr
                  LEFT JOIN usuarios u ON sr.usuario_id = u.id
                  ORDER BY sr.creado_en DESC";
        return self::consultarSQL($query);
    }

    public static function contarTodas() {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla;
        $resultado = self::$db->query($query);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }
}