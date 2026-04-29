<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$curso_id = $_POST['curso_id'];
$cantidad = $_POST['cantidad'];
$dia      = $_POST['dia'];
$motivo   = $_POST['motivo'];
$user_id  = $_SESSION['user_id']; // Quién lo carga

try {
    $sql = "INSERT INTO puntos (curso_id, usuario_id, cantidad, motivo, dia_semana) 
            VALUES (:curso, :usuario, :cantidad, :motivo, :dia)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        'curso'    => $curso_id,
        'usuario'  => $user_id,
        'cantidad' => $cantidad,
        'motivo'   => $motivo,
        'dia'      => $dia
    ]);

    header("Location: dashboard.php?success=1");
} catch (Exception $e) {
    header("Location: dashboard.php?error=1");
}