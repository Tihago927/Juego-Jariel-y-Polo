<?php
// Configurar headers y errores
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Crear carpeta de logs si no existe
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
ini_set('error_log', $log_dir . '/error.log');

// Iniciar buffer para capturar cualquier salida accidental
ob_start();

try {
    require 'config.php';
    require 'db.php';
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    die(json_encode([
        'exito' => false,
        'mensaje' => 'Error al cargar archivos: ' . $e->getMessage(),
        'linea' => $e->getLine()
    ]));
}

// Funciones de utilidad
function obtenerLetra($promedio) {
    $promedio = (float)$promedio;
    if ($promedio >= 9) return "A";
    if ($promedio >= 8) return "B";
    if ($promedio >= 7) return "C";
    if ($promedio >= 6) return "D";
    return "F";
}

function respuestaJSON($exito, $mensaje, $datos = []) {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje,
        'datos' => $datos
    ]);
    exit;
}

// ENDPOINTS
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

try {
    switch ($accion) {
        case 'verificarClave':
            verificarClave();
            break;
            
        case 'obtenerNiveles':
            obtenerNiveles();
            break;
            
        case 'guardarEstudiante':
            guardarEstudiante();
            break;
            
        case 'obtenerEstudiantes':
            obtenerEstudiantes();
            break;
            
        case 'descargarCalificaciones':
            descargarCalificaciones();
            break;
            
        default:
            respuestaJSON(false, 'Acción no válida: ' . $accion);
    }
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error en la acción: ' . $e->getMessage(),
        'linea' => $e->getLine()
    ]);
}

// FUNCIONES DE ENDPOINTS
function verificarClave() {
    $clave_ingresada = isset($_POST['clave']) ? $_POST['clave'] : '';
    $clave_definida = CLAVE;
    
    // Log para debug
    $log_dir = __DIR__ . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    file_put_contents($log_dir . '/debug.log', "API - Clave ingresada: '$clave_ingresada' vs Esperada: '$clave_definida'\n", FILE_APPEND);
    
    if ($clave_ingresada === $clave_definida) {
        respuestaJSON(true, 'Clave correcta');
    } else {
        respuestaJSON(false, 'Clave incorrecta. Ingresada: ' . $clave_ingresada);
    }
}

function obtenerNiveles() {
    global $NIVELES;
    respuestaJSON(true, 'Niveles obtenidos', $NIVELES);
}

function guardarEstudiante() {
    global $db;
    
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
        $puntos = isset($data['puntos']) ? intval($data['puntos']) : 0;
        $estrellas = isset($data['estrellas']) ? intval($data['estrellas']) : 0;
        
        if (!$nombre) {
            respuestaJSON(false, 'El nombre es requerido');
        }
        
        $promedio = ($estrellas / 10 * 10);
        $letra = obtenerLetra($promedio);
        
        $id = $db->agregarEstudiante($nombre, $puntos, $estrellas, $promedio, $letra);
        
        respuestaJSON(true, 'Estudiante guardado', [
            'id' => $id,
            'nombre' => $nombre,
            'promedio' => $promedio,
            'letra' => $letra,
            'estrellas' => $estrellas,
            'puntos' => $puntos,
            'fecha' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        respuestaJSON(false, 'Error al guardar estudiante: ' . $e->getMessage());
    }
}

function obtenerEstudiantes() {
    global $db;
    
    try {
        $estudiantes = $db->obtenerEstudiantes();
        respuestaJSON(true, 'Estudiantes obtenidos', $estudiantes);
    } catch (Exception $e) {
        respuestaJSON(false, 'Error al obtener estudiantes: ' . $e->getMessage());
    }
}

function descargarCalificaciones() {
    global $db;
    
    $estudiantes = $db->obtenerEstudiantes();
    
    if (empty($estudiantes)) {
        respuestaJSON(false, 'No hay estudiantes registrados');
    }
    
    // Crear CSV
    $csv = "\"Unidad Educativa La Independencia\"\n";
    $csv .= "\"Evaluación de Inglés - 1.º Básica\"\n\n";
    $csv .= "N°,Nombre,Promedio,Calificación,Fecha y hora\n";
    
    foreach ($estudiantes as $i => $e) {
        $csv .= ($i + 1) . ",\"" . $e['nombre'] . "\"," . $e['promedio'] . "," . 
                $e['letra'] . ",\"" . $e['fecha'] . "\"\n";
    }
    
    // Descargar archivo
    header('Content-Type: text/csv;charset=utf-8');
    header('Content-Disposition: attachment;filename="Calificaciones_Ingles_1B.csv"');
    echo $csv;
    exit;
}
?>
