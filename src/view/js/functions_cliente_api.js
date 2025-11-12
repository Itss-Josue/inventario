function numero_pagina_cliente_api(pagina) {
    document.getElementById('pagina_cliente_api').value = pagina;
    listar_clientesApiOrdenados();
}

async function listar_clientesApiOrdenados() {
    try {
        mostrarPopupCarga();
        let pagina = document.getElementById('pagina_cliente_api').value;
        let cantidad_mostrar = document.getElementById('cantidad_mostrar_cliente_api').value;
        let busqueda_tabla_nombre = document.getElementById('busqueda_tabla_nombre').value;
        let busqueda_tabla_cliente = document.getElementById('busqueda_tabla_cliente').value;
        let busqueda_tabla_estado = document.getElementById('busqueda_tabla_estado').value;
        
        document.getElementById('filtro_nombre').value = busqueda_tabla_nombre;
        document.getElementById('filtro_cliente').value = busqueda_tabla_cliente;
        document.getElementById('filtro_estado_cliente_api').value = busqueda_tabla_estado;

        const formData = new FormData();
        formData.append('pagina', pagina);
        formData.append('cantidad_mostrar', cantidad_mostrar);
        formData.append('busqueda_tabla_nombre', busqueda_tabla_nombre);
        formData.append('busqueda_tabla_cliente', busqueda_tabla_cliente);
        formData.append('busqueda_tabla_estado', busqueda_tabla_estado);
        formData.append('sesion', session_session);
        formData.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/ClienteApi.php?tipo=listar_clientes_api_ordenados_tabla', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        let json = await respuesta.json();
        document.getElementById('tablas_clientes_api').innerHTML = `<table id="" class="table dt-responsive" width="100%">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Nombre</th>
                            <th>Cliente</th>
                            <th>Descripción</th>
                            <th>IP Permisos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_cliente_api">
                    </tbody>
                </table>`;
        document.querySelector('#modals_editar_cliente_api').innerHTML = ``;
        
        if (json.status) {
            let datos = json.contenido;
            datos.forEach(item => {
                generarfilastabla_cliente_api(item);
            });
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            document.getElementById('tablas_clientes_api').innerHTML = `No se encontraron resultados`;
        }
        
        let paginacion = generar_paginacion(json.total, cantidad_mostrar);
        let texto_paginacion = generar_texto_paginacion(json.total, cantidad_mostrar);
        document.getElementById('texto_paginacion_tabla_cliente_api').innerHTML = texto_paginacion;
        document.getElementById('lista_paginacion_tabla_cliente_api').innerHTML = paginacion;
        
    } catch (e) {
        console.log("Error al cargar clientes API: " + e);
    } finally {
        ocultarPopupCarga();
    }
}

