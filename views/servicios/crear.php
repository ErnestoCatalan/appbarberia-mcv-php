<h1 class="nombre-pagina">Nuevo Servicio</h1>
<p class="descripcion-pagina">Llena todos los campos para añadir un nuevo servicio</p>

<?php
    include_once __DIR__ . '/../templates/barra.php';
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form action="/servicios/crear" method="POST" class="formulario" enctype="multipart/form-data">
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
        <label for="imagen">Foto del Servicio (Opcional)</label>
        <div class="file-upload">
            <input 
                type="file" 
                id="imagen" 
                name="imagen" 
                accept="image/*"
                onchange="previewImage(event)"
            >
            <label for="imagen" class="file-label">
                <i class="fas fa-upload"></i> 
                <span id="file-text">Seleccionar imagen...</span>
            </label>
            <div class="image-preview" id="imagePreview">
                <?php if($servicio->imagen): ?>
                    <img src="/uploads/servicios/<?php echo htmlspecialchars($servicio->imagen); ?>" alt="Vista previa">
                <?php endif; ?>
            </div>
            <p class="file-help">
                <i class="fas fa-info-circle"></i> 
                Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB. Recomendado: 600x400px
            </p>
        </div>
    </div>

    <div class="acciones-formulario">
        <input type="submit" value="Crear Servicio" class="boton">
        <a href="/servicios" class="btn-cancelar">Cancelar</a>
    </div>
</form>

<!-- Modal de confirmación para cancelar cambios -->
<div id="modalCancelar" class="modal">
    <div class="modal-content">
        <button class="close-modal" onclick="cerrarModalCancelar()">&times;</button>
        <h2><i class="fas fa-exclamation-circle" style="color: #ffc107;"></i> Cambios sin guardar</h2>
        <p>Tienes cambios sin guardar en el formulario. ¿Estás seguro de que quieres cancelar?</p>
        
        <div class="modal-acciones">
            <button onclick="window.location.href='/servicios'" class="boton-confirmar-cancelar">
                <i class="fas fa-check"></i> Sí, cancelar
            </button>
            <button onclick="cerrarModalCancelar()" class="boton-continuar">
                <i class="fas fa-times"></i> No, continuar editando
            </button>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('imagePreview');
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
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(file);
    } else {
        fileText.textContent = 'Seleccionar imagen...';
    }
}

// Funciones para el modal de cancelar
function mostrarModalCancelar() {
    document.getElementById('modalCancelar').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    return false; // Prevenir navegación inmediata
}

function cerrarModalCancelar() {
    document.getElementById('modalCancelar').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Interceptar clic en el botón Cancelar
document.addEventListener('DOMContentLoaded', function() {
    const btnCancelar = document.querySelector('.btn-cancelar');
    
    if(btnCancelar) {
        btnCancelar.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Verificar si hay cambios en el formulario
            const form = document.querySelector('form');
            if(form && form.classList.contains('dirty')) {
                mostrarModalCancelar();
            } else {
                // Si no hay cambios, navegar directamente
                window.location.href = '/servicios';
            }
        });
    }
    
    // Marcar formulario como "sucio" cuando se modifica
    const form = document.querySelector('form');
    if(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                form.classList.add('dirty');
            });
            
            input.addEventListener('keyup', function() {
                form.classList.add('dirty');
            });
        });
    }
});

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
