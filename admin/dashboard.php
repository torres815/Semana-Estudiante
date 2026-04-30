<?php
session_start();
require '../config/db.php';

// Verificación de seguridad: Si no hay sesión, a casa.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener la lista de cursos para el selector (dropdown)
$queryCursos = $conexion->query("SELECT * FROM cursos ORDER BY nombre_curso ASC");
$cursos = $queryCursos->fetchAll(PDO::FETCH_ASSOC);

// Mensajes de éxito o error
$mensaje = "";
if (isset($_GET['success'])) $mensaje = "¡Puntos cargados correctamente!";
if (isset($_GET['error'])) $mensaje = "Hubo un error al cargar los puntos.";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Carga de Puntos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f4f6f9;
        }

        .admin-navbar{
            background:linear-gradient(135deg,#212529,#343a40);
        }

        .card-panel{
            border:none;
            border-radius:22px;
            overflow:hidden;
        }

        .card-header-custom{
            background:linear-gradient(135deg,#0d6efd,#0b5ed7);
        }

        .form-control,
        .form-select,
        textarea{
            border-radius:14px;
            padding:12px 14px;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus{
            box-shadow:none;
            border-color:#0d6efd;
        }

        .btn-rounded{
            border-radius:14px;
            font-weight:700;
            padding:12px;
        }

        .badge-role{
            font-size:.8rem;
            letter-spacing:.5px;
        }

        .top-card{
            border:none;
            border-radius:18px;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark admin-navbar shadow-sm mb-4">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
            ⚙️ Panel Administrativo
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#menuAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end mt-3 mt-lg-0" id="menuAdmin">

            <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center">

                <!-- Usuario -->
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill badge-role">
                    👤 <?php echo $_SESSION['username']; ?>
                </span>

                <!-- Ver Tabla -->
                <a href="../index.php" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                    🏆 Ver Tabla
                </a>

                <?php if($_SESSION['rol'] == 'master'): ?>
                    <a href="master.php" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold">
                        👑 Master
                    </a>
                <?php endif; ?>

                <?php if($_SESSION['rol'] == 'master'): ?>
                    <a href="sorteo.php" class="btn btn-primary btn-sm rounded-pill px-3">
                        🎁 Sorteo
                    </a>

                    <a href="asistencia.php" class="btn btn-primary btn-sm rounded-pill px-3">
                        📋 Asistencia
                    </a>
                <?php endif; ?>

                <!-- Salir -->
                <a href="../procesar_logout.php" class="btn btn-danger btn-sm rounded-pill px-3">
                    🚪 Salir
                </a>

            </div>

        </div>

    </div>
</nav>

<!-- CONTENIDO -->
<div class="container">

    <!-- Resumen -->
    <div class="row mb-4 g-3">

        <div class="col-md-4">
            <div class="card top-card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Usuario activo</small>
                    <h5 class="fw-bold mb-0"><?php echo $_SESSION['username']; ?></h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card top-card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Rol</small>
                    <h5 class="fw-bold mb-0 text-primary text-uppercase">
                        <?php echo $_SESSION['rol']; ?>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card top-card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Acción rápida</small>
                    <h5 class="fw-bold mb-0 text-success">Carga de puntos</h5>
                </div>
            </div>
        </div>

    </div>

    <!-- Formulario -->
    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card card-panel shadow-lg">

                <!-- Header -->
                <div class="card-header card-header-custom text-white py-4 px-4 border-0">
                    <h4 class="mb-1 fw-bold">🏆 Cargar Nuevos Puntos</h4>
                    <small class="text-white-50">
                        Registra premios, sanciones o puntajes diarios
                    </small>
                </div>

                <!-- Body -->
                <div class="card-body p-4 bg-white">

                    <?php if($mensaje): ?>
                        <div class="alert alert-info border-0 rounded-4 fw-semibold">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <form action="procesar_puntos.php" method="POST">

                        <!-- Curso -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Seleccionar Curso</label>

                            <select name="curso_id" class="form-select" required>
                                <option value="">Elija un curso...</option>

                                <?php foreach($cursos as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo $c['nombre_curso'] . " - " . $c['division']; ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <!-- fila -->
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Puntos</label>

                                <input 
                                    type="number" 
                                    name="cantidad" 
                                    class="form-control"
                                    placeholder="Ej: 10 o -5"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Día</label>

                                <select name="dia" class="form-select" required>
                                    <option value="Lunes">Lunes</option>
                                    <option value="Martes">Martes</option>
                                    <option value="Miércoles">Miércoles</option>
                                    <option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option>
                                </select>
                            </div>

                        </div>

                        <!-- Motivo -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Motivo / Descripción</label>

                            <textarea 
                                name="motivo" 
                                rows="4"
                                class="form-control"
                                placeholder="Ej: Ganadores en voley, conducta en el acto, etc."
                                required></textarea>
                        </div>

                        <!-- Botón -->
                        <button type="submit" class="btn btn-success w-100 btn-rounded shadow-sm">
                            💾 Guardar Puntos
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>