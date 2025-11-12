<!-- start page title -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Nuevo Proyecto</h4>
                <br>
                <form class="form-horizontal" id="frmRegistrarProyecto">
                    <div class="form-group row mb-2">
                        <label for="nombre" class="col-3 col-form-label">Nombre del Proyecto</label>
                        <div class="col-9">
                            <input type="text" class="form-control" id="nombre" name="nombre">
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
                            <select name="id_cliente" id="id_cliente" class="form-control">
                                <option value="">Seleccionar cliente</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="fecha_inicio" class="col-3 col-form-label">Fecha Inicio</label>
                        <div class="col-9">
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="fecha_fin" class="col-3 col-form-label">Fecha Fin</label>
                        <div class="col-9">
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="presupuesto" class="col-3 col-form-label">Presupuesto (S/)</label>
                        <div class="col-9">
                            <input type="number" step="0.01" class="form-control" id="presupuesto" name="presupuesto">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label for="estado" class="col-3 col-form-label">Estado</label>
                        <div class="col-9">
                            <select name="estado" id="estado" class="form-control">
                                <option value="activo">ACTIVO</option>
                                <option value="inactivo">INACTIVO</option>
                                <option value="completado">COMPLETADO</option>
                                <option value="cancelado">CANCELADO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0 justify-content-end row text-center">
                        <div class="col-12">
                            <a href="<?php echo BASE_URL;?>proyectos" class="btn btn-light waves-effect waves-light">Regresar</a>
                            <button type="button" class="btn btn-success waves-effect waves-light" onclick="registrar_proyecto();">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>src/view/js/functions_proyecto.js"></script>
<!-- end page title -->