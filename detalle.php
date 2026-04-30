<?php
require 'config/db.php';

// 1. Obtener el ID del curso y validar que exista
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$curso_id = $_GET['id'];

// 2. Obtener información del curso
$stmtCurso = $conexion->prepare("SELECT * FROM cursos WHERE id = :id");
$stmtCurso->execute(['id' => $curso_id]);
$curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    die("Curso no encontrado.");
}

// 3. Obtener el historial de puntos con el nombre del usuario que cargó
$sqlPuntos = "SELECT p.*, u.username 
              FROM puntos p 
              INNER JOIN usuarios u ON p.usuario_id = u.id 
              WHERE p.curso_id = :id 
              ORDER BY p.fecha_registro DESC";

$stmtPuntos = $conexion->prepare($sqlPuntos);
$stmtPuntos->execute(['id' => $curso_id]);
$historial = $stmtPuntos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Puntos - <?php echo $curso['nombre_curso']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                📊 Detalle:
                <span class="text-primary">
                    <?php echo $curso['nombre_curso']; ?>
                </span>
            </h2>

            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                División <?php echo $curso['division']; ?>
            </span>
        </div>

        <a href="index.php" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
            ← Volver
        </a>

    </div>

    <!-- Card principal -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

        <!-- Header -->
        <div class="card-header bg-primary text-white border-0 py-3 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0 fw-bold">
                    🏆 Historial de Movimientos
                </h5>

                <span class="badge bg-light text-primary rounded-pill px-3 py-2 fw-semibold">
                    Semana del Estudiante
                </span>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body p-0 bg-white">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <!-- Head -->
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4 py-3">Día</th>
                            <th>Puntos</th>
                            <th>Motivo</th>
                            <th>Fecha / Hora</th>
                            <th class="pe-4">Cargado por</th>
                        </tr>
                    </thead>

                    <!-- Body -->
                    <tbody>

                        <?php if (count($historial) > 0): ?>
                            <?php foreach ($historial as $h): ?>

                                <tr>

                                    <!-- Día -->
                                    <td class="ps-4">
                                        <span class="badge bg-info-subtle text-dark rounded-pill px-3 py-2">
                                            <?php echo $h['dia_semana']; ?>
                                        </span>
                                    </td>

                                    <!-- Puntos -->
                                    <td>
                                        <span class="fw-bold fs-5 <?php echo $h['cantidad'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo ($h['cantidad'] > 0 ? '+' : '') . $h['cantidad']; ?>
                                        </span>
                                    </td>

                                    <!-- Motivo -->
                                    <td class="fw-semibold text-dark">
                                        <?php echo $h['motivo']; ?>
                                    </td>

                                    <!-- Fecha -->
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m H:i', strtotime($h['fecha_registro'])); ?> hs
                                        </small>
                                    </td>

                                    <!-- Usuario -->
                                    <td class="pe-4">
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                                            @<?php echo $h['username']; ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="5" class="text-center py-5">

                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div style="font-size: 2rem;">📭</div>

                                        <div class="fw-semibold text-muted">
                                            Este curso aún no tiene puntos registrados
                                        </div>
                                    </div>

                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>