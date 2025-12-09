<?php
include_once __DIR__ . '/../templates/barra.php';
include_once __DIR__ . '/../templates/alertas.php';
?>

<h1 class="nombre-pagina">Solicitud de Registro</h1>
<p class="descripcion-pagina">Completa el formulario para registrar tu barbería</p>

<form class="formulario" method="POST" action="/solicitud">
    <div class="campo">
        <label for="nombre_barberia">Nombre de la Barbería</label>
        <input
            type="text"
            id="nombre_barberia"
            name="nombre_barberia"
            placeholder="Nombre de tu barbería"
            value="<?php echo htmlspecialchars($solicitud->nombre_barberia ?? ''); ?>"
            required
        />
    </div>

    <div class="campo">
        <label for="direccion">Dirección</label>
        <textarea
            id="direccion"
            name="direccion"
            placeholder="Dirección completa de tu barbería"
            required
        ><?php echo htmlspecialchars($solicitud->direccion ?? ''); ?></textarea>
    </div>

    <div class="campo">
        <label for="telefono">Teléfono</label>
        <input
            type="tel"
            id="telefono"
            name="telefono"
            placeholder="Teléfono de contacto"
            value="<?php echo htmlspecialchars($solicitud->telefono ?? ''); ?>"
            required
        />
    </div>

    <div class="campo">
        <label for="email">Email de la Barbería</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Email de contacto"
            value="<?php echo htmlspecialchars($solicitud->email ?? ''); ?>"
            required
        />
    </div>

    <div class="campo">
        <label for="nombre_propietario">Nombre del Propietario</label>
        <input
            type="text"
            id="nombre_propietario"
            name="nombre_propietario"
            placeholder="Tu nombre completo"
            value="<?php echo htmlspecialchars($solicitud->nombre_propietario ?? ''); ?>"
            required
        />
    </div>

    <!-- Nuevos campos para horario -->
    <div class="horario-container">
        <h3>Horario de Atención</h3>
        
        <div class="campos-horario">
            <div class="campo">
                <label for="horario_apertura">Hora de Apertura</label>
                <select id="horario_apertura" name="horario_apertura" required>
                    <option value="">Selecciona hora</option>
                    <?php 
                    $horaActual = $solicitud->horario_apertura ?? '09:00:00';
                    for($h = 6; $h <= 12; $h++): 
                        $hora = str_pad($h, 2, '0', STR_PAD_LEFT);
                    ?>
                    <option value="<?php echo $hora . ':00:00'; ?>" <?php echo $horaActual == $hora . ':00:00' ? 'selected' : ''; ?>>
                        <?php echo $hora . ':00 AM'; ?>
                    </option>
                    <?php endfor; ?>
                    <?php for($h = 1; $h <= 11; $h++): 
                        $hora = str_pad($h, 2, '0', STR_PAD_LEFT);
                    ?>
                    <option value="<?php echo str_pad($h+12, 2, '0', STR_PAD_LEFT) . ':00:00'; ?>" <?php echo $horaActual == str_pad($h+12, 2, '0', STR_PAD_LEFT) . ':00:00' ? 'selected' : ''; ?>>
                        <?php echo $hora . ':00 PM'; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="campo">
                <label for="horario_cierre">Hora de Cierre</label>
                <select id="horario_cierre" name="horario_cierre" required>
                    <option value="">Selecciona hora</option>
                    <?php 
                    $horaActualCierre = $solicitud->horario_cierre ?? '19:00:00';
                    for($h = 6; $h <= 12; $h++): 
                        $hora = str_pad($h, 2, '0', STR_PAD_LEFT);
                    ?>
                    <option value="<?php echo $hora . ':00:00'; ?>" <?php echo $horaActualCierre == $hora . ':00:00' ? 'selected' : ''; ?>>
                        <?php echo $hora . ':00 AM'; ?>
                    </option>
                    <?php endfor; ?>
                    <?php for($h = 1; $h <= 11; $h++): 
                        $hora = str_pad($h, 2, '0', STR_PAD_LEFT);
                    ?>
                    <option value="<?php echo str_pad($h+12, 2, '0', STR_PAD_LEFT) . ':00:00'; ?>" <?php echo $horaActualCierre == str_pad($h+12, 2, '0', STR_PAD_LEFT) . ':00:00' ? 'selected' : ''; ?>>
                        <?php echo $hora . ':00 PM'; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        
        <p class="ayuda-horario">
            <i class="fas fa-info-circle"></i> Selecciona el horario en que tu barbería estará disponible para atender citas.
        </p>
    </div>

    <input type="submit" class="boton" value="Enviar Solicitud">
</form>

<div class="acciones">
    <a href="/barberias">Volver a Barberías</a>
</div>

<style>
.horario-container {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    border-left: 4px solid #d4af37;
}

.horario-container h3 {
    color: #d4af37;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
}

.campos-horario {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.ayuda-horario {
    margin-top: 1rem;
    font-size: 1.4rem;
    color: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ayuda-horario i {
    color: #d4af37;
}

@media (max-width: 768px) {
    .campos-horario {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>

<script>
// Validación: Hora de cierre debe ser después de hora de apertura
document.querySelector('form').addEventListener('submit', function(e) {
    const apertura = document.getElementById('horario_apertura').value;
    const cierre = document.getElementById('horario_cierre').value;
    
    if(apertura && cierre) {
        const horaApertura = parseInt(apertura.split(':')[0]);
        const horaCierre = parseInt(cierre.split(':')[0]);
        
        if(horaCierre <= horaApertura) {
            e.preventDefault();
            alert('La hora de cierre debe ser posterior a la hora de apertura');
            return false;
        }
        
        // Validar que haya al menos 8 horas de diferencia
        if((horaCierre - horaApertura) < 8) {
            e.preventDefault();
            alert('El horario de atención debe ser de al menos 8 horas');
            return false;
        }
    }
    
    return true;
});
</script>