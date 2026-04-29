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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalle: <span class="text-primary"><?php echo $curso['nombre_curso']; ?></span></h2>
        <a href="index.php" class="btn btn-secondary">← Volver</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            Historial de Movimientos - <?php echo $curso['division']; ?>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Día</th>
                        <th>Puntos</th>
                        <th>Motivo</th>
                        <th>Fecha/Hora</th>
                        <th>Cargado por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($historial) > 0): ?>
                        <?php foreach ($historial as $h): ?>
                            <tr>
                                <td><span class="badge bg-info text-dark"><?php echo $h['dia_semana']; ?></span></td>
                                <td>
                                    <strong class="<?php echo $h['cantidad'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo ($h['cantidad'] > 0 ? '+' : '') . $h['cantidad']; ?>
                                    </strong>
                                </td>
                                <td><?php echo $h['motivo']; ?></td>
                                <td><small><?php echo date('d/m H:i', strtotime($h['fecha_registro'])); ?> hs</small></td>
                                <td><small class="text-muted">@<?php echo $h['username']; ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">Este curso aún no tiene puntos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>