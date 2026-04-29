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
    <title>Admin - Carga de Puntos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Panel de Control: <?php echo $_SESSION['username']; ?></span>
        <div>
            <?php if($_SESSION['rol'] == 'master'): ?>
                <a href="master.php" class="btn btn-warning btn-sm">Panel Master</a>
            <?php endif; ?>
            <a href="../procesar_logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Cargar Nuevos Puntos</h4>
                </div>
                <div class="card-body">
                    
                    <?php if($mensaje): ?>
                        <div class="alert alert-info"><?php echo $mensaje; ?></div>
                    <?php endif; ?>

                    <form action="procesar_puntos.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Seleccionar Curso</label>
                            <select name="curso_id" class="form-select" required>
                                <option value="">Elija un curso...</option>
                                <?php foreach($cursos as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo $c['nombre_curso'] . " - " . $c['division']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Puntos (ej: 10 o -5)</label>
                                <input type="number" name="cantidad" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Día</label>
                                <select name="dia" class="form-select" required>
                                    <option value="Lunes">Lunes</option>
                                    <option value="Martes">Martes</option>
                                    <option value="Miércoles">Miércoles</option>
                                    <option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motivo / Descripción</label>
                            <textarea name="motivo" class="form-control" rows="3" placeholder="Ej: Ganadores en voley, conducta en el acto, etc." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Guardar Puntos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>