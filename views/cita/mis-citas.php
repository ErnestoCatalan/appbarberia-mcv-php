<?php
include_once __DIR__ . '/../templates/barra.php';
?>

<h1 class="nombre-pagina">Mis Citas</h1>
<p class="descripcion-pagina">Aquí puedes ver y gestionar todas tus citas</p>

<?php if(empty($citas)): ?>
    <div class="no-citas">
        <i class="fas fa-calendar-times"></i>
        <h3>No tienes citas programadas</h3>
        <p>¿Listo para tu próximo corte? Agenda una cita en tu barbería favorita.</p>
        <a href="/barberias" class="boton">
            <i class="fas fa-cut"></i> Ver Barberías
        </a>
    </div>
<?php else: ?>
    <div class="citas-container">
        <?php foreach($citas as $cita): ?>
            <div class="cita-card">
                <div class="cita-header">
                    <div class="cita-fecha">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>
                            <?php 
                            $fecha = $cita->fecha ?? '';
                            echo $fecha ? date('d/m/Y', strtotime($fecha)) : 'Fecha no disponible';
                            ?>
                        </h3>
                    </div>
                    <div class="cita-hora">
                        <i class="fas fa-clock"></i>
                        <span>
                            <?php 
                            $hora = $cita->hora ?? '';
                            echo $hora ? date('h:i A', strtotime($hora)) : 'Hora no disponible';
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="cita-body">
                    <div class="cita-info">
                        <p>
                            <strong>Barbería:</strong> 
                            <?php 
                            if (!empty($cita->barberia_nombre)) {
                                echo htmlspecialchars($cita->barberia_nombre);
                            } elseif (!empty($cita->barberia_id)) {
                                echo '<span style="color: #666; font-style: italic;">Barbería ID: ' . 
                                     htmlspecialchars($cita->barberia_id) . '</span>';
                            } else {
                                echo '<span style="color: #ff6b6b; font-style: italic;">No especificada</span>';
                            }
                            ?>
                        </p>
                        
                        <?php if(!empty($cita->servicios)): ?>
                            <p>
                                <strong>Servicios:</strong> 
                                <?php echo htmlspecialchars($cita->servicios); ?>
                                <?php if(!empty($cita->cantidad_servicios) && $cita->cantidad_servicios > 1): ?>
                                    <span style="color: #666; font-size: 1.3rem;">
                                        (<?php echo $cita->cantidad_servicios; ?> servicios)
                                    </span>
                                <?php endif; ?>
                            </p>
                        <?php else: ?>
                            <p>
                                <strong>Servicios:</strong> 
                                <span style="color: #ff6b6b; font-style: italic;">
                                    No especificados
                                    <?php if(!empty($cita->cantidad_servicios) && $cita->cantidad_servicios > 0): ?>
                                        (<?php echo $cita->cantidad_servicios; ?> servicios sin nombre)
                                    <?php endif; ?>
                                </span>
                            </p>
                        <?php endif; ?>
                        
                        <p>
                            <strong>Total:</strong> 
                            <span class="total-cita">
                                $<?php 
                                if (!empty($cita->total) && $cita->total > 0) {
                                    echo number_format($cita->total, 2);
                                } else {
                                    echo '0.00';
                                }
                                ?>
                            </span>
                        </p>
                        
                        <?php if(!empty($cita->barberia_direccion)): ?>
                        <p>
                            <strong>Dirección:</strong> 
                            <?php echo htmlspecialchars($cita->barberia_direccion); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if(!empty($cita->barberia_telefono)): ?>
                        <p>
                            <strong>Teléfono:</strong> 
                            <?php echo htmlspecialchars($cita->barberia_telefono); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cita-acciones">
                        <button onclick="mostrarModalCancelar(
                            <?php echo $cita->id; ?>, 
                            '<?php echo addslashes($cita->barberia_nombre ?? 'Barbería'); ?>',
                            '<?php echo $cita->fecha ?? ''; ?>',
                            '<?php echo $cita->hora ?? ''; ?>'
                        )" class="btn-cancelar-cita">
                            <i class="fas fa-times"></i> Cancelar Cita
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="acciones-centro">
    <a href="/barberias" class="boton">
        <i class="fas fa-plus"></i> Nueva Cita
    </a>
</div>

<!-- Modal para cancelar cita -->
<div id="modalCancelarCita" class="modal">
    <div class="modal-content">
        <button class="close-modal" onclick="cerrarModalCancelar()">&times;</button>
        <h2><i class="fas fa-exclamation-triangle" style="color: #ff6b6b;"></i> Confirmar Cancelación</h2>
        <p>¿Estás seguro de cancelar esta cita? Esta acción <strong>no se puede deshacer</strong>.</p>
        <p id="infoCitaCancelar" style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin: 1rem 0;"></p>
        
        <div class="modal-acciones">
            <form id="formCancelarCita" method="POST" action="/api/eliminar">
                <input type="hidden" id="idCitaCancelar" name="id" value="">
                <button type="submit" class="boton-eliminar-confirmar">
                    <i class="fas fa-times"></i> Sí, cancelar cita
                </button>
            </form>
            <button onclick="cerrarModalCancelar()" class="boton-cancelar">
                <i class="fas fa-arrow-left"></i> No, conservar cita
            </button>
        </div>
    </div>
