function numero_pagina_token_api(pagina) {
    document.getElementById('pagina_token_api').value = pagina;
    listar_tokensApiOrdenados();
}

async function listar_tokensApiOrdenados() {
    try {
        mostrarPopupCarga();
        let pagina = document.getElementById('pagina_token_api').value;
        let cantidad_mostrar = document.getElementById('cantidad_mostrar_token_api').value;
        let busqueda_tabla_cliente_api = document.getElementById('busqueda_tabla_cliente_api').value;
        let busqueda_tabla_estado = document.getElementById('busqueda_tabla_estado').value;
        
        document.getElementById('filtro_cliente_api').value = busqueda_tabla_cliente_api;
        document.getElementById('filtro_estado_token_api').value = busqueda_tabla_estado;

        const formData = new FormData();
        formData.append('pagina', pagina);
        formData.append('cantidad_mostrar', cantidad_mostrar);
        formData.append('busqueda_tabla_cliente_api', busqueda_tabla_cliente_api);
        formData.append('busqueda_tabla_estado', busqueda_tabla_estado);
        formData.append('sesion', session_session);
        formData.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/TokenApi.php?tipo=listar_tokens_api_ordenados_tabla', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        let json = await respuesta.json();
        document.getElementById('tablas_tokens_api').innerHTML = `<table id="" class="table dt-responsive" width="100%">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Token</th>
                            <th>Cliente API</th>
                            <th>Cliente</th>
                            <th>Fecha Creación</th>
                            <th>Fecha Expiración</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_token_api">
                    </tbody>
                </table>`;
        document.querySelector('#modals_editar_token_api').innerHTML = ``;
        
        if (json.status) {
            let datos = json.contenido;
            datos.forEach(item => {
                generarfilastabla_token_api(item);
            });
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            document.getElementById('tablas_tokens_api').innerHTML = `No se encontraron resultados`;
        }
        
        let paginacion = generar_paginacion(json.total, cantidad_mostrar);
        let texto_paginacion = generar_texto_paginacion(json.total, cantidad_mostrar);
        document.getElementById('texto_paginacion_tabla_token_api').innerHTML = texto_paginacion;
        document.getElementById('lista_paginacion_tabla_token_api').innerHTML = paginacion;
        
    } catch (e) {
        console.log("Error al cargar tokens API: " + e);
    } finally {
        ocultarPopupCarga();
    }
}

