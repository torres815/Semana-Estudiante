<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$cursos = $conexion->query("SELECT * FROM cursos ORDER BY nombre_curso ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencia - Semana del Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .navbar { background-color: #28a745 !important; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .custom-file-upload { 
            border: 2px dashed #ccc; padding: 30px; text-align: center; 
            border-radius: 10px; cursor: pointer; transition: 0.3s; background: #fff;
        }
        .custom-file-upload:hover { border-color: #28a745; background: #f0fff4; }
        .btn-success { border-radius: 10px; padding: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4 shadow">
        <div class="container">
            <span class="navbar-brand">📝 Control de Asistencia</span>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Volver</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="text-center mb-4">Cargar Alumnos desde Excel</h4>
                    <form action="procesar_asistencia.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="form-label fw-bold">1. Seleccionar el Curso</label>
                            <select name="curso_id" class="form-select" required>
                                <option value="">Elija el curso...</option>
                                <?php foreach($cursos as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['nombre_curso'] ?> - <?= $c['division'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">2. Subir Lista (CSV)</label>
                            <label class="custom-file-upload w-100">
                                <input type="file" name="archivo_lista" accept=".csv" required style="display:none;" onchange="this.nextElementSibling.innerText = this.files[0].name">
                                <span>Haga clic aquí para seleccionar el archivo .csv</span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success w-100 shadow-sm">PROCESAR ASISTENCIA</button>
                    </form>
                    <div class="card mt-4 border-danger">
    <div class="card-header bg-danger text-white">⚠️ Zona de Peligro</div>
    <div class="card-body">
        <p class="small text-muted">Usa esto para limpiar la lista antes de una nueva carga o al finalizar el día.</p>
        <form action="procesar_asistencia.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres borrar todos los alumnos? Esta acción no se puede deshacer.');">
            <input type="hidden" name="accion" value="resetear_todo">
            <button type="submit" class="btn btn-outline-danger w-100">Vaciar Toda la Asistencia</button>
        </form>
    </div>
</div>
                </div>
                <div class="alert alert-info mt-4">
                    <strong>Instrucciones:</strong> El archivo debe ser un CSV. La primera columna debe contener Nombre y Apellido.
                </div>
            </div>
        </div>
    </div>
</body>
</html>