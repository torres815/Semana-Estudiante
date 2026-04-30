<?php
session_start();
require '../config/db.php';

// Seguridad
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../login.php"); 
    exit(); 
}

$cursos = $conexion->query("SELECT * FROM cursos ORDER BY nombre_curso ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sorteador de Jugadores</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    background:linear-gradient(135deg,#4f46e5 0%, #7c3aed 45%, #111827 100%);
}

.nav-custom{
    background:rgba(0,0,0,.18);
    backdrop-filter:blur(10px);
}

.panel-card{
    border:none;
    border-radius:26px;
    overflow:hidden;
    background:rgba(255,255,255,.96);
}

.hero-box{
    background:linear-gradient(135deg,#6d28d9,#4f46e5);
}

.form-control,
.form-select{
    border-radius:14px;
    padding:12px 14px;
}

.form-control:focus,
.form-select:focus{
    box-shadow:none;
    border-color:#6f42c1;
}

.btn-sorteo{
    background:linear-gradient(135deg,#6d28d9,#4f46e5);
    color:#fff;
    border:none;
    border-radius:14px;
    font-weight:800;
    padding:12px;
}

.btn-sorteo:hover{
    color:#fff;
    transform:translateY(-2px);
}

.winner-item{
    background:#f8f9fa;
    border-left:6px solid #6d28d9;
    border-radius:14px;
    font-size:1.2rem;
    font-weight:700;
    margin-bottom:12px;
    animation:popIn .45s ease;
}

.stat-card{
    border:none;
    border-radius:18px;
}

@keyframes popIn{
    0%{opacity:0;transform:translateY(15px) scale(.95);}
    100%{opacity:1;transform:translateY(0) scale(1);}
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark nav-custom shadow-sm mb-4">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
            🎲 Sorteador Oficial
        </a>

        <div class="d-flex gap-2">

            <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4">
                ⚙️ Panel Admin
            </a>

            <a href="../index.php" class="btn btn-light btn-sm rounded-pill px-4 fw-semibold">
                🏆 Inicio
            </a>

        </div>

    </div>
</nav>

<div class="container pb-5">

    <!-- TOP INFO -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Sistema</small>
                    <h6 class="fw-bold mb-0">Selección Aleatoria</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Filtro</small>
                    <h6 class="fw-bold mb-0 text-success">Solo Presentes</h6>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Modo</small>
                    <h6 class="fw-bold mb-0 text-primary">Automático</h6>
                </div>
            </div>
        </div>

    </div>

    <!-- PANEL CENTRAL -->
    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card panel-card shadow-lg">

                <!-- HEADER -->
                <div class="hero-box text-white text-center py-5 px-4">

                    <h1 class="fw-bold mb-2">🎯 Sorteador de Jugadores</h1>

                    <p class="mb-0 text-white-50">
                        Selecciona alumnos presentes al azar para participar
                    </p>

                </div>

                <!-- BODY -->
                <div class="card-body p-4 p-md-5">

                    <form action="sorteo.php" method="POST" class="row g-3">

                        <!-- Curso -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Curso</label>

                            <select name="curso_id" class="form-select" required>
                                <option value="">Seleccione un curso...</option>

                                <?php foreach($cursos as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= $c['nombre_curso'] ?> - <?= $c['division'] ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <!-- Cantidad -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">¿Cuántos?</label>

                            <input type="number"
                                   name="cantidad"
                                   class="form-control"
                                   value="1"
                                   min="1">
                        </div>

                        <!-- Botón -->
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit"
                                    name="sortear"
                                    class="btn btn-sorteo w-100 shadow-sm">
                                🎲 SORTEAR
                            </button>
                        </div>

                    </form>

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sortear'])) {

                        $c_id = (int)$_POST['curso_id'];
                        $cant = (int)$_POST['cantidad'];

                        $stmt = $conexion->prepare("SELECT nombre_apellido FROM alumnos 
                                                    WHERE curso_id = :id AND presente = 1 
                                                    ORDER BY RAND() LIMIT :limite");

                        $stmt->bindValue(':id', $c_id, PDO::PARAM_INT);
                        $stmt->bindValue(':limite', $cant, PDO::PARAM_INT);

                        $stmt->execute();
                        $ganadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        echo "<div class='mt-5'>";

                        if ($ganadores) {

                            echo "<div class='text-center mb-4'>";
                            echo "<h3 class='fw-bold text-primary'>🏆 Jugadores Seleccionados</h3>";
                            echo "<small class='text-muted'>Resultado del sorteo actual</small>";
                            echo "</div>";

                            foreach ($ganadores as $index => $g) {

                                echo "<div class='winner-item p-3 shadow-sm text-center'>";
                                echo "🎉 " . htmlspecialchars($g['nombre_apellido']);
                                echo "</div>";
                            }

                        } else {

                            echo "
                            <div class='alert alert-warning border-0 rounded-4 text-center'>
                                ⚠️ No se encontraron alumnos presentes para este curso.<br>
                                <a href='asistencia.php' class='fw-semibold text-decoration-none'>
                                    Ir a cargar asistencia
                                </a>
                            </div>";
                        }

                        echo "</div>";
                    }
                    ?>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>