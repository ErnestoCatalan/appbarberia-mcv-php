<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<h1 class="nombre-pagina">Gestionar Solicitudes</h1>
<p class="descripcion-pagina">Revisa y aprueba solicitudes de barberías</p>

<?php 
// Mostrar errores si existen
if(isset($_SESSION['error'])): ?>
    <div class="alerta error">
        <?php echo $_SESSION['error']; ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if(empty($solicitudes)): ?>
    <p class="text-center">No hay solicitudes pendientes.</p>
<?php else: ?>
    <div class="listado-solicitudes">
        <?php foreach($solicitudes as $solicitud): ?>
            <div class="solicitud <?php echo $solicitud->estado; ?>">
                <div class="solicitud-info">
                    <h3><?php echo $solicitud->nombre_barberia; ?></h3>
                    <p><strong>Propietario:</strong> <?php echo $solicitud->nombre_propietario; ?></p>
                    <p><strong>Dirección:</strong> <?php echo $solicitud->direccion; ?></p>
                    <p><strong>Teléfono:</strong> <?php echo $solicitud->telefono; ?></p>
                    <p><strong>Email:</strong> <?php echo $solicitud->email; ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="estado-<?php echo $solicitud->estado; ?>">
                            <?php echo ucfirst($solicitud->estado); ?>
                        </span>
                    </p>
                    <p><strong>Fecha solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($solicitud->creado_en)); ?></p>
                    
                    <!-- Información del usuario solicitante -->
                    <?php 
                    if($solicitud->usuario_id):
                        $usuarioSolicitante = \Model\Usuario::find($solicitud->usuario_id);
                        if($usuarioSolicitante): ?>
                    <p><strong>Usuario:</strong> <?php echo $usuarioSolicitante->nombre . ' ' . $usuarioSolicitante->apellido; ?></p>
                    <p><strong>Email usuario:</strong> <?php echo $usuarioSolicitante->email; ?></p>
                    <p><strong>Tipo actual:</strong> <?php echo $usuarioSolicitante->tipo; ?></p>
                    <?php 
                        endif; 
                    endif; ?>
                </div>

                <?php if($solicitud->estado === 'pendiente'): ?>
                <div class="solicitud-acciones">
                    <form action="/solicitudes/aprobar" method="POST" class="form-accion" onsubmit="return confirm('¿Estás seguro de aprobar esta solicitud?')">
                        <input type="hidden" name="id" value="<?php echo $solicitud->id; ?>">
                        <input type="submit" class="boton" value="Aprobar">
                    </form>
                    <form action="/solicitudes/rechazar" method="POST" class="form-accion" onsubmit="return confirm('¿Estás seguro de rechazar esta solicitud?')">
                        <input type="hidden" name="id" value="<?php echo $solicitud->id; ?>">
                        <input type="submit" class="boton-eliminar" value="Rechazar">
                    </form>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>