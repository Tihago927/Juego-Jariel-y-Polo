<?php
header('Content-Type: application/json');

echo json_encode([
    'exito' => true,
    'mensaje' => 'PHP y JSON funcionan correctamente'
]);
?>
