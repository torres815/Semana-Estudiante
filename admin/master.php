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
    <title>Panel Master - Control Total</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger mb-4">
    <div class="container">
        <span class="navbar-brand">🛡️ PANEL MASTER ADM</span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">Volver al Panel de Carga</a>
    </div>
</nav>

<div class="container">
    <?php if($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">Visibilidad de la Tabla</div>
                <div class="card-body text-center">
                    <p>Estado actual: <strong><?php echo $config['tabla_visible'] ? 'PÚBLICO' : 'OCULTO'; ?></strong></p>
                    <form action="procesar_master.php" method="POST">
                        <input type="hidden" name="accion" value="cambiar_visibilidad">
                        <input type="hidden" name="estado_actual" value="<?php echo $config['tabla_visible']; ?>">
                        
                        <?php if($config['tabla_visible']): ?>
                            <button type="submit" class="btn btn-warning w-100">Ocultar Tabla para Usuarios</button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success w-100">Hacer Tabla Pública</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">Registrar Nuevo Usuario (Admin)</div>
                <div class="card-body">
                    <form action="procesar_master.php" method="POST">
                        <input type="hidden" name="accion" value="registrar_usuario">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nombre de Usuario</label>
                                <input type="text" name="nuevo_username" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Contraseña</label>
                                <input type="password" name="nuevo_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Rol del Usuario</label>
                            <select name="nuevo_rol" class="form-select">
                                <option value="admin">Administrador (Carga puntos)</option>
                                <option value="master">Master (Control total)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </form>

                    <hr>
                    <h5>Usuarios Registrados</h5>
                    <table class="table table-sm">
                        <thead>
                            <tr><th>ID</th><th>Usuario</th><th>Rol</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($usuarios as $u): ?>
                                <tr>
                                    <td><?php echo $u['id']; ?></td>
                                    <td><?php echo $u['username']; ?></td>
                                    <td><span class="badge <?php echo $u['rol']=='master'?'bg-danger':'bg-secondary'; ?>"><?php echo $u['rol']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="row mt-5">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Gestión de Cursos y Puntos Iniciales</div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
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
                                <td><?= $cm['nombre_curso'] ?></td>
                                <td><?= $cm['division'] ?></td>
                                <td>
                                    <input type="number" name="puntos_ini" class="form-control form-control-sm" value="<?= $cm['puntos_iniciales'] ?>">
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
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
        </div>
    </div>
</div>

</body>
</html>