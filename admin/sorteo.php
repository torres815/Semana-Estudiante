<?php
require '../config/db.php';
$cursos = $conexion->query("SELECT * FROM cursos")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-purple text-white" style="background: #6f42c1;">
            <h4>🎲 Sorteador de Jugadores</h4>
        </div>
        <div class="card-body">
            <form action="sorteo.php" method="POST" class="mb-4">
                <div class="row">
                    <div class="col-md-5">
                        <label>Curso</label>
                        <select name="curso_id" class="form-select">
                            <?php foreach($cursos as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['nombre_curso'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>¿Cuántos sorteamos?</label>
                        <input type="number" name="cantidad" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">¡Sortear!</button>
                    </div>
                </div>
            </form>

            <?php
            if ($_POST) {
                $c_id = $_POST['curso_id'];
                $cant = $_POST['cantidad'];

                // Buscamos alumnos PRESENTES de ese curso y los ordenamos al azar (RAND())
                $stmt = $conexion->prepare("SELECT nombre_apellido FROM alumnos 
                                            WHERE curso_id = ? AND presente = 1 
                                            ORDER BY RAND() LIMIT ?");
                $stmt->execute([$c_id, (int)$cant]);
                $ganadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($ganadores) {
                    echo "<h3 class='text-center text-success'>Elegidos:</h3><ul class='list-group'>";
                    foreach ($ganadores as $g) {
                        echo "<li class='list-group-item text-center fw-bold h4'>" . $g['nombre_apellido'] . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<div class='alert alert-warning'>No hay alumnos marcados como presentes en este curso.</div>";
                }
            }
            ?>
        </div>
    </div>
</div>