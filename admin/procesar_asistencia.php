<?php
require '../config/db.php';

if ($_FILES['archivo_lista']['size'] > 0) {
    $curso_id = $_POST['curso_id'];
    $archivo = $_FILES['archivo_lista']['tmp_name'];
    $handle = fopen($archivo, "r");

    // Limpiamos asistencias anteriores de ese curso si es necesario
    $conexion->prepare("UPDATE alumnos SET presente = 0 WHERE curso_id = ?")->execute([$curso_id]);

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $nombre = $data[0];
        // Insertamos o actualizamos presencia
        $stmt = $conexion->prepare("INSERT INTO alumnos (curso_id, nombre_apellido, presente) 
                                    VALUES (?, ?, 1) 
                                    ON DUPLICATE KEY UPDATE presente = 1");
        $stmt->execute([$curso_id, $nombre]);
    }
    fclose($handle);
    header("Location: asistencia.php?msg=ok");
}