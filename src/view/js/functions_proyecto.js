function numero_pagina_proyecto(pagina) {
    document.getElementById('pagina_proyecto').value = pagina;
    listar_proyectosOrdenados();
}

async function listar_proyectosOrdenados() {
    try {
        mostrarPopupCarga();
        let pagina = document.getElementById('pagina_proyecto').value;
        let cantidad_mostrar = document.getElementById('cantidad_mostrar_proyecto').value;
        let busqueda_tabla_nombre = document.getElementById('busqueda_tabla_nombre').value;
        let busqueda_tabla_cliente = document.getElementById('busqueda_tabla_cliente').value;
        let busqueda_tabla_estado = document.getElementById('busqueda_tabla_estado').value;
        
        document.getElementById('filtro_nombre').value = busqueda_tabla_nombre;
        document.getElementById('filtro_cliente').value = busqueda_tabla_cliente;
        document.getElementById('filtro_estado_proyecto').value = busqueda_tabla_estado;

        const formData = new FormData();
        formData.append('pagina', pagina);
        formData.append('cantidad_mostrar', cantidad_mostrar);
        formData.append('busqueda_tabla_nombre', busqueda_tabla_nombre);
        formData.append('busqueda_tabla_cliente', busqueda_tabla_cliente);
        formData.append('busqueda_tabla_estado', busqueda_tabla_estado);
        formData.append('sesion', session_session);
        formData.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/Proyecto.php?tipo=listar_proyectos_ordenados_tabla', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        let json = await respuesta.json();
        document.getElementById('tablas_proyectos').innerHTML = `<table id="" class="table dt-responsive" width="100%">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Nombre</th>
                            <th>Cliente</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Presupuesto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_proyecto">
                    </tbody>
                </table>`;
        document.querySelector('#modals_editar_proyecto').innerHTML = ``;
        
        if (json.status) {
            let datos = json.contenido;
            datos.forEach(item => {
                generarfilastabla_proyecto(item);
            });
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            document.getElementById('tablas_proyectos').innerHTML = `No se encontraron resultados`;
        }
        
        let paginacion = generar_paginacion(json.total, cantidad_mostrar);
        let texto_paginacion = generar_texto_paginacion(json.total, cantidad_mostrar);
        document.getElementById('texto_paginacion_tabla_proyecto').innerHTML = texto_paginacion;
        document.getElementById('lista_paginacion_tabla_proyecto').innerHTML = paginacion;
        
    } catch (e) {
        console.log("Error al cargar proyectos: " + e);
    } finally {
        ocultarPopupCarga();
    }
}

