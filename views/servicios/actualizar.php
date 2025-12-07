<h1 class="nombre-pagina">Actualizar Servicio</h1>
<p class="descripcion-pagina">Modifica los campos para actualizar el servicio</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php';
    include_once __DIR__ . '/../templates/alerta_exito.php';
?>

<form action="/servicios/actualizar?id=<?php echo $servicio->id; ?>" method="POST" class="formulario" enctype="multipart/form-data">
    <div class="campo">
        <label for="nombre">Nombre del Servicio</label>
        <input
            type="text"
            id="nombre"
            placeholder="Ej: Corte Clásico"
            name="nombre"
            value="<?php echo htmlspecialchars($servicio->nombre); ?>"
            required
        >
    </div>

    <div class="campo">
        <label for="descripcion">Descripción (Opcional)</label>
        <textarea 
            id="descripcion" 
            name="descripcion" 
            placeholder="Describe brevemente el servicio..."
            rows="3"
        ><?php echo htmlspecialchars($servicio->descripcion); ?></textarea>
    </div>

    <div class="campo">
        <label for="precio">Precio ($)</label>
        <input
            type="number"
            id="precio"
            placeholder="Ej: 150"
            name="precio"
            value="<?php echo htmlspecialchars($servicio->precio); ?>"
            min="0"
            step="0.01"
            required
        >
    </div>

    <div class="campo">
        <label for="duracion">Duración (minutos)</label>
        <select id="duracion" name="duracion" required>
            <option value="15" <?php echo $servicio->duracion == 15 ? 'selected' : ''; ?>>15 min</option>
            <option value="30" <?php echo $servicio->duracion == 30 ? 'selected' : ''; ?>>30 min</option>
            <option value="45" <?php echo $servicio->duracion == 45 ? 'selected' : ''; ?>>45 min</option>
            <option value="60" <?php echo $servicio->duracion == 60 ? 'selected' : ''; ?>>60 min</option>
            <option value="90" <?php echo $servicio->duracion == 90 ? 'selected' : ''; ?>>90 min</option>
        </select>
    </div>

    <div class="campo">
        <label for="imagen">Foto del Servicio</label>
        <div class="file-upload">
            <?php if($servicio->imagen): ?>
            <div class="imagen-actual">
                <p><strong>Imagen actual:</strong></p>
                <div class="image-preview" id="currentImagePreview">
                    <img src="/uploads/servicios/<?php echo htmlspecialchars($servicio->imagen); ?>" 
                         alt="Imagen actual del servicio"
                         style="max-width: 200px; max-height: 150px; border-radius: 8px; margin-bottom: 10px;">
                </div>
                <div class="checkbox-eliminar">
                    <input type="checkbox" id="eliminar_imagen" name="eliminar_imagen">
                    <label for="eliminar_imagen">Eliminar imagen actual</label>
                </div>
                <p style="margin-top: 10px; font-style: italic;">O sube una nueva imagen para reemplazarla:</p>
            </div>
            <?php endif; ?>
            
            <input 
                type="file" 
                id="imagen" 
                name="imagen" 
                accept="image/*"
                onchange="previewImage(event)"
                <?php echo $servicio->imagen ? '' : 'required'; ?>
            >
            <label for="imagen" class="file-label">
                <i class="fas fa-upload"></i> 
                <span id="file-text"><?php echo $servicio->imagen ? 'Seleccionar nueva imagen...' : 'Seleccionar imagen...'; ?></span>
            </label>
            <div class="image-preview" id="newImagePreview">
                <!-- Aquí se mostrará la vista previa de la nueva imagen -->
            </div>
            <p class="file-help">
                <i class="fas fa-info-circle"></i> 
                Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB. Recomendado: 600x400px
            </p>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $servicio->id; ?>">
    
    <div class="acciones-formulario">
        <input type="submit" value="Actualizar Servicio" class="boton">
        <button type="button" onclick="mostrarModalEliminar(<?php echo $servicio->id; ?>)" class="boton-eliminar">
            <i class="fas fa-trash"></i> Eliminar Servicio
        </button>
        <a href="/servicios" class="btn-cancelar">Cancelar</a>
    </div>
</form>

<!-- Modal de confirmación para eliminar servicio -->
<div id="modalEliminar" class="modal">
    <div class="modal-content">
        <button class="close-modal" onclick="cerrarModalEliminar()">&times;</button>
        <h2><i class="fas fa-exclamation-triangle" style="color: #ff6b6b;"></i> Confirmar Eliminación</h2>
        <p>¿Estás seguro de eliminar este servicio? Esta acción <strong>no se puede deshacer</strong> y se eliminará permanentemente del sistema.</p>
        <p id="nombreServicioEliminar" style="font-weight: bold; color: #ff6b6b;"></p>
        
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
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('newImagePreview');
    const fileText = document.getElementById('file-text');
    
    // Limpiar preview anterior
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        fileText.textContent = file.name;
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '150px';
            img.style.borderRadius = '8px';
            img.style.marginTop = '10px';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(file);
    } else {
        fileText.textContent = '<?php echo $servicio->imagen ? "Seleccionar nueva imagen..." : "Seleccionar imagen..."; ?>';
    }
}

// Controlar checkbox para eliminar imagen
document.addEventListener('DOMContentLoaded', function() {
    const eliminarCheckbox = document.getElementById('eliminar_imagen');
    const imagenInput = document.getElementById('imagen');
    
    if(eliminarCheckbox && imagenInput) {
        eliminarCheckbox.addEventListener('change', function() {
            if(this.checked) {
                imagenInput.disabled = true;
                document.getElementById('file-text').textContent = 'Imagen será eliminada';
                document.getElementById('newImagePreview').innerHTML = '';
                imagenInput.removeAttribute('required');
            } else {
                imagenInput.disabled = false;
                document.getElementById('file-text').textContent = 'Seleccionar nueva imagen...';
                imagenInput.setAttribute('required', 'required');
            }
        });
    }
});

// Funciones para el modal de eliminación
function mostrarModalEliminar(servicioId) {
    const servicioNombre = document.querySelector('#nombre').value;
    document.getElementById('idServicioEliminar').value = servicioId;
    document.getElementById('nombreServicioEliminar').textContent = `Servicio: ${servicioNombre}`;
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

// Validar formulario antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const precio = document.getElementById('precio').value;
    const nombre = document.getElementById('nombre').value.trim();
    
    if(!nombre) {
        e.preventDefault();
        alert('El nombre del servicio es requerido');
        return;
    }
    
    if(!precio || parseFloat(precio) <= 0) {
        e.preventDefault();
        alert('El precio debe ser mayor a 0');
        return;
    }
});
</script>