function generarfilastabla_token_api(item) {
    let cont = 1;
    $(".filas_tabla_token_api").each(function () {
        cont++;
    })
    
    let nueva_fila = document.createElement("tr");
    nueva_fila.id = "fila_token_api" + item.id;
    nueva_fila.className = "filas_tabla_token_api";

    // Formatear fechas
    let fecha_creacion = new Date(item.fecha_creacion).toLocaleDateString('es-ES');
    let fecha_expiracion = new Date(item.fecha_expiracion).toLocaleDateString('es-ES');
    
    // Acortar token para visualización
    let token_corto = item.token.length > 20 ? item.token.substring(0, 20) + '...' : item.token;

    nueva_fila.innerHTML = `
        <th>${cont}</th>
        <td title="${item.token}">${token_corto}</td>
        <td>${item.cliente_api_nombre || '-'}</td>
        <td>${item.razon_social || '-'}</td>
        <td>${fecha_creacion}</td>
        <td>${fecha_expiracion}</td>
        <td>${item.estado == 1 ? 'ACTIVO' : 'INACTIVO'}</td>
        <td>${item.options}</td>
    `;
    
    document.querySelector('#modals_editar_token_api').innerHTML += `
        <div class="modal fade modal_editar_token_api${item.id}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title h4">Actualizar Token API</h5>
                        <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <form class="form-horizontal" id="frmActualizarTokenApi${item.id}">
                                <div class="form-group row mb-2">
                                    <label for="token_api${item.id}" class="col-3 col-form-label">Token</label>
                                    <div class="col-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="token_api${item.id}" name="token" value="${item.token}" readonly>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" onclick="copiarToken('token_api${item.id}')">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-primary" onclick="generarNuevoToken(${item.id})">
                                                    <i class="fa fa-refresh"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="id_cliente_api_token${item.id}" class="col-3 col-form-label">Cliente API</label>
                                    <div class="col-9">
                                        <select name="id_cliente_api" id="id_cliente_api_token${item.id}" class="form-control">
                                            <option value="">Seleccionar cliente API</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="fecha_expiracion_token${item.id}" class="col-3 col-form-label">Fecha Expiración</label>
                                    <div class="col-9">
                                        <input type="datetime-local" class="form-control" id="fecha_expiracion_token${item.id}" name="fecha_expiracion" value="${item.fecha_expiracion.replace(' ', 'T')}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="permisos_token${item.id}" class="col-3 col-form-label">Permisos</label>
                                    <div class="col-9">
                                        <textarea class="form-control" id="permisos_token${item.id}" name="permisos" rows="3" placeholder="JSON de permisos">${item.permisos || ''}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="estado_token${item.id}" class="col-3 col-form-label">Estado</label>
                                    <div class="col-9">
                                        <select name="estado" id="estado_token${item.id}" class="form-control">
                                            <option value="1" ${item.estado == 1 ? 'selected' : ''}>ACTIVO</option>
                                            <option value="0" ${item.estado == 0 ? 'selected' : ''}>INACTIVO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-0 justify-content-end row text-center">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success waves-effect waves-light" onclick="actualizarTokenApi(${item.id})">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        
    // Cargar clientes API en el select
    cargarClientesApiParaSelect(item.id_cliente_api, 'id_cliente_api_token' + item.id);
    document.querySelector('#contenido_tabla_token_api').appendChild(nueva_fila);
}

async function cargarClientesApiParaSelect(cliente_api_seleccionado = '', select_id) {
    try {
        const formData = new FormData();
        formData.append('sesion', session_session);
        formData.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/ClienteApi.php?tipo=listarTodosClientesApi', {
            method: 'POST',
            body: formData
        });
        
        let json = await respuesta.json();
        if (json.status) {
            let select = document.getElementById(select_id);
            select.innerHTML = '<option value="">Seleccionar cliente API</option>';
            json.contenido.forEach(cliente_api => {
                let selected = cliente_api.id == cliente_api_seleccionado ? 'selected' : '';
                select.innerHTML += `<option value="${cliente_api.id}" ${selected}>${cliente_api.nombre} - ${cliente_api.razon_social}</option>`;
            });
        }
    } catch (e) {
        console.log("Error al cargar clientes API: " + e);
    }
}

async function registrar_token_api() {
    let id_cliente_api = document.getElementById('id_cliente_api').value;
    let fecha_expiracion = document.querySelector('#fecha_expiracion').value;
    let permisos = document.querySelector('#permisos').value;
    
    if (id_cliente_api == "" || fecha_expiracion == "") {
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
        const datos = new FormData(document.getElementById('frmRegistrarTokenApi'));
        datos.append('sesion', session_session);
        datos.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/TokenApi.php?tipo=registrar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });
        
        json = await respuesta.json();
        if (json.status) {
            document.getElementById("frmRegistrarTokenApi").reset();
            Swal.fire({
                type: 'success',
                title: 'Registro Exitoso',
                html: `Token API registrado exitosamente<br><br>
                      <strong>Token Generado:</strong><br>
                      <code style="background: #f8f9fa; padding: 5px; border-radius: 3px; word-break: break-all;">${json.token_generado}</code><br><br>
                      <small>Guarde este token en un lugar seguro, no podrá verlo nuevamente.</small>`,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                width: 600
            });
            listar_tokensApiOrdenados();
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
        console.log("Error al registrar token API: " + e);
    }
}

async function actualizarTokenApi(id) {
    let id_cliente_api = document.getElementById('id_cliente_api_token' + id).value;
    let token = document.querySelector('#token_api' + id).value;
    let fecha_expiracion = document.querySelector('#fecha_expiracion_token' + id).value;
    let permisos = document.querySelector('#permisos_token' + id).value;
    let estado = document.querySelector('#estado_token' + id).value;
    
    if (id_cliente_api == "" || token == "" || fecha_expiracion == "" || estado == "") {
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
    
    const formulario = document.getElementById('frmActualizarTokenApi' + id);
    const datos = new FormData(formulario);
    datos.append('data', id);
    datos.append('sesion', session_session);
    datos.append('token', token_token);
    
    try {
        let respuesta = await fetch(base_url_server + 'src/control/TokenApi.php?tipo=actualizar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });
        
        json = await respuesta.json();
        if (json.status) {
            $('.modal_editar_token_api' + id).modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Actualizar',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_tokensApiOrdenados();
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
        console.log("Error al actualizar token API: " + e);
    }
}

async function generarNuevoToken(id) {
    try {
        const formData = new FormData();
        formData.append('sesion', session_session);
        formData.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/TokenApi.php?tipo=generar_token', {
            method: 'POST',
            body: formData
        });
        
        let json = await respuesta.json();
        if (json.status) {
            document.getElementById('token_api' + id).value = json.token;
            Swal.fire({
                type: 'success',
                title: 'Token Generado',
                text: 'Nuevo token generado exitosamente',
                timer: 1000
            });
        }
    } catch (e) {
        console.log("Error al generar token: " + e);
    }
}

function copiarToken(inputId) {
    let input = document.getElementById(inputId);
    input.select();
    document.execCommand('copy');
    Swal.fire({
        type: 'success',
        title: 'Copiado',
        text: 'Token copiado al portapapeles',
        timer: 1000
    });
}

// Cargar clientes API al cargar la página de nuevo token
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('id_cliente_api')) {
        cargarClientesApiParaSelect('', 'id_cliente_api');
    }
    if (document.getElementById('busqueda_tabla_cliente_api')) {
        cargarClientesApiParaSelect('', 'busqueda_tabla_cliente_api');
    }
});