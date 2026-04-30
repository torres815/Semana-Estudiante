<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) { exit("Acceso denegado"); }

// --- CASO 1: RESETEAR TODO ---
if (isset($_POST['accion']) && $_POST['accion'] == 'resetear_todo') {
    try {
        $conexion->query("DELETE FROM alumnos"); // Borra todos los registros de la tabla
        header("Location: asistencia.php?msg=reset_ok");
        exit();
    } catch (Exception $e) {
        header("Location: asistencia.php?msg=error");
        exit();
    }
}

// --- CASO 2: CARGAR CSV ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_lista'])) {
    $curso_id = $_POST['curso_id'];
    $archivo = $_FILES['archivo_lista']['tmp_name'];

    if (($handle = fopen($archivo, "r")) !== FALSE) {
        try {
            $conexion->beginTransaction();

            // Limpiamos solo los alumnos de este curso antes de cargar los nuevos
            $stmtDel = $conexion->prepare("DELETE FROM alumnos WHERE curso_id = ?");
            $stmtDel->execute([$curso_id]);

            $stmtIns = $conexion->prepare("INSERT INTO alumnos (curso_id, nombre_apellido, presente) VALUES (?, ?, 1)");

            while (($linea = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Como tu CSV tiene los nombres en la primera columna:
                $nombre = isset($linea[0]) ? trim($linea[0]) : '';
                
                if (!empty($nombre)) {
                    $stmtIns->execute([$curso_id, $nombre]);
                }
            }

            $conexion->commit();
            header("Location: asistencia.php?msg=ok");
            
        } catch (Exception $e) {
            $conexion->rollBack();
            header("Location: asistencia.php?msg=error");
        }
        fclose($handle);
    }
}
?>