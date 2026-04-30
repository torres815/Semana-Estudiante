<?php
session_start();
require '../config/db.php';

// SEGURIDAD: Solo el 'master' puede entrar aquí
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'master') {
    header("Location: dashboard.php");
    exit();
}

// 1. Obtener estado actual de la tabla
$queryConfig = $conexion->query("SELECT tabla_visible FROM sistema_config WHERE id = 1");
$config = $queryConfig->fetch(PDO::FETCH_ASSOC);

// 2. Obtener lista de usuarios actuales (para ver quiénes están registrados)
$usuarios = $conexion->query("SELECT id, username, rol FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

$mensaje = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'user_ok') $mensaje = "Usuario registrado con éxito.";
    if ($_GET['msg'] == 'config_ok') $mensaje = "Visibilidad de la tabla actualizada.";
    if ($_GET['msg'] == 'error') $mensaje = "Ocurrió un error inesperado.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Panel Master - Control Total</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:linear-gradient(135deg,#111827 0%, #1f2937 35%, #f5f7fa 35%, #f5f7fa 100%);
    min-height:100vh;
}

.master-navbar{
    background:linear-gradient(135deg,#7f1d1d,#dc2626);
}

.master-card{
    border:none;
    border-radius:24px;
    overflow:hidden;
}

.master-shadow{
    box-shadow:0 15px 35px rgba(0,0,0,.12);
}

.section-title{
    font-weight:800;
    letter-spacing:.4px;
}

.form-control,
.form-select{
    border-radius:14px;
    padding:12px 14px;
}

.form-control:focus,
.form-select:focus{
    box-shadow:none;
    border-color:#dc2626;
}

.btn-master{
    border-radius:14px;
    font-weight:700;
    padding:12px 16px;
}

.table thead th{
    white-space:nowrap;
}

.glow-badge{
    box-shadow:0 0 0 4px rgba(255,255,255,.08);
}

.stat-box{
    border:none;
    border-radius:18px;
}
</style>
</head>

<body>

<!-- NAVBAR MASTER -->
<nav class="navbar navbar-expand-lg navbar-dark master-navbar shadow mb-4">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
            👑 PANEL MASTER
        </a>

        <div class="d-flex gap-2">
            <span class="badge bg-light text-danger px-3 py-2 rounded-pill glow-badge">
                CONTROL TOTAL
            </span>

            <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4">
                ← Volver
            </a>
        </div>

    </div>
</nav>

<div class="container pb-5">

    <?php if($mensaje): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm fw-semibold">
            ✅ <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <!-- BLOQUE SUPERIOR -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card stat-box shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Nivel de acceso</small>
                    <h5 class="fw-bold text-danger mb-0">MASTER</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-box shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Permisos</small>
                    <h5 class="fw-bold mb-0">Ilimitados</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-box shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Sistema</small>
                    <h5 class="fw-bold text-success mb-0">Operativo</h5>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        <!-- VISIBILIDAD -->
        <div class="col-lg-4">

            <div class="card master-card master-shadow">

                <div class="card-header bg-dark text-white py-3 px-4 border-0">
                    <h5 class="mb-0 section-title">🛰️ VISIBILIDAD GLOBAL</h5>
                </div>

                <div class="card-body p-4">

                    <p class="mb-3">
                        Estado actual:
                        <strong class="<?php echo $config['tabla_visible'] ? 'text-success':'text-danger'; ?>">
                            <?php echo $config['tabla_visible'] ? 'PÚBLICO' : 'OCULTO'; ?>
                        </strong>
                    </p>

                    <form action="procesar_master.php" method="POST">

                        <input type="hidden" name="accion" value="cambiar_visibilidad">
                        <input type="hidden" name="estado_actual" value="<?php echo $config['tabla_visible']; ?>">

                        <?php if($config['tabla_visible']): ?>
                            <button type="submit" class="btn btn-warning w-100 btn-master">
                                🔒 Ocultar Tabla
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success w-100 btn-master">
                                🌍 Hacer Pública
                            </button>
                        <?php endif; ?>

                    </form>

                </div>

            </div>

        </div>

        <!-- GESTION USUARIOS -->
        <div class="col-lg-8">

            <div class="card master-card master-shadow">

                <div class="card-header bg-danger text-white py-3 px-4 border-0">
                    <h5 class="mb-0 section-title">🛡️ CREAR NUEVO ADMINISTRADOR</h5>
                </div>

                <div class="card-body p-4">

                    <form action="procesar_master.php" method="POST">

                        <input type="hidden" name="accion" value="registrar_usuario">

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Usuario</label>
                                <input type="text" name="nuevo_username" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <input type="password" name="nuevo_password" class="form-control" required>
                            </div>

                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Nivel de acceso</label>

                            <select name="nuevo_rol" class="form-select">
                                <option value="admin">Administrador</option>
                                <option value="master">Master</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger btn-master">
                            ⚡ Crear Usuario
                        </button>

                    </form>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3">👥 Usuarios Registrados</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach($usuarios as $u): ?>

                                <tr>
                                    <td><?php echo $u['id']; ?></td>

                                    <td class="fw-semibold">
                                        <?php echo $u['username']; ?>
                                    </td>

                                    <td>
                                        <span class="badge <?php echo $u['rol']=='master'?'bg-danger':'bg-secondary'; ?> rounded-pill px-3 py-2">
                                            <?php echo strtoupper($u['rol']); ?>
                                        </span>
                                    </td>
                                </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        </div>

        <!-- CURSOS -->
        <div class="col-12">

            <div class="card master-card master-shadow">

                <div class="card-header bg-primary text-white py-3 px-4 border-0">
                    <h5 class="mb-0 section-title">🏆 GESTIÓN DE CURSOS Y PUNTOS INICIALES</h5>
                </div>

                <div class="card-body p-4">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-dark">
                                <tr>
                                    <th>Curso</th>
                                    <th>División</th>
                                    <th>Puntos Iniciales</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php 
                            $cursos_master = $conexion->query("SELECT * FROM cursos ORDER BY nombre_curso ASC")->fetchAll(PDO::FETCH_ASSOC);
                            foreach($cursos_master as $cm): 
                            ?>

                            <tr>

                                <form action="procesar_master.php" method="POST">

                                    <input type="hidden" name="accion" value="actualizar_puntos_iniciales">
                                    <input type="hidden" name="curso_id" value="<?= $cm['id'] ?>">

                                    <td class="fw-semibold"><?= $cm['nombre_curso'] ?></td>

                                    <td><?= $cm['division'] ?></td>

                                    <td style="max-width:180px;">
                                        <input type="number"
                                               name="puntos_ini"
                                               class="form-control form-control-sm"
                                               value="<?= $cm['puntos_iniciales'] ?>">
                                    </td>

                                    <td>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                                            💾 Actualizar
                                        </button>
                                    </td>

                                </form>

                            </tr>

                            <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>