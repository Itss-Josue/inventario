<!-- start page title -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="page-title-box d-flex align-items-center justify-content-between p-0">
                    <h4 class="mb-0 font-size-18">Clientes</h4>
                    <div class="page-title-right">
                        <a href="<?php echo BASE_URL; ?>nuevo-cliente" class="btn btn-primary waves-effect waves-light">+ Nuevo</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Filtros de Búsqueda</h4>
                <div class="row col-12">
                    <div class="form-group row mb-3 col-6">
                        <label for="busqueda_tabla_ruc" class="col-5 col-form-label">RUC:</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="busqueda_tabla_ruc" id="busqueda_tabla_ruc" maxlength="11">
                        </div>
                    </div>
                    <div class="form-group row mb-3 col-6">
                        <label for="busqueda_tabla_razon_social" class="col-5 col-form-label">Razón Social:</label>
                        <div class="col-7">
                            <input type="text" class="form-control" name="busqueda_tabla_razon_social" id="busqueda_tabla_razon_social">
                        </div>
                    </div>
                    <div class="form-group row mb-3 col-6">
                        <label for="busqueda_tabla_estado" class="col-5 col-form-label">Estado:</label>
                        <div class="col-7">
                            <select class="form-control" name="busqueda_tabla_estado" id="busqueda_tabla_estado">
                                <option value="">TODOS</option>
                                <option value="1">ACTIVO</option>
                                <option value="0">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-center ">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="numero_pagina_cliente(1);"><i class="fa fa-search"></i> Buscar</button>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <a href="#" class="btn btn-danger" title="REPORTE GENERAL" onclick="generarReporteClientes()"><i class="fa fa-file-pdf"></i></a>
                <h4 class="card-title">Resultados de Búsqueda</h4>
                <div id="filtros_tabla_header" class="form-group  row page-title-box d-flex align-items-center justify-content-between m-0 mb-1 p-0">
                    <input type="hidden" id="pagina_cliente" value="1">
                    <input type="hidden" id="filtro_ruc" value="">
                    <input type="hidden" id="filtro_razon_social" value="">
                    <input type="hidden" id="filtro_estado_cliente" value="">
                    <div>
                        <label for="cantidad_mostrar_cliente">Mostrar</label>
                        <select name="cantidad_mostrar_cliente" id="cantidad_mostrar_cliente" class="form-control-sm" onchange="numero_pagina_cliente(1);">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label for="cantidad_mostrar_cliente">registros</label>
                    </div>
                </div>
                <div id="tablas_clientes"></div>
                <div id="filtros_tabla_footer" class="form-group  row page-title-box d-flex align-items-center justify-content-between m-0 mb-1 p-0">
                    <div id="texto_paginacion_tabla_cliente"></div>
                    <div id="paginacion_tabla_cliente">
                        <ul class="pagination justify-content-end" id="lista_paginacion_tabla_cliente"></ul>
                    </div>
                </div>
                <div id="modals_editar_cliente"></div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>src/view/js/functions_cliente.js"></script>
<script>
    listar_clientesOrdenados();
</script>
<!-- end page title -->