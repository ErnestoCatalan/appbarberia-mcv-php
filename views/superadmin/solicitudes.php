<h1 class="nombre-pagina">Gestión de Solicitudes</h1>

<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<div class="acciones">
    <a href="/superadmin" class="boton">← Volver al Dashboard</a>
</div>

<div class="listado-solicitudes-admin">
    <?php if(empty($solicitudes)): ?>
        <p class="text-center">No hay solicitudes registradas.</p>
    <?php else: ?>
        <div class="tabla-container">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Barbería</th>
                        <th>Dirección</th>
                        <th>Propietario</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Fecha Solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($solicitudes as $solicitud): ?>
                        <tr>
                            <td><?php echo $solicitud->id; ?></td>
                            <td><?php echo htmlspecialchars($solicitud->nombre_barberia); ?></td>
                            <td><?php echo htmlspecialchars($solicitud->direccion); ?></td>
                            <td>
                                <?php 
                                if(isset($solicitud->nombre_usuario) && $solicitud->nombre_usuario) {
                                    echo htmlspecialchars($solicitud->nombre_usuario);
                                    if(isset($solicitud->apellido_usuario) && $solicitud->apellido_usuario) {
                                        echo ' ' . htmlspecialchars($solicitud->apellido_usuario);
                                    }
                                } else {
                                    echo htmlspecialchars($solicitud->nombre_propietario);
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($solicitud->email); ?></td>
                            <td><?php echo htmlspecialchars($solicitud->telefono); ?></td>
                            <td>
                                <span class="estado estado-<?php echo $solicitud->estado; ?>">
                                    <?php echo ucfirst($solicitud->estado); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if(isset($solicitud->creado_en) && $solicitud->creado_en) {
                                    echo date('d/m/Y H:i', strtotime($solicitud->creado_en));
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                            <td class="acciones-celda">
                                <div class="botones-accion">
                                    <?php if($solicitud->estado === 'pendiente'): ?>
                                        <form action="/solicitudes/aprobar" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $solicitud->id; ?>">
                                            <button type="submit" class="boton-accion aprobar" onclick="return confirm('¿Aprobar esta solicitud?')">
                                                Aprobar
                                            </button>
                                        </form>
                                        
                                        <form action="/solicitudes/rechazar" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?php echo $solicitud->id; ?>">
                                            <button type="submit" class="boton-accion rechazar" onclick="return confirm('¿Rechazar esta solicitud?')">
                                                Rechazar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form action="/superadmin/eliminar-solicitud" method="POST" style="display: inline;" class="form-eliminar">
                                        <input type="hidden" name="id" value="<?php echo $solicitud->id; ?>">
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