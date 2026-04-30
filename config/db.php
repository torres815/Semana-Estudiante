<?php
// Configuración de la base de datos
$host = "localhost";
$db_name = "semana_estudiante";
$username = "root";
$password = "root";
try {
    // Creamos la conexión usando PDO
    $conexion = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    
    // Configuramos para que PDO lance excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Opcional: Descomenta la siguiente línea para probar que funciona
    // echo "Conexión exitosa"; 
    
} catch (PDOException $e) {
    // Si hay un error, lo mostramos
    die("Error de conexión: " . $e->getMessage());
}
?>