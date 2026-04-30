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
    <!-- Logo / pestaña -->
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-primary { background-color: #cfe2ff !important; }
        .navbar-brand { font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-lg border-bottom border-light border-opacity-25">
    <div class="container py-2">

        <!-- Logo / Marca -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4 text-white" href="#">
            <span class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center"
                  style="width:42px;height:42px;font-size:1.2rem;">
                🏆
            </span>
            <div class="d-flex flex-column lh-sm">
                <span>Posiciones</span>
                <small class="fw-light text-white-50" style="font-size: 0.75rem;">
                    Semana del Estudiante
                </small>
            </div>
        </a>

        <!-- Botón Mobile -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú -->
        <div class="collapse navbar-collapse justify-content-end mt-3 mt-lg-0" id="navbarAdmin">

            <div class="d-flex flex-column flex-lg-row gap-2">

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php 
                        $dashboard = ($_SESSION['rol'] === 'master') ? 'admin/master.php' : 'admin/carga_puntos.php';
                    ?>

                    <a href="<?php echo $dashboard; ?>" 
                       class="btn btn-warning fw-bold px-4 rounded-pill shadow-sm">
                        ⚙️ Panel Admin
                    </a>

                    <a href="logout.php" 
                       class="btn btn-outline-light px-4 rounded-pill">
                        Cerrar Sesión
                    </a>

                <?php else: ?>

                    <a href="login.php" 
                       class="btn btn-light text-primary fw-bold px-4 rounded-pill shadow-sm">
                        🔐 Login Admin
                    </a>

                <?php endif; ?>

            </div>

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
        
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

    <!-- Header -->
    <div class="card-header bg-primary text-white py-3 px-4 border-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold">
                🏆 Tabla General de Puntos
            </h5>

            <span class="badge bg-light text-primary px-3 py-2 rounded-pill fw-semibold">
                Semana del Estudiante
            </span>
        </div>
    </div>

    <!-- Body -->
    <div class="card-body bg-white p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle text-center mb-0">

                <!-- Head -->
                <thead class="bg-dark text-white">
                    <tr class="align-middle">
                        <th class="text-start ps-4 py-3">Curso</th>
                        <th class="bg-secondary-subtle text-dark">Iniciales</th>
                        <th>Lun</th>
                        <th>Mar</th>
                        <th>Mié</th>
                        <th>Jue</th>
                        <th>Vie</th>
                        <th class="bg-warning text-dark fw-bold">Total</th>
                        <th class="pe-4">Acción</th>
                    </tr>
                </thead>

                <!-- Body -->
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

                    <tr class="border-bottom">

                        <!-- Curso -->
                        <td class="text-start ps-4 py-3">
                            <div class="fw-bold text-dark">
                                <?php echo htmlspecialchars($row['nombre_curso']); ?>
                            </div>

                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 mt-1">
                                <?php echo htmlspecialchars($row['division']); ?>
                            </span>
                        </td>

                        <!-- Iniciales -->
                        <td class="bg-light fw-semibold">
                            <?php echo $row['puntos_iniciales']; ?>
                        </td>

                        <!-- Días -->
                        <td><?php echo $row['lunes']; ?></td>
                        <td><?php echo $row['martes']; ?></td>
                        <td><?php echo $row['miercoles']; ?></td>
                        <td><?php echo $row['jueves']; ?></td>
                        <td><?php echo $row['viernes']; ?></td>

                        <!-- Total -->
                        <td class="bg-warning-subtle text-dark fw-bold fs-5">
                            <?php echo $row['total_general']; ?>
                        </td>

                        <!-- Acción -->
                        <td class="pe-4">
                            <a href="detalle.php?id=<?php echo $row['id']; ?>"
                               class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                Ver
                            </a>
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