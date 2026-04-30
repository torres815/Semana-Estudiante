<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'master') {
    exit("Acceso denegado");
}

$accion = $_POST['accion'];

if ($accion == 'cambiar_visibilidad') {
    $nuevo_estado = $_POST['estado_actual'] == 1 ? 0 : 1;
    $stmt = $conexion->prepare("UPDATE sistema_config SET tabla_visible = ? WHERE id = 1");
    if($stmt->execute([$nuevo_estado])) {
        header("Location: master.php?msg=config_ok");
    }
}

if ($accion == 'registrar_usuario') {
    $user = $_POST['nuevo_username'];
    $pass = password_hash($_POST['nuevo_password'], PASSWORD_DEFAULT); // Encriptación segura
    $rol  = $_POST['nuevo_rol'];

    try {
        $stmt = $conexion->prepare("INSERT INTO usuarios (username, password, rol) VALUES (?, ?, ?)");
        $stmt->execute([$user, $pass, $rol]);
        header("Location: master.php?msg=user_ok");
    } catch (Exception $e) {
        header("Location: master.php?msg=error");
    }
}
// --- NUEVA ACCIÓN: ACTUALIZAR PUNTOS INICIALES ---
if ($accion == 'actualizar_puntos_iniciales') {
    $curso_id = $_POST['curso_id'];
    $puntos_ini = (int)$_POST['puntos_ini'];

    try {
        $stmt = $conexion->prepare("UPDATE cursos SET puntos_iniciales = ? WHERE id = ?");
        $stmt->execute([$puntos_ini, $curso_id]);
        header("Location: master.php?msg=puntos_ok");
    } catch (Exception $e) {
        header("Location: master.php?msg=error");
    }
}