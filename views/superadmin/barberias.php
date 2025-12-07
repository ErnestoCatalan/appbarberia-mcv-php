<h1 class="nombre-pagina">Gestión de Barberías</h1>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<div class="acciones">
    <a href="/superadmin" class="boton">← Volver al Dashboard</a>
</div>

<div class="listado-barberias-admin">
    <?php if(empty($barberias)): ?>
        <p class="text-center">No hay barberías registradas.</p>
    <?php else: ?>
        <div class="tabla-container">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Propietario</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($barberias as $barberia): ?>
                        <tr>
                            <td><?php echo $barberia->id; ?></td>
                            <td><?php echo htmlspecialchars($barberia->nombre); ?></td>
                            <td><?php echo htmlspecialchars($barberia->direccion ?? 'No especificada'); ?></td>
                            <td><?php echo htmlspecialchars($barberia->telefono); ?></td>
                            <td>
                                <span class="estado estado-<?php echo $barberia->estado; ?>">
                                    <?php echo ucfirst($barberia->estado); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $nombrePropietario = '';
                                if(isset($barberia->nombre_usuario) && $barberia->nombre_usuario) {
                                    $nombrePropietario = htmlspecialchars($barberia->nombre_usuario);
                                    if(isset($barberia->apellido_usuario) && $barberia->apellido_usuario) {
                                        $nombrePropietario .= ' ' . htmlspecialchars($barberia->apellido_usuario);
                                    }
                                }
                                
                                if($nombrePropietario): ?>
                                    <?php echo $nombrePropietario; ?>
                                <?php else: ?>
                                    <span class="sin-propietario">Sin propietario</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if(isset($barberia->creado_en) && $barberia->creado_en) {
                                    echo date('d/m/Y H:i', strtotime($barberia->creado_en));
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                            <td class="acciones-celda">
                                <div class="botones-accion">
                                    <?php if($barberia->estado === 'pendiente'): ?>
                                        <form action="/superadmin/cambiar-estado-barberia" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $barberia->id; ?>">
                                            <input type="hidden" name="estado" value="aprobada">
                                            <button type="submit" class="boton-accion aprobar" onclick="return confirm('¿Aprobar esta barbería?')">
                                                Aprobar
                                            </button>
                                        </form>
                                        
                                        <form action="/superadmin/cambiar-estado-barberia" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $barberia->id; ?>">
                                            <input type="hidden" name="estado" value="rechazada">
                                            <button type="submit" class="boton-accion rechazar" onclick="return confirm('¿Rechazar esta barbería?')">
                                                Rechazar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form action="/superadmin/eliminar-barberia" method="POST" style="display: inline;" class="form-eliminar">
                                        <input type="hidden" name="id" value="<?php echo $barberia->id; ?>">
                                        <button type="submit" class="boton-accion eliminar">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$script = "
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmar eliminaciones
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                if(!confirm('¿Estás seguro de eliminar esta barbería? Esta acción eliminará todos los servicios y citas asociadas.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
";
?>