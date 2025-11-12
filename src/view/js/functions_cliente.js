function numero_pagina_cliente(pagina) {
    document.getElementById('pagina_cliente').value = pagina;
    listar_clientesOrdenados();
}

async function listar_clientesOrdenados() {
    try {
        mostrarPopupCarga();
        let pagina = document.getElementById('pagina_cliente').value;
        let cantidad_mostrar = document.getElementById('cantidad_mostrar_cliente').value;
        let busqueda_tabla_ruc = document.getElementById('busqueda_tabla_ruc').value;
        let busqueda_tabla_razon_social = document.getElementById('busqueda_tabla_razon_social').value;
        let busqueda_tabla_estado = document.getElementById('busqueda_tabla_estado').value;
        
        document.getElementById('filtro_ruc').value = busqueda_tabla_ruc;
        document.getElementById('filtro_razon_social').value = busqueda_tabla_razon_social;
        document.getElementById('filtro_estado_cliente').value = busqueda_tabla_estado;

        const formData = new FormData();
        formData.append('pagina', pagina);
        formData.append('cantidad_mostrar', cantidad_mostrar);
        formData.append('busqueda_tabla_ruc', busqueda_tabla_ruc);
        formData.append('busqueda_tabla_razon_social', busqueda_tabla_razon_social);
        formData.append('busqueda_tabla_estado', busqueda_tabla_estado);
        formData.append('sesion', session_session);
        formData.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/Cliente.php?tipo=listar_clientes_ordenados_tabla', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        let json = await respuesta.json();
        document.getElementById('tablas_clientes').innerHTML = `<table id="" class="table dt-responsive" width="100%">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>RUC</th>
                            <th>Razón Social</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_cliente">
                    </tbody>
                </table>`;
        document.querySelector('#modals_editar_cliente').innerHTML = ``;
        
        if (json.status) {
            let datos = json.contenido;
            datos.forEach(item => {
                generarfilastabla_cliente(item);
            });
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            document.getElementById('tablas_clientes').innerHTML = `No se encontraron resultados`;
        }
        
        let paginacion = generar_paginacion(json.total, cantidad_mostrar);
        let texto_paginacion = generar_texto_paginacion(json.total, cantidad_mostrar);
        document.getElementById('texto_paginacion_tabla_cliente').innerHTML = texto_paginacion;
        document.getElementById('lista_paginacion_tabla_cliente').innerHTML = paginacion;
        
    } catch (e) {
        console.log("Error al cargar clientes: " + e);
    } finally {
        ocultarPopupCarga();
    }
}

function generarfilastabla_cliente(item) {
    let cont = 1;
    $(".filas_tabla_cliente").each(function () {
        cont++;
    })
    
    let nueva_fila = document.createElement("tr");
    nueva_fila.id = "fila_cliente" + item.id;
    nueva_fila.className = "filas_tabla_cliente";

    activo_si = "";
    activo_no = "";
    if (item.estado == 1) {
        estado = "ACTIVO";
        activo_si = "selected";
    } else {
        estado = "INACTIVO";
        activo_no = "selected";
    }

    nueva_fila.innerHTML = `
        <th>${cont}</th>
        <td>${item.ruc}</td>
        <td>${item.razon_social}</td>
        <td>${item.contacto || '-'}</td>
        <td>${item.telefono}</td>
        <td>${estado}</td>
        <td>${item.options}</td>
    `;
    
    document.querySelector('#modals_editar_cliente').innerHTML += `
        <div class="modal fade modal_editar${item.id}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title h4">Actualizar datos del cliente</h5>
                        <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <form class="form-horizontal" id="frmActualizarCliente${item.id}">
                                <div class="form-group row mb-2">
                                    <label for="ruc${item.id}" class="col-3 col-form-label">RUC</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="ruc${item.id}" name="ruc" value="${item.ruc}" maxlength="11">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="razon_social${item.id}" class="col-3 col-form-label">Razón Social</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="razon_social${item.id}" name="razon_social" value="${item.razon_social}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="direccion${item.id}" class="col-3 col-form-label">Dirección</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="direccion${item.id}" name="direccion" value="${item.direccion || ''}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="telefono${item.id}" class="col-3 col-form-label">Teléfono</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="telefono${item.id}" name="telefono" value="${item.telefono}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="correo${item.id}" class="col-3 col-form-label">Correo</label>
                                    <div class="col-9">
                                        <input type="email" class="form-control" id="correo${item.id}" name="correo" value="${item.correo || ''}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="contacto${item.id}" class="col-3 col-form-label">Contacto</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="contacto${item.id}" name="contacto" value="${item.contacto || ''}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="estado${item.id}" class="col-3 col-form-label">Estado</label>
                                    <div class="col-9">
                                        <select name="estado" id="estado${item.id}" class="form-control">
                                            <option value=""></option>
                                            <option value="1" ${activo_si}>ACTIVO</option>
                                            <option value="0" ${activo_no}>INACTIVO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-0 justify-content-end row text-center">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success waves-effect waves-light" onclick="actualizarCliente(${item.id})">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        
    document.querySelector('#contenido_tabla_cliente').appendChild(nueva_fila);
}

async function registrar_cliente() {
    let ruc = document.getElementById('ruc').value;
    let razon_social = document.querySelector('#razon_social').value;
    let direccion = document.querySelector('#direccion').value;
    let telefono = document.querySelector('#telefono').value;
    let correo = document.querySelector('#correo').value;
    let contacto = document.querySelector('#contacto').value;
    
    if (ruc == "" || razon_social == "" || telefono == "") {
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
        const datos = new FormData(frmRegistrarCliente);
        datos.append('sesion', session_session);
        datos.append('token', token_token);
        
        let respuesta = await fetch(base_url_server + 'src/control/Cliente.php?tipo=registrar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });
        
        json = await respuesta.json();
        if (json.status) {
            document.getElementById("frmRegistrarCliente").reset();
            Swal.fire({
                type: 'success',
                title: 'Registro',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_clientesOrdenados();
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
        console.log("Error al registrar cliente: " + e);
    }
}

async function actualizarCliente(id) {
    let ruc = document.getElementById('ruc' + id).value;
    let razon_social = document.querySelector('#razon_social' + id).value;
    let direccion = document.querySelector('#direccion' + id).value;
    let telefono = document.querySelector('#telefono' + id).value;
    let correo = document.querySelector('#correo' + id).value;
    let contacto = document.querySelector('#contacto' + id).value;
    let estado = document.querySelector('#estado' + id).value;

    if (ruc == "" || razon_social == "" || telefono == "" || estado == "") {
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'Campos obligatorios vacíos...',
            confirmButtonClass: 'btn btn-confirm mt-2'
        });
        return;
    }

    try {
        const datos = new FormData();
        datos.append('id', id);
        datos.append('ruc', ruc);
        datos.append('razon_social', razon_social);
        datos.append('direccion', direccion);
        datos.append('telefono', telefono);
        datos.append('correo', correo);
        datos.append('contacto', contacto);
        datos.append('estado', estado);
        datos.append('sesion', session_session);
        datos.append('token', token_token);

        let respuesta = await fetch(base_url_server + 'src/control/Cliente.php?tipo=actualizar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });

        let json = await respuesta.json();
        if (json.status) {
            Swal.fire({
                type: 'success',
                title: 'Actualización',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                timer: 1000
            });
            listar_clientesOrdenados();
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                timer: 1000
            });
        }
    } catch (e) {
        console.log("Error al actualizar cliente: " + e);
    }
}
