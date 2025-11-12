<!-- start page title -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Nuevo Cliente API</h4>
                <br>
                <form class="form-horizontal" id="frmRegistrarClienteApi">
                    <div class="form-group row mb-2">
                        <label for="nombre" class="col-3 col-form-label">Nombre</label>
                        <div class="col-9">
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="descripcion" class="col-3 col-form-label">Descripción</label>
                        <div class="col-9">
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="id_cliente" class="col-3 col-form-label">Cliente</label>
                        <div class="col-9">
                            <select name="id_cliente" id="id_cliente" class="form-control" required>
                                <option value="">Seleccionar cliente</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="ip_permisos" class="col-3 col-form-label">IP Permisos</label>
                        <div class="col-9">
                            <textarea class="form-control" id="ip_permisos" name="ip_permisos" rows="2" placeholder="Ejemplo: 192.168.1.1, 10.0.0.0/24 (dejar vacío para permitir todas)"></textarea>
                            <small class="form-text text-muted">Separar múltiples IPs con comas. Ejemplo: 192.168.1.1, 10.0.0.0/24</small>
                        </div>
                    </div>
                    <div class="form-group mb-0 justify-content-end row text-center">
                        <div class="col-12">
                            <a href="<?php echo BASE_URL;?>clientes-api" class="btn btn-light waves-effect waves-light">Regresar</a>
                            <button type="button" class="btn btn-success waves-effect waves-light" onclick="registrar_cliente_api();">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>src/view/js/functions_cliente_api.js"></script>
<!-- end page title -->