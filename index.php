<?php
require 'config/db.php';

// 1. Verificar si la tabla debe estar visible
$queryConfig = $conexion->query("SELECT tabla_visible FROM sistema_config WHERE id = 1");
$config = $queryConfig->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Semana del Estudiante - Posiciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">🏆 Posiciones Semana del Estudiante</span>
        <a href="login.php" class="btn btn-outline-light btn-sm">Login Admin</a>
    </div>
</nav>

<div class="container">
    
    <?php if ($config['tabla_visible'] == 0): ?>
        <div class="alert alert-warning text-center shadow-sm">
            <h2 class="display-6">¡Resultados Ocultos!</h2>
            <p>El administrador ha ocultado la tabla. ¡Los resultados finales se darán pronto!</p>
        </div>
    <?php else: ?>
        
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Curso</th>
                                <th>Lunes</th>
                                <th>Martes</th>
                                <th>Miércoles</th>
                                <th>Jueves</th>
                                <th>Viernes</th>
                                <th class="table-primary text-dark">Total</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta SQL Maestra: Suma puntos por día usando CASE
                            $sql = "SELECT c.id, c.nombre_curso, c.division,
                                    SUM(CASE WHEN p.dia_semana = 'Lunes' THEN p.cantidad ELSE 0 END) AS lunes,
                                    SUM(CASE WHEN p.dia_semana = 'Martes' THEN p.cantidad ELSE 0 END) AS martes,
                                    SUM(CASE WHEN p.dia_semana = 'Miércoles' THEN p.cantidad ELSE 0 END) AS miercoles,
                                    SUM(CASE WHEN p.dia_semana = 'Jueves' THEN p.cantidad ELSE 0 END) AS jueves,
                                    SUM(CASE WHEN p.dia_semana = 'Viernes' THEN p.cantidad ELSE 0 END) AS viernes,
                                    SUM(p.cantidad) AS total_general
                                    FROM cursos c
                                    LEFT JOIN puntos p ON c.id = p.curso_id
                                    GROUP BY c.id
                                    ORDER BY total_general DESC";
                            
                            $stmt = $conexion->prepare($sql);
                            $stmt->execute();
                            
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                $total = $row['total_general'] ?? 0;
                            ?>
                            <tr>
                                <td class="text-start"><strong><?php echo $row['nombre_curso']; ?></strong> <br><small><?php echo $row['division']; ?></small></td>
                                <td><?php echo $row['lunes']; ?></td>
                                <td><?php echo $row['martes']; ?></td>
                                <td><?php echo $row['miercoles']; ?></td>
                                <td><?php echo $row['jueves']; ?></td>
                                <td><?php echo $row['viernes']; ?></td>
                                <td class="fw-bold"><?php echo $total; ?></td>
                                <td>
                                    <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Ver Detalle</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>