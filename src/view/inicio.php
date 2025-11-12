<!-- DASHBOARD MODERNO -->
<style>
    .card-glass {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border-radius: 15px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .card-glass:hover {
        transform: translateY(-3px);
    }

    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    .glass-title {
        font-weight: 600;
        color: #333;
    }

    .glass-value {
        font-size: 32px;
        font-weight: bold;
    }

    .btn-glass {
        background: rgba(255, 255, 255, 0.1);
        color: #333;
        border: none;
    }

    .btn-glass:hover {
        background: rgba(0, 0, 0, 0.1);
    }

    /* Espaciado adicional entre los módulos */
    .card-glass-container {
        margin-bottom: 30px;
    }
</style>

<div class="row g-4">
    <!-- CARD 1: Usuarios -->
    <div class="col-md-6 col-xl-4 card-glass-container">
        <div class="card-glass p-4 text-center">
            <div class="icon-circle bg-primary mx-auto mb-3">
                <i class="fa fa-users"></i>
            </div>
            <div class="glass-title">Usuarios</div>
            <div class="glass-value">20</div>
            <a href="#" class="btn btn-glass mt-3">Ver</a>
        </div>
    </div>

    <!-- CARD 2: Instituciones -->
    <div class="col-md-6 col-xl-4 card-glass-container">
        <div class="card-glass p-4 text-center">
            <div class="icon-circle bg-success mx-auto mb-3">
                <i class="fa fa-university"></i>
            </div>
            <div class="glass-title">Clientes</div>
            <div class="glass-value">10</div>
            <a href="<?php echo BASE_URL ?>clientes" class="dropdown-item">Ver</a>
        </div>
    </div>

    <!-- CARD 3: Ambientes -->
    <div class="col-md-6 col-xl-4 card-glass-container">
        <div class="card-glass p-4 text-center">
            <div class="icon-circle bg-warning mx-auto mb-3">
                <i class="fa fa-building"></i>
            </div>
            <div class="glass-title">Proyectos</div>
            <div class="glass-value">20</div>
            <a href="<?php echo BASE_URL ?>proyectos" class="dropdown-item">Ver</a>
        </div>
    </div>

    <!-- CARD 4: Bienes -->
    <div class="col-md-6 col-xl-4 card-glass-container">
        <div class="card-glass p-4 text-center">
            <div class="icon-circle bg-info mx-auto mb-3">
                <i class="fa fa-cogs"></i>
            </div>
            <div class="glass-title">Token Api</div>
            <div class="glass-value">4</div>
           <a href="<?php echo BASE_URL ?>tokens-api" class="dropdown-item">Ver</a>
        </div>
    </div>

    <!-- CARD 5: Movimientos -->
    <div class="col-md-6 col-xl-4 card-glass-container">
        <div class="card-glass p-4 text-center">
            <div class="icon-circle bg-danger mx-auto mb-3">
                <i class="fa fa-exchange-alt"></i>
            </div>
            <div class="glass-title">Cliente Api</div>
            <div class="glass-value">7</div>
            <a href="<?php echo BASE_URL ?>clientes-api" class="dropdown-item">Ver</a>
        </div>
    </div>

    <!-- CARD 6: Reportes -->
    <div class="col-md-6 col-xl-4 card-glass-container">
        <div class="card-glass p-4 text-center">
            <div class="icon-circle bg-dark mx-auto mb-3">
                <i class="fa fa-file-alt"></i>
            </div>
            <div class="glass-title">Reportes</div>
            <div class="glass-value">01/04/2025</div>
            <a href="<?php echo BASE_URL ?>reportes" class="dropdown-item">Ver</a>
        </div>
    </div>

</div>
