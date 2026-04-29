<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // trim() elimina espacios accidentales al principio o final
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    // Buscamos al usuario por nombre
    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE username = :user");
    $stmt->execute(['user' => $user]);
    $usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuarioEncontrado) {
        // El usuario existe, ahora verificamos la contraseña
        if (password_verify($pass, $usuarioEncontrado['password'])) {
            // ¡ÉXITO!
            $_SESSION['user_id'] = $usuarioEncontrado['id'];
            $_SESSION['username'] = $usuarioEncontrado['username'];
            $_SESSION['rol'] = $usuarioEncontrado['rol'];

            header("Location: admin/dashboard.php");
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: login.php?error=pass_incorrecta");
            exit();
        }
    } else {
        // Usuario no existe
        header("Location: login.php?error=usuario_no_existe");
        exit();
    }
}