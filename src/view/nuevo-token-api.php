<!-- start page title -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Nuevo Token API</h4>
                <br>
                <form class="form-horizontal" id="frmRegistrarTokenApi">
                    <div class="form-group row mb-2">
                        <label for="id_cliente_api" class="col-3 col-form-label">Cliente API</label>
                        <div class="col-9">
                            <select name="id_cliente_api" id="id_cliente_api" class="form-control" required>
                                <option value="">Seleccionar cliente API</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="fecha_expiracion" class="col-3 col-form-label">Fecha Expiración</label>
                        <div class="col-9">
                            <input type="datetime-local" class="form-control" id="fecha_expiracion" name="fecha_expiracion" required>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="permisos" class="col-3 col-form-label">Permisos (JSON)</label>
                        <div class="col-9">
                            <textarea class="form-control" id="permisos" name="permisos" rows="3" placeholder='Ejemplo: {"lectura": true, "escritura": false, "eliminacion": false}'></textarea>
                            <small class="form-text text-muted">Especifique los permisos en formato JSON</small>
                        </div>
                    </div>
                    <div class="form-group mb-0 justify-content-end row text-center">
                        <div class="col-12">
                            <a href="<?php echo BASE_URL;?>tokens-api" class="btn btn-light waves-effect waves-light">Regresar</a>
                            <button type="button" class="btn btn-success waves-effect waves-light" onclick="registrar_token_api();">Generar Token</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>src/view/js/functions_token_api.js"></script>
<!-- end page title -->