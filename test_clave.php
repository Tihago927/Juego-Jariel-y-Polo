<?php
header('Content-Type: application/json; charset=utf-8');

// Permitir errores en la salida JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Carpeta de logs
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
ini_set('error_log', $log_dir . '/error.log');

// Iniciar captura de buffer
ob_start();

try {
    // Intentar verificar clave
    require 'config.php';
    require 'db.php';
    
    $clave_ingresada = isset($_POST['clave']) ? $_POST['clave'] : '';
    $clave_definida = CLAVE;
    
    // Log para debug
    file_put_contents($log_dir . '/debug.log', "Clave ingresada: '$clave_ingresada'\nClave definida: '$clave_definida'\nComparación: " . ($clave_ingresada === $clave_definida ? 'OK' : 'FAIL') . "\n\n", FILE_APPEND);
    
    if ($clave_ingresada === $clave_definida) {
        $output = json_encode([
            'exito' => true,
            'mensaje' => 'Clave correcta'
        ]);
    } else {
        $output = json_encode([
            'exito' => false,
            'mensaje' => 'Clave incorrecta',
            'debug' => "Ingresada: '$clave_ingresada' vs Esperada: '$clave_definida'"
        ]);
    }
    
    ob_end_clean();
    echo $output;
    
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage(),
        'archivo' => $e->getFile(),
        'linea' => $e->getLine()
    ]);
}
?>
