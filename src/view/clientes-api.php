<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Gestión de Clientes API</h4>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <a href="<?php echo BASE_URL;?>nuevo-cliente-api" class="btn btn-danger waves-effect waves-light">
                            <i class="fa fa-plus"></i> Nuevo Cliente API
                        </a>
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-right">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="cantidad_mostrar_cliente_api">Mostrar</label>
                            <select id="cantidad_mostrar_cliente_api" class="form-control" onchange="listar_clientesApiOrdenados()">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="busqueda_tabla_nombre">Buscar por Nombre</label>
                            <input type="text" class="form-control" id="busqueda_tabla_nombre" placeholder="Nombre del cliente API" onkeyup="listar_clientesApiOrdenados()">
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="busqueda_tabla_cliente">Filtrar por Cliente</label>
                            <select id="busqueda_tabla_cliente" class="form-control" onchange="listar_clientesApiOrdenados()">
                                <option value="0">TODOS</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="busqueda_tabla_estado">Filtrar por Estado</label>
                            <select id="busqueda_tabla_estado" class="form-control" onchange="listar_clientesApiOrdenados()">
                                <option value="">TODOS</option>
                                <option value="1">ACTIVO</option>
                                <option value="0">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6">
                        <div id="texto_paginacion_tabla_cliente_api"></div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <ul class="pagination pagination-rounded justify-content-end mb-0" id="lista_paginacion_tabla_cliente_api">
                        </ul>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div id="tablas_clientes_api"></div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6">
                        <div id="texto_paginacion_tabla_cliente_api2"></div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <ul class="pagination pagination-rounded justify-content-end mb-0" id="lista_paginacion_tabla_cliente_api2">
                        </ul>
                    </div>
                </div>

                <input type="hidden" id="pagina_cliente_api" value="1">
                <input type="hidden" id="filtro_nombre" value="">
                <input type="hidden" id="filtro_cliente" value="">
                <input type="hidden" id="filtro_estado_cliente_api" value="">
            </div>
        </div>
    </div>
</div>

<div id="modals_editar_cliente_api"></div>

<script src="<?php echo BASE_URL; ?>src/view/js/functions_cliente_api.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        listar_clientesApiOrdenados();
    });
</script>