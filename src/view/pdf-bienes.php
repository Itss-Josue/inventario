<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Bienes</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Reporte de Bienes Registrados</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Código Patrimonial</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Serie</th>
                <th>Color</th>
                <th>Estado</th>
                <th>Ambiente</th>
                <th>Responsable</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; foreach ($datos as $bien): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $bien['codigo_patrimonial'] ?></td>
                    <td><?= $bien['nombre_bien'] ?></td>
                    <td><?= $bien['marca'] ?></td>
                    <td><?= $bien['modelo'] ?></td>
                    <td><?= $bien['serie'] ?></td>
                    <td><?= $bien['color'] ?></td>
                    <td><?= $bien['estado_bien'] ?></td>
                    <td><?= $bien['ambiente'] ?></td>
                    <td><?= $bien['responsable'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