</div>

<style>
.no-citas {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    margin: 2rem 0;
    
    i {
        font-size: 5rem;
        color: rgba(212, 175, 55, 0.3);
        margin-bottom: 2rem;
    }
    
    h3 {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 1rem;
        font-size: 2rem;
    }
    
    p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 1.6rem;
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .boton {
        padding: 1rem 3rem;
        font-size: 1.6rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
}

.citas-container {
    max-width: 800px;
    margin: 3rem auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.cita-card {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    overflow: hidden;
    border: 1px solid rgba(212, 175, 55, 0.1);
    transition: all 0.3s ease;
    
    &:hover {
        transform: translateY(-3px);
        border-color: rgba(212, 175, 55, 0.3);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
}

.cita-header {
    background: rgba(212, 175, 55, 0.1);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(212, 175, 55, 0.2);
}

.cita-fecha {
    display: flex;
    align-items: center;
    gap: 1rem;
    
    i {
        color: #d4af37;
        font-size: 2rem;
    }
    
    h3 {
        margin: 0;
        color: #d4af37;
        font-size: 1.8rem;
    }
}

.cita-hora {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(0, 0, 0, 0.3);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    
    i {
        color: #fff;
        font-size: 1.4rem;
    }
    
    span {
        color: #fff;
        font-weight: 600;
        font-size: 1.4rem;
    }
}

.cita-body {
    padding: 1.5rem;
}

.cita-info {
    margin-bottom: 1.5rem;
    
    p {
        margin-bottom: 0.8rem;
        font-size: 1.5rem;
        display: flex;
        align-items: flex-start;
        
        strong {
            color: #d4af37;
            min-width: 100px;
            display: inline-block;
        }
        
        .total-cita {
            color: #4caf50;
            font-weight: bold;
            font-size: 1.8rem;
        }
    }
}

.cita-acciones {
    display: flex;
    justify-content: flex-end;
}

.btn-cancelar-cita {
    padding: 0.8rem 1.5rem;
    background: rgba(255, 107, 107, 0.2);
    color: #ff6b6b;
    border: 2px solid #ff6b6b;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.4rem;
    
    &:hover {
        background: #ff6b6b;
        color: white;
        transform: translateY(-2px);
    }
}

.acciones-centro {
    text-align: center;
    margin-top: 3rem;
    
    .boton {
        padding: 1rem 3rem;
        font-size: 1.6rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
}

/* Estilos del modal (usar los mismos que ya tienes) */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 3rem;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;
    position: relative;
    animation: modalFade 0.3s;
    color: #333;
}

@keyframes modalFade {
    from { opacity: 0; transform: translateY(-50px); }
    to { opacity: 1; transform: translateY(0); }
}

.close-modal {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
}

.modal-content h2 {
    color: #333;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-content p {
    margin-bottom: 1.5rem;
    line-height: 1.6;
    color: #555;
}

.modal-acciones {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.boton-eliminar-confirmar {
    flex: 1;
    padding: 0.8rem 2rem;
    background: #ff6b6b;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.boton-eliminar-confirmar:hover {
    background: #ff5252;
    transform: translateY(-2px);
}

.boton-cancelar {
    flex: 1;
    padding: 0.8rem 2rem;
    background: #666;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.boton-cancelar:hover {
    background: #555;
    transform: translateY(-2px);
}
</style>

<script>
// Funciones para el modal de cancelación
function mostrarModalCancelar(citaId, barberiaNombre, fecha, hora) {
    // Formatear fecha y hora
    const fechaFormateada = fecha ? new Date(fecha).toLocaleDateString('es-MX') : 'Fecha no disponible';
    const horaFormateada = hora ? new Date('1970-01-01T' + hora + 'Z').toLocaleTimeString('es-MX', 
        { hour: '2-digit', minute: '2-digit' }) : 'Hora no disponible';
    
    document.getElementById('idCitaCancelar').value = citaId;
    document.getElementById('infoCitaCancelar').innerHTML = `
        <strong>Barbería:</strong> ${barberiaNombre}<br>
        <strong>Fecha:</strong> ${fechaFormateada}<br>
        <strong>Hora:</strong> ${horaFormateada}
    `;
    document.getElementById('modalCancelarCita').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalCancelar() {
    document.getElementById('modalCancelarCita').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modalCancelarCita');
    if (event.target === modal) {
        cerrarModalCancelar();
    }
}

// Confirmar cancelación de cita
document.getElementById('formCancelarCita').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if(!confirm('¿Estás completamente seguro de cancelar esta cita?')) {
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('/api/eliminar', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok || response.redirected) {
            // Recargar la página para mostrar cambios
            window.location.reload();
        } else {
            return response.text();
        }
    })
    .then(data => {
        if (data) {
            console.error('Error:', data);
            alert('Error al cancelar la cita. Por favor, intenta de nuevo.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cancelar la cita. Por favor, intenta de nuevo.');
    });
});

// Mostrar mensaje de éxito si viene de una creación exitosa
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('exito')) {
        Swal.fire({
            icon: 'success',
            title: '¡Cita creada!',
            text: 'Tu cita ha sido agendada exitosamente',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true
        });
    }
});
</script>

<?php 
    // Incluir SweetAlert2 para las alertas
    $script = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    ";
?>