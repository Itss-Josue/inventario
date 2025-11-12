<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Gestión de Tokens API</h4>
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
                        <a href="<?php echo BASE_URL;?>nuevo-token-api" class="btn btn-danger waves-effect waves-light">
                            <i class="fa fa-plus"></i> Nuevo Token API
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
                            <label for="cantidad_mostrar_token_api">Mostrar</label>
                            <select id="cantidad_mostrar_token_api" class="form-control" onchange="listar_tokensApiOrdenados()">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="busqueda_tabla_cliente_api">Filtrar por Cliente API</label>
                            <select id="busqueda_tabla_cliente_api" class="form-control" onchange="listar_tokensApiOrdenados()">
                                <option value="0">TODOS</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label for="busqueda_tabla_estado">Filtrar por Estado</label>
                            <select id="busqueda_tabla_estado" class="form-control" onchange="listar_tokensApiOrdenados()">
                                <option value="">TODOS</option>
                                <option value="1">ACTIVO</option>
                                <option value="0">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6">
                        <div id="texto_paginacion_tabla_token_api"></div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <ul class="pagination pagination-rounded justify-content-end mb-0" id="lista_paginacion_tabla_token_api">
                        </ul>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div id="tablas_tokens_api"></div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-sm-12 col-md-6">
                        <div id="texto_paginacion_tabla_token_api2"></div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <ul class="pagination pagination-rounded justify-content-end mb-0" id="lista_paginacion_tabla_token_api2">
                        </ul>
                    </div>
                </div>

                <input type="hidden" id="pagina_token_api" value="1">
                <input type="hidden" id="filtro_cliente_api" value="">
                <input type="hidden" id="filtro_estado_token_api" value="">
            </div>
        </div>
    </div>
</div>

<div id="modals_editar_token_api"></div>

<script src="<?php echo BASE_URL; ?>src/view/js/functions_token_api.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        listar_tokensApiOrdenados();
    });
</script>