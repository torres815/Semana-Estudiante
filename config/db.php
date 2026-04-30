<?php
// Configuración de la base de datos
$host = "10.0.24.101";
$db_name = "jxkhkuhk_gestiondepuntos";
$username = "jxkhkuhk_gestiondepuntos";
$password = "mc[&gbvP_!Y$6.fn";
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