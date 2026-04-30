<?php
// Iniciamos sesión para verificar si el usuario está logueado
session_start();
require 'config/db.php';

// 1. Verificar si la tabla debe estar visible
$queryConfig = $conexion->query("SELECT tabla_visible FROM sistema_config WHERE id = 1");
$config = $queryConfig->fetch(PDO::FETCH_ASSOC);

// Si no existe la configuración, por defecto mostrar (evita errores si la tabla está vacía)
$esta_visible = $config ? $config['tabla_visible'] : 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semana del Estudiante - Posiciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-primary { background-color: #cfe2ff !important; }
        .navbar-brand { font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow">
    <div class="container">
        <span class="navbar-brand mb-0 h1">🏆 Posiciones Semana del Estudiante</span>
        
        <div class="d-flex gap-2">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php 
                    $dashboard = ($_SESSION['rol'] === 'master') ? 'admin/master.php' : 'admin/carga_puntos.php';
                ?>
                <a href="<?php echo $dashboard; ?>" class="btn btn-warning btn-sm fw-bold">⚙️ Panel Admin</a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm">Login Admin</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    
    <?php if ($esta_visible == 0): ?>
        <div class="alert alert-warning text-center shadow-sm py-5">
            <h2 class="display-6">¡Resultados Ocultos!</h2>
            <p class="lead">El administrador ha ocultado la tabla. ¡Los resultados finales se darán pronto!</p>
            <img src="https://cdn-icons-png.flaticon.com/512/565/565547.png" width="100" class="mt-3 opacity-50" alt="Oculto">
        </div>
    <?php else: ?>
        
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary">Tabla General de Puntos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start">Curso</th>
                                <th class="table-secondary text-dark">Puntos Iniciales</th> 
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
                            $sql = "SELECT c.id, c.nombre_curso, c.division, c.puntos_iniciales,
                                    SUM(CASE WHEN p.dia_semana = 'Lunes' THEN p.cantidad ELSE 0 END) AS lunes,
                                    SUM(CASE WHEN p.dia_semana = 'Martes' THEN p.cantidad ELSE 0 END) AS martes,
                                    SUM(CASE WHEN p.dia_semana = 'Miércoles' THEN p.cantidad ELSE 0 END) AS miercoles,
                                    SUM(CASE WHEN p.dia_semana = 'Jueves' THEN p.cantidad ELSE 0 END) AS jueves,
                                    SUM(CASE WHEN p.dia_semana = 'Viernes' THEN p.cantidad ELSE 0 END) AS viernes,
                                    (c.puntos_iniciales + IFNULL(SUM(p.cantidad), 0)) AS total_general
                                    FROM cursos c
                                    LEFT JOIN puntos p ON c.id = p.curso_id
                                    GROUP BY c.id
                                    ORDER BY total_general DESC";
                            
                            $stmt = $conexion->prepare($sql);
                            $stmt->execute();
                            
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                            <tr>
                                <td class="text-start">
                                    <strong><?php echo htmlspecialchars($row['nombre_curso']); ?></strong> 
                                    <br><span class="badge bg-secondary"><?php echo htmlspecialchars($row['division']); ?></span>
                                </td>
                                <td class="table-secondary"><?php echo $row['puntos_iniciales']; ?></td>
                                <td><?php echo $row['lunes']; ?></td>
                                <td><?php echo $row['martes']; ?></td>
                                <td><?php echo $row['miercoles']; ?></td>
                                <td><?php echo $row['jueves']; ?></td>
                                <td><?php echo $row['viernes']; ?></td>
                                <td class="table-primary fw-bold fs-5"><?php echo $row['total_general']; ?></td>
                                <td>
                                    <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm text-white">Detalle</a>
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

<footer class="text-center mt-5 pb-4 text-muted">
    <small>&copy; <?php echo date('Y'); ?> - Sistema de Puntos Semana Estudiantil</small>
</footer>

</body>
</html>