function generarfilastabla_cliente_api(item) {
    let cont = 1;
    $(".filas_tabla_cliente_api").each(function () {
        cont++;
    })
    
    let nueva_fila = document.createElement("tr");
    nueva_fila.id = "fila_cliente_api" + item.id;
    nueva_fila.className = "filas_tabla_cliente_api";

    // Acortar descripción para visualización
    let descripcion_corta = item.descripcion && item.descripcion.length > 50 ? 
        item.descripcion.substring(0, 50) + '...' : item.descripcion || '-';

    nueva_fila.innerHTML = `
        <th>${cont}</th>
        <td>${item.nombre}</td>
        <td>${item.razon_social || '-'}</td>
        <td title="${item.descripcion || ''}">${descripcion_corta}</td>
        <td>${item.ip_permisos || 'Todas'}</td>
        <td>${item.estado == 1 ? 'ACTIVO' : 'INACTIVO'}</td>
        <td>${item.options}</td>
    `;
    
    document.querySelector('#modals_editar_cliente_api').innerHTML += `
        <div class="modal fade modal_editar_cliente_api${item.id}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title h4">Actualizar Cliente API</h5>
                        <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <form class="form-horizontal" id="frmActualizarClienteApi${item.id}">
                                <div class="form-group row mb-2">
                                    <label for="nombre_cliente_api${item.id}" class="col-3 col-form-label">Nombre</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="nombre_cliente_api${item.id}" name="nombre" value="${item.nombre}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="descripcion_cliente_api${item.id}" class="col-3 col-form-label">Descripción</label>
                                    <div class="col-9">
                                        <textarea class="form-control" id="descripcion_cliente_api${item.id}" name="descripcion" rows="3">${item.descripcion || ''}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="id_cliente_api${item.id}" class="col-3 col-form-label">Cliente</label>
                                    <div class="col-9">
                                        <select name="id_cliente" id="id_cliente_api${item.id}" class="form-control">
                                            <option value="">Seleccionar cliente</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="ip_permisos_cliente_api${item.id}" class="col-3 col-form-label">IP Permisos</label>
                                    <div class="col-9">
                                        <textarea class="form-control" id="ip_permisos_cliente_api${item.id}" name="ip_permisos" rows="2" placeholder="Ejemplo: 192.168.1.1, 10.0.0.0/24 (dejar vacío para permitir todas)">${item.ip_permisos || ''}</textarea>
                                        <small class="form-text text-muted">Separar múltiples IPs con comas. Ejemplo: 192.168.1.1, 10.0.0.0/24</small>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="estado_cliente_api${item.id}" class="col-3 col-form-label">Estado</label>
                                    <div class="col-9">
                                        <select name="estado" id="estado_cliente_api${item.id}" class="form-control">
                                            <option value="1" ${item.estado == 1 ? 'selected' : ''}>ACTIVO</option>
                                            <option value="0" ${item.estado == 0 ? 'selected' : ''}>INACTIVO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-0 justify-content-end row text-center">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success waves-effect waves-light" onclick="actualizarClienteApi(${item.id})">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        
    // Cargar clientes en el select
    cargarClientesParaSelectClienteApi(item.id_cliente, 'id_cliente_api' + item.id);
    document.querySelector('#contenido_tabla_cliente_api').appendChild(nueva_fila);
}

async function cargarClientesParaSelectClienteApi(cliente_seleccionado = '', select_id) {
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

async function registrar_cliente_api() {
    let id_cliente = document.getElementById('id_cliente').value;
    let nombre = document.querySelector('#nombre').value;
    let descripcion = document.querySelector('#descripcion').value;
    let ip_permisos = document.querySelector('#ip_permisos').value;
    
    if (id_cliente == "" || nombre == "") {
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
        const datos = new FormData(document.getElementById('frmRegistrarClienteApi'));
        datos.append('sesion', session_session);
        datos.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/ClienteApi.php?tipo=registrar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });
        
        json = await respuesta.json();
        if (json.status) {
            document.getElementById("frmRegistrarClienteApi").reset();
            Swal.fire({
                type: 'success',
                title: 'Registro',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_clientesApiOrdenados();
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
        console.log("Error al registrar cliente API: " + e);
    }
}

async function actualizarClienteApi(id) {
    let id_cliente = document.getElementById('id_cliente_api' + id).value;
    let nombre = document.querySelector('#nombre_cliente_api' + id).value;
    let descripcion = document.querySelector('#descripcion_cliente_api' + id).value;
    let ip_permisos = document.querySelector('#ip_permisos_cliente_api' + id).value;
    let estado = document.querySelector('#estado_cliente_api' + id).value;
    
    if (id_cliente == "" || nombre == "" || estado == "") {
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
    
    const formulario = document.getElementById('frmActualizarClienteApi' + id);
    const datos = new FormData(formulario);
    datos.append('data', id);
    datos.append('sesion', session_session);
    datos.append('token', token_token);
    
    try {
        let respuesta = await fetch(base_url_server + 'src/control/ClienteApi.php?tipo=actualizar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });
        
        json = await respuesta.json();
        if (json.status) {
            $('.modal_editar_cliente_api' + id).modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Actualizar',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_clientesApiOrdenados();
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
        console.log("Error al actualizar cliente API: " + e);
    }
}

// Cargar clientes al cargar la página de nuevo cliente API
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('id_cliente')) {
        cargarClientesParaSelectClienteApi('', 'id_cliente');
    }
    if (document.getElementById('busqueda_tabla_cliente')) {
        cargarClientesParaSelectClienteApi('', 'busqueda_tabla_cliente');
    }
});