function generarfilastabla_proyecto(item) {
    let cont = 1;
    $(".filas_tabla_proyecto").each(function () {
        cont++;
    })
    
    let nueva_fila = document.createElement("tr");
    nueva_fila.id = "fila_proyecto" + item.id;
    nueva_fila.className = "filas_tabla_proyecto";

    // Formatear fechas
    let fecha_inicio = new Date(item.fecha_inicio).toLocaleDateString('es-ES');
    let fecha_fin = item.fecha_fin ? new Date(item.fecha_fin).toLocaleDateString('es-ES') : '-';
    let presupuesto = item.presupuesto ? 'S/ ' + parseFloat(item.presupuesto).toFixed(2) : '-';

    nueva_fila.innerHTML = `
        <th>${cont}</th>
        <td>${item.nombre}</td>
        <td>${item.razon_social || '-'}</td>
        <td>${fecha_inicio}</td>
        <td>${fecha_fin}</td>
        <td>${presupuesto}</td>
        <td>${item.estado.toUpperCase()}</td>
        <td>${item.options}</td>
    `;
    
    document.querySelector('#modals_editar_proyecto').innerHTML += `
        <div class="modal fade modal_editar_proyecto${item.id}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title h4">Actualizar datos del proyecto</h5>
                        <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <form class="form-horizontal" id="frmActualizarProyecto${item.id}">
                                <div class="form-group row mb-2">
                                    <label for="nombre_proyecto${item.id}" class="col-3 col-form-label">Nombre</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="nombre_proyecto${item.id}" name="nombre" value="${item.nombre}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="descripcion_proyecto${item.id}" class="col-3 col-form-label">Descripción</label>
                                    <div class="col-9">
                                        <textarea class="form-control" id="descripcion_proyecto${item.id}" name="descripcion" rows="3">${item.descripcion || ''}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="id_cliente_proyecto${item.id}" class="col-3 col-form-label">Cliente</label>
                                    <div class="col-9">
                                        <select name="id_cliente" id="id_cliente_proyecto${item.id}" class="form-control">
                                            <option value="">Seleccionar cliente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="fecha_inicio_proyecto${item.id}" class="col-3 col-form-label">Fecha Inicio</label>
                                    <div class="col-9">
                                        <input type="date" class="form-control" id="fecha_inicio_proyecto${item.id}" name="fecha_inicio" value="${item.fecha_inicio}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="fecha_fin_proyecto${item.id}" class="col-3 col-form-label">Fecha Fin</label>
                                    <div class="col-9">
                                        <input type="date" class="form-control" id="fecha_fin_proyecto${item.id}" name="fecha_fin" value="${item.fecha_fin || ''}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="presupuesto_proyecto${item.id}" class="col-3 col-form-label">Presupuesto (S/)</label>
                                    <div class="col-9">
                                        <input type="number" step="0.01" class="form-control" id="presupuesto_proyecto${item.id}" name="presupuesto" value="${item.presupuesto || ''}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="estado_proyecto${item.id}" class="col-3 col-form-label">Estado</label>
                                    <div class="col-9">
                                        <select name="estado" id="estado_proyecto${item.id}" class="form-control">
                                            <option value="activo" ${item.estado == 'activo' ? 'selected' : ''}>ACTIVO</option>
                                            <option value="inactivo" ${item.estado == 'inactivo' ? 'selected' : ''}>INACTIVO</option>
                                            <option value="completado" ${item.estado == 'completado' ? 'selected' : ''}>COMPLETADO</option>
                                            <option value="cancelado" ${item.estado == 'cancelado' ? 'selected' : ''}>CANCELADO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-0 justify-content-end row text-center">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success waves-effect waves-light" onclick="actualizarProyecto(${item.id})">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        
    // Cargar clientes en el select
    cargarClientesParaSelect(item.id_cliente, 'id_cliente_proyecto' + item.id);
    document.querySelector('#contenido_tabla_proyecto').appendChild(nueva_fila);
}

async function cargarClientesParaSelect(cliente_seleccionado = '', select_id) {
    try {
        const formData = new FormData();
        formData.append('sesion', session_session);
        formData.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/Cliente.php?tipo=listarTodosClientes', {
            method: 'POST',
            body: formData
        });
        
        let json = await respuesta.json();
        if (json.status) {
            let select = document.getElementById(select_id);
            select.innerHTML = '<option value="">Seleccionar cliente</option>';
            json.contenido.forEach(cliente => {
                let selected = cliente.id == cliente_seleccionado ? 'selected' : '';
                select.innerHTML += `<option value="${cliente.id}" ${selected}>${cliente.razon_social}</option>`;
            });
        }
    } catch (e) {
        console.log("Error al cargar clientes: " + e);
    }
}

async function registrar_proyecto() {
    let id_cliente = document.getElementById('id_cliente').value;
    let nombre = document.querySelector('#nombre').value;
    let descripcion = document.querySelector('#descripcion').value;
    let fecha_inicio = document.querySelector('#fecha_inicio').value;
    let fecha_fin = document.querySelector('#fecha_fin').value;
    let presupuesto = document.querySelector('#presupuesto').value;
    let estado = document.querySelector('#estado').value;
    
    if (id_cliente == "" || nombre == "" || fecha_inicio == "" || estado == "") {
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'Campos obligatorios vacíos...',
            confirmButtonClass: 'btn btn-confirm mt-2',
            footer: ''
        })
        return;
    }
    
    try {
        const datos = new FormData(document.getElementById('frmRegistrarProyecto'));
        datos.append('sesion', session_session);
        datos.append('token', token_token);
        datos.append('usuario_registro', '1'); // Aquí deberías usar el ID del usuario logueado
        
        let respuesta = await fetch(base_url_server + 'src/control/Proyecto.php?tipo=registrar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });
        
        json = await respuesta.json();
        if (json.status) {
            document.getElementById("frmRegistrarProyecto").reset();
            Swal.fire({
                type: 'success',
                title: 'Registro',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_proyectosOrdenados();
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            })
        }
    } catch (e) {
        console.log("Error al registrar proyecto: " + e);
    }
}

async function actualizarProyecto(id) {
    let id_cliente = document.getElementById('id_cliente_proyecto' + id).value;
    let nombre = document.querySelector('#nombre_proyecto' + id).value;
    let descripcion = document.querySelector('#descripcion_proyecto' + id).value;
    let fecha_inicio = document.querySelector('#fecha_inicio_proyecto' + id).value;
    let fecha_fin = document.querySelector('#fecha_fin_proyecto' + id).value;
    let presupuesto = document.querySelector('#presupuesto_proyecto' + id).value;
    let estado = document.querySelector('#estado_proyecto' + id).value;
    
    if (id_cliente == "" || nombre == "" || fecha_inicio == "" || estado == "") {
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'Campos obligatorios vacíos...',
            confirmButtonClass: 'btn btn-confirm mt-2',
            footer: '',
            timer: 1000
        })
        return;
    }
    
    const formulario = document.getElementById('frmActualizarProyecto' + id);
    const datos = new FormData(formulario);
    datos.append('data', id);
    datos.append('sesion', session_session);
    datos.append('token', token_token);
    
    try {
        let respuesta = await fetch(base_url_server + 'src/control/Proyecto.php?tipo=actualizar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });
        
        json = await respuesta.json();
        if (json.status) {
            $('.modal_editar_proyecto' + id).modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Actualizar',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_proyectosOrdenados();
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            })
        }
    } catch (e) {
        console.log("Error al actualizar proyecto: " + e);
    }
}

// Cargar clientes al cargar la página de nuevo proyecto
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('id_cliente')) {
        cargarClientesParaSelect('', 'id_cliente');
    }
});