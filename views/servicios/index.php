<h1 class="nombre-pagina">Mis Servicios</h1>
<p class="descripcion-pagina">Administración de Servicios</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<div class="servicios-contenedor">
    <?php if(empty($servicios)): ?>
        <div class="no-servicios">
            <i class="fas fa-cut"></i>
            <h3>No tienes servicios registrados</h3>
            <p>Crea tu primer servicio para empezar a recibir citas</p>
            <a href="/servicios/crear" class="boton">Crear Primer Servicio</a>
        </div>
    <?php else: ?>
        <div class="servicios-grid">
            <?php foreach($servicios as $servicio): ?>
                <div class="servicio-card">
                    <?php if($servicio->imagen): ?>
                    <div class="servicio-imagen">
                        <img src="/uploads/servicios/<?php echo htmlspecialchars($servicio->imagen); ?>" 
                             alt="<?php echo htmlspecialchars($servicio->nombre); ?>"
                             onerror="this.src='/build/img/servicio-default.jpg'">
                    </div>
                    <?php else: ?>
                    <div class="servicio-imagen default">
                        <i class="fas fa-cut"></i>
                    </div>
                    <?php endif; ?>
                    
                    <div class="servicio-info">
                        <h3><?php echo htmlspecialchars($servicio->nombre); ?></h3>
                        
                        <?php if($servicio->descripcion): ?>
                        <p class="servicio-descripcion"><?php echo htmlspecialchars($servicio->descripcion); ?></p>
                        <?php endif; ?>
                        
                        <div class="servicio-detalles">
                            <p class="precio"><i class="fas fa-tag"></i> $<?php echo number_format($servicio->precio, 2); ?></p>
                            <p class="duracion"><i class="fas fa-clock"></i> <?php echo $servicio->duracion; ?> min</p>
                        </div>
                    </div>
                    
                    <div class="servicio-acciones">
                        <a href="/servicios/actualizar?id=<?php echo $servicio->id; ?>" 
                           class="boton-accion editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        
                        <button type="button" 
                                onclick="mostrarModalEliminar(<?php echo $servicio->id; ?>, '<?php echo htmlspecialchars(addslashes($servicio->nombre)); ?>')" 
                                class="boton-accion eliminar">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="acciones-centro">
            <a href="/servicios/crear" class="boton">
                <i class="fas fa-plus"></i> Nuevo Servicio
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmación para eliminar servicio -->
<div id="modalEliminar" class="modal">
    <div class="modal-content">
        <button class="close-modal" onclick="cerrarModalEliminar()">&times;</button>
        <h2><i class="fas fa-exclamation-triangle" style="color: #ff6b6b;"></i> Confirmar Eliminación</h2>
        <p>¿Estás seguro de eliminar este servicio? Esta acción <strong>no se puede deshacer</strong> y se eliminará permanentemente del sistema.</p>
        <p id="nombreServicioEliminar" style="font-weight: bold; color: #ff6b6b; font-size: 1.6rem;"></p>
        
        <div class="modal-acciones">
            <form id="formEliminarServicio" method="POST" action="/servicios/eliminar">
                <input type="hidden" id="idServicioEliminar" name="id" value="">
                <button type="submit" class="boton-eliminar-confirmar">
                    <i class="fas fa-trash"></i> Sí, eliminar servicio
                </button>
            </form>
            <button onclick="cerrarModalEliminar()" class="boton-cancelar">
                <i class="fas fa-times"></i> Cancelar
            </button>
        </div>
    </div>
</div>

<script>
// Funciones para el modal de eliminación
function mostrarModalEliminar(servicioId, servicioNombre) {
    document.getElementById('idServicioEliminar').value = servicioId;
    document.getElementById('nombreServicioEliminar').textContent = `"${servicioNombre}"`;
    document.getElementById('modalEliminar').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminar').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalEliminar');
    if (event.target === modal) {
        cerrarModalEliminar();
    }
}

// Confirmar si se abandona la página con cambios
window.addEventListener('beforeunload', function(e) {
    const formularios = document.querySelectorAll('form');
    let tieneCambios = false;
    
    formularios.forEach(form => {
        if (form.classList.contains('dirty')) {
            tieneCambios = true;
        }
    });
    
    if (tieneCambios) {
        e.preventDefault();
        e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de salir?';
    }
});

// Marcar formularios como "sucios" cuando se modifican
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.classList.add('dirty');
            }
        });
    });
});
</script>

