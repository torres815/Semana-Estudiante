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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asistencia - Semana del Estudiante</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f6f9;
        }

        .navbar-custom{
            background:linear-gradient(135deg,#198754,#157347);
        }

        .panel-card{
            border:none;
            border-radius:24px;
            overflow:hidden;
        }

        .upload-box{
            border:2px dashed #ced4da;
            border-radius:18px;
            padding:35px 20px;
            text-align:center;
            cursor:pointer;
            transition:.25s ease;
            background:#fff;
        }

        .upload-box:hover{
            border-color:#198754;
            background:#f3fff8;
            transform:translateY(-2px);
        }

        .btn-rounded{
            border-radius:14px;
            padding:12px;
            font-weight:700;
        }

        .form-control,
        .form-select{
            border-radius:14px;
            padding:12px 14px;
        }

        .form-control:focus,
        .form-select:focus{
            box-shadow:none;
            border-color:#198754;
        }

        .danger-card{
            border:none;
            border-radius:18px;
            overflow:hidden;
        }

        .top-box{
            border:none;
            border-radius:18px;
        }
    </style>
</head>

<body>

<!-- NAV -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
            📝 Control de Asistencia
        </a>

        <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4">
            ← Volver
        </a>

    </div>
</nav>

<!-- CONTENIDO -->
<div class="container">

    <!-- Tarjetas superiores -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card top-box shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Módulo</small>
                    <h6 class="fw-bold mb-0">Carga de alumnos</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card top-box shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Formato</small>
                    <h6 class="fw-bold mb-0 text-success">Archivo CSV</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card top-box shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Estado</small>
                    <h6 class="fw-bold mb-0 text-primary">Listo para importar</h6>
                </div>
            </div>
        </div>

    </div>

    <!-- Panel principal -->
    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card panel-card shadow-lg">

                <!-- Header -->
                <div class="card-header bg-success text-white py-4 px-4 border-0">
                    <h4 class="fw-bold mb-1">📋 Cargar Alumnos</h4>
                    <small class="text-white-50">
                        Importa listas para registrar asistencia
                    </small>
                </div>

                <!-- Body -->
                <div class="card-body p-4 bg-white">

                    <form action="procesar_asistencia.php" method="POST" enctype="multipart/form-data">

                        <!-- Curso -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                1. Seleccionar Curso
                            </label>

                            <select name="curso_id" class="form-select" required>
                                <option value="">Elija el curso...</option>

                                <?php foreach($cursos as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= $c['nombre_curso'] ?> - <?= $c['division'] ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- Archivo -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                2. Subir Lista (CSV)
                            </label>

                            <label class="upload-box w-100">

                                <input 
                                    type="file"
                                    name="archivo_lista"
                                    accept=".csv"
                                    required
                                    style="display:none;"
                                    onchange="this.nextElementSibling.innerText = this.files[0].name">

                                <span class="text-muted fw-semibold">
                                    📁 Haga clic aquí para seleccionar el archivo .csv
                                </span>

                            </label>

                        </div>

                        <!-- Botón -->
                        <button type="submit" class="btn btn-success w-100 btn-rounded shadow-sm">
                            🚀 PROCESAR ASISTENCIA
                        </button>

                    </form>

                    <!-- Zona peligro -->
                    <div class="card danger-card mt-4 shadow-sm">

                        <div class="card-header bg-danger text-white fw-bold">
                            ⚠️ Zona de Peligro
                        </div>

                        <div class="card-body">

                            <p class="small text-muted mb-3">
                                Usa esta opción para limpiar la lista antes de una nueva carga
                                o al finalizar la jornada.
                            </p>

                            <form action="procesar_asistencia.php" method="POST"
                                  onsubmit="return confirm('¿Estás seguro de que quieres borrar todos los alumnos? Esta acción no se puede deshacer.');">

                                <input type="hidden" name="accion" value="resetear_todo">

                                <button type="submit" class="btn btn-outline-danger w-100 btn-rounded">
                                    🗑 Vaciar Toda la Asistencia
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Info -->
            <div class="alert alert-info border-0 rounded-4 shadow-sm mt-4">

                <strong>📌 Instrucciones:</strong><br>
                El archivo debe estar en formato <strong>CSV</strong>.
                La primera columna debe contener <strong>Nombre y Apellido</strong>.

            </div>

        </div>

    </div>

</div>

</body>
</html>