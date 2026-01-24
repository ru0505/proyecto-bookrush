<?php
include 'conexion.php';

// Verificar estructura de tabla usuarios
$sql = "DESCRIBE usuarios;";
$result = $conn->query($sql);

echo "=== Estructura tabla usuarios ===\n";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . "\n";
}

echo "\n=== Primeros usuarios ===\n";
$sql2 = "SELECT ID, NOMBRE, email FROM usuarios LIMIT 3;";
$result2 = $conn->query($sql2);
while ($row = $result2->fetch_assoc()) {
    echo "ID: " . $row['ID'] . " | NOMBRE: " . $row['NOMBRE'] . " | email: " . ($row['email'] ?? 'NULL') . "\n";
}
?>
