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
    <title>Sorteador de Jugadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; color: #fff; }
        .card-sorteo { background: rgba(255, 255, 255, 0.95); color: #333; border-radius: 20px; border: none; }
        .winner-item { 
            background: #f8f9fa; border-left: 5px solid #6f42c1; 
            font-size: 1.5rem; font-weight: bold; margin-bottom: 10px;
            animation: popIn 0.5s ease;
        }
        @keyframes popIn { 0% { transform: scale(0.8); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .btn-sorteo { background: #6f42c1; color: white; border-radius: 30px; padding: 15px 30px; font-weight: bold; border: none; }
        .btn-sorteo:hover { background: #59359a; transform: translateY(-2px); color: white; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">🎲 Sorteador de Jugadores</h1>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill">Volver al Panel</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-sorteo shadow-lg p-4">
                    <form action="sorteo.php" method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Curso</label>
                            <select name="curso_id" class="form-select" required>
                                <option value="">Seleccione un curso...</option>
                                <?php foreach($cursos as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['nombre_curso'] ?> - <?= $c['division'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">¿Cuántos?</label>
                            <input type="number" name="cantidad" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" name="sortear" class="btn btn-sorteo w-100">SORTEAR</button>
                        </div>
                    </form>

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sortear'])) {
                        $c_id = (int)$_POST['curso_id'];
                        $cant = (int)$_POST['cantidad'];

                        // --- LA PARTE CRÍTICA PARA EL ERROR 1064 ---
                        $stmt = $conexion->prepare("SELECT nombre_apellido FROM alumnos 
                                                    WHERE curso_id = :id AND presente = 1 
                                                    ORDER BY RAND() LIMIT :limite");
                        
                        // Vinculamos como ENTEROS explícitamente
                        $stmt->bindValue(':id', $c_id, PDO::PARAM_INT);
                        $stmt->bindValue(':limite', $cant, PDO::PARAM_INT);
                        
                        $stmt->execute();
                        $ganadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        echo "<div class='mt-5'>";
                        if ($ganadores) {
                            echo "<h3 class='text-center mb-4 text-primary font-monospace'>✨ JUGADORES ✨</h3>";
                            foreach ($ganadores as $g) {
                                echo "<div class='winner-item p-3 shadow-sm text-center'>" . htmlspecialchars($g['nombre_apellido']) . "</div>";
                            }
                        } else {
                            echo "<div class='alert alert-warning text-center'>No se encontraron alumnos presentes para este curso. <br> <a href='asistencia.php'>Cargar asistencia aquí</a></div>";
                        }
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>