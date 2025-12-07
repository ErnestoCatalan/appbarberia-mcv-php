let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function () {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();
    tabs();
    botonesPaginador();
    paginaSiguiente();
    paginaAnterior();

    consultarAPI();
    idCliente();
    nombreCliente();
    seleccionarFecha();
    seleccionarHora();
    mostrarResumen();
}

function mostrarSeccion() {
    // Ocultar la seccion que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la seccion con el paso..
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    if(seccion) {
        seccion.classList.add('mostrar');
    }

    // Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if (tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    if(tab) {
        tab.classList.add('actual');
    }
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach(boton => {
        boton.addEventListener('click', function (e) {
            paso = parseInt(e.target.dataset.paso);
            mostrarSeccion();
            botonesPaginador();
        });
    })
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if (paso === 1) {
        if(paginaAnterior) paginaAnterior.classList.add('ocultar');
        if(paginaSiguiente) paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        if(paginaAnterior) paginaAnterior.classList.remove('ocultar');
        if(paginaSiguiente) paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    } else {
        if(paginaAnterior) paginaAnterior.classList.remove('ocultar');
        if(paginaSiguiente) paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    if(paginaSiguiente) {
        paginaSiguiente.addEventListener('click', function () {
            if (paso >= pasoFinal) return;
            paso++;
            botonesPaginador();
        });
    }
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    if(paginaAnterior) {
        paginaAnterior.addEventListener('click', function () {
            if (paso <= pasoInicial) return;
            paso--;
            botonesPaginador();
        });
    }
}

async function consultarAPI() {
    const barberia_id = document.querySelector('#barberia_id')?.value;
    
    if (!barberia_id) {
        console.error('No hay barbería seleccionada');
        return;
    }

    try {
        const url = `${location.origin}/api/servicios?barberia_id=${barberia_id}`;
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log('Error al consultar API:', error);
    }
}

function mostrarServicios(servicios) {
    const contenedor = document.querySelector('#servicios');
    if(!contenedor) return;
    
    contenedor.innerHTML = '';
    
    servicios.forEach(servicio => {
        const { id, nombre, precio, descripcion = '', duracion = 30, imagen = '' } = servicio;
        
        // Crear tarjeta de servicio
        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio-cita');
        servicioDiv.dataset.idServicio = id;
        
        // Usar onclick en lugar de asignar la función directamente
        servicioDiv.addEventListener('click', function() {
            seleccionarServicio(servicio);
        });
        
        // Imagen del servicio
        let imagenHTML = '';
        if (imagen) {
            imagenHTML = `<img src="/uploads/servicios/${imagen}" 
                             alt="${nombre}"
                             onerror="this.onerror=null; this.src='/build/img/servicio-default.jpg';">`;
        } else {
            imagenHTML = `<img src="/build/img/servicio-default.jpg" alt="${nombre}">`;
        }
        
        // Contenido de la tarjeta
        servicioDiv.innerHTML = `
            <div class="servicio-imagen-cita">
                ${imagenHTML}
            </div>
            <div class="servicio-info-cita">
                <h3>${nombre}</h3>
                ${descripcion ? `<p class="descripcion-servicio">${descripcion}</p>` : ''}
                <p class="precio-servicio">$${parseFloat(precio).toFixed(2)}</p>
                <p class="duracion-servicio">
                    <i class="fas fa-clock"></i> ${duracion} minutos
                </p>
            </div>
        `;
        
        contenedor.appendChild(servicioDiv);
    });
}

function seleccionarServicio(servicio) {
    if(!servicio || !servicio.id) return;
    
    const { id } = servicio;
    const { servicios } = cita;
    
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);
    if(!divServicio) return;
    
    // Verificar si el servicio ya está seleccionado
    const index = servicios.findIndex(s => s.id === id);
    
    if(index > -1) {
        // Eliminarlo
        cita.servicios.splice(index, 1);
        divServicio.classList.remove('seleccionado');
    } else {
        // Agregarlo
        cita.servicios.push(servicio);
        divServicio.classList.add('seleccionado');
    }
    
    console.log('Cita actual:', cita);
}

function idCliente() {
    const idInput = document.querySelector('#id');
    if(idInput) {
        cita.id = idInput.value;
    }
}

function nombreCliente() {
    const nombreInput = document.querySelector('#nombre');
    if(nombreInput) {
        cita.nombre = nombreInput.value;
    }
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    if(!inputFecha) return;
    
    inputFecha.addEventListener('input', function (e) {
        const fechaValue = e.target.value;
        if(!fechaValue) return;
        
        const dia = new Date(fechaValue).getUTCDay();
        if ([4, 0].includes(dia)) {
            e.target.value = '';
            mostrarAlerta('Jueves y Domingos No Disponibles', 'error', '.formulario');
        } else {
            cita.fecha = fechaValue;
        }
    });
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    if(!inputHora) return;
    
    inputHora.addEventListener('input', function (e) {
        const horaCita = e.target.value;
        if(!horaCita) return;
        
        const hora = parseInt(horaCita.split(":")[0]);
        if (hora < 9 || hora > 19) {
            e.target.value = '';
            mostrarAlerta('Hora No Válida', 'error', '.formulario');
        } else {
            cita.hora = horaCita;
        }
    });
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    // Previene que se generen más de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if (alertaPrevia) {
        alertaPrevia.remove();
    }

    // Crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    if(referencia) {
        referencia.appendChild(alerta);

        if (desaparece) {
            // Eliminar la alerta después de 3 segundos
            setTimeout(() => {
                alerta.remove();
            }, 3000);
        }
    }
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');
    if(!resumen) return;
    
    const barberia_id = document.querySelector('#barberia_id')?.value;

    // Limpiar el contenido de Resumen
    resumen.innerHTML = '';

    // Agregar barberia_id al objeto cita
    if(barberia_id) {
        cita.barberia_id = barberia_id;
    }

    // Validar que todos los campos requeridos estén completos
    if (!cita.nombre || !cita.fecha || !cita.hora || cita.servicios.length === 0 || !cita.barberia_id) {
        const errores = [];
        if(!cita.nombre) errores.push('Nombre del cliente');
        if(!cita.fecha) errores.push('Fecha');
        if(!cita.hora) errores.push('Hora');
        if(cita.servicios.length === 0) errores.push('Al menos un servicio');
        if(!cita.barberia_id) errores.push('Barbería');
        
        mostrarAlerta(`Faltan datos: ${errores.join(', ')}`, 'error', '.contenido-resumen', false);
        return;
    }

    const { nombre, fecha, hora, servicios } = cita;

    // Heading para Servicios en resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach(servicio => {
        const { id, precio, nombre: nombreServicio, descripcion = '', duracion = 30, imagen = '' } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombreServicio;
        
        if(descripcion) {
            const descripcionElem = document.createElement('P');
            descripcionElem.textContent = descripcion;
            descripcionElem.style.fontSize = '1.4rem';
            descripcionElem.style.color = 'rgba(255, 255, 255, 0.7)';
            descripcionElem.style.marginTop = '0.5rem';
            contenedorServicio.appendChild(descripcionElem);
        }

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${parseFloat(precio).toFixed(2)}`;
        
        const duracionServicio = document.createElement('P');
        duracionServicio.innerHTML = `<span>Duración:</span> ${duracion} minutos`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);
        contenedorServicio.appendChild(duracionServicio);

        resumen.appendChild(contenedorServicio);
    })

    // Heading para Citas en resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`

    // Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, mes, dia));

    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    // Calcular total
    const total = servicios.reduce((sum, servicio) => sum + parseFloat(servicio.precio), 0);
    const totalElem = document.createElement('P');
    totalElem.innerHTML = `<span>Total:</span> $${total.toFixed(2)}`;
    totalElem.style.fontSize = '2rem';
    totalElem.style.color = '#4caf50';
    totalElem.style.fontWeight = 'bold';
    totalElem.style.marginTop = '1rem';

    // Botón para Crear una Cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(totalElem);
    resumen.appendChild(botonReservar);
}

async function reservarCita() {
    const { nombre, fecha, hora, servicios, id } = cita;
    const barberia_id = document.querySelector('#barberia_id')?.value;

    // Validar que todos los campos estén completos
    if (!barberia_id || !fecha || !hora || servicios.length === 0) {
        mostrarAlerta('Faltan datos de Servicios, Fecha u Hora', 'error', '.contenido-resumen', false);
        return;
    }

    const idServicios = servicios.map(servicio => servicio.id);

    const datos = new FormData();
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    // NO enviar usuarioId - se obtendrá de la sesión en el servidor
    datos.append('servicios', idServicios.join(','));
    datos.append('barberia_id', barberia_id);

    console.log('Enviando datos para crear cita:', {
        fecha, hora, servicios: idServicios, barberia_id
    });

    try {
        const url = `${location.origin}/api/citas`;
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        // Obtener el texto de la respuesta primero
        const responseText = await respuesta.text();
        console.log('Respuesta del servidor (raw):', responseText.substring(0, 500));

        // Intentar parsear como JSON
        let resultado;
        try {
            resultado = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Error parseando JSON:', parseError);
            console.error('Respuesta completa:', responseText);
            
            // Si la respuesta no es JSON válido pero la cita se creó (código 200)
            if (respuesta.ok) {
                // Asumir éxito y redirigir
                await mostrarExitoYCargarCitas();
                return;
            }
            
            throw new Error('El servidor devolvió una respuesta no válida. Verifica los logs.');
        }

        console.log('Respuesta del servidor (parsed):', resultado);
        
        if (resultado.resultado) {
            await mostrarExitoYCargarCitas();
        } else {
            throw new Error(resultado.error || 'Error al crear cita');
        }

    } catch (error) {
        console.error('Error completo:', error);
        Swal.fire({
            icon: "error",
            title: "Error",
            html: `Hubo un error al guardar la cita.<br><br>
                   <small style="font-size: 0.8em; color: #666;">
                   Detalles: ${error.message}<br>
                   Nota: La cita puede haberse creado exitosamente. 
                   Verifica en "Mis Citas".
                   </small>`,
            confirmButtonText: 'Entendido'
        });
    }
}

// Función auxiliar para mostrar éxito y recargar
async function mostrarExitoYCargarCitas() {
    await Swal.fire({
        icon: "success",
        title: "Cita Creada",
        text: "Tu cita fue creada correctamente",
        confirmButtonText: 'Ver Mis Citas',
        showCancelButton: true,
        cancelButtonText: 'Crear Otra Cita'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir a la página de mis citas
            window.location.href = '/cita?exito=1';
        } else {
            // Recargar la página para crear otra cita
            window.location.reload();
        }
    });
}