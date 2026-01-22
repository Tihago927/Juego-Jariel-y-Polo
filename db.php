<?php
require 'config.php';

class Database {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO('sqlite:' . DB_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->inicializarDB();
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error de conexión a base de datos: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    
    private function inicializarDB() {
        try {
            // Crear tabla de niveles
            $sql1 = "CREATE TABLE IF NOT EXISTS niveles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                descripcion TEXT,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql1);
            
            // Crear tabla de juegos
            $sql2 = "CREATE TABLE IF NOT EXISTS juegos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nivel_id INTEGER NOT NULL,
                nombre_es TEXT NOT NULL,
                nombre_en TEXT NOT NULL,
                color TEXT,
                imagen TEXT,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (nivel_id) REFERENCES niveles(id)
            )";
            $this->pdo->exec($sql2);
            
            // Crear tabla de estudiantes
            $sql3 = "CREATE TABLE IF NOT EXISTS estudiantes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                puntos INTEGER DEFAULT 0,
                estrellas INTEGER DEFAULT 0,
                promedio REAL DEFAULT 0,
                letra TEXT DEFAULT 'F',
                fecha TEXT NOT NULL,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->pdo->exec($sql3);
            
            // Crear tabla de respuestas
            $sql4 = "CREATE TABLE IF NOT EXISTS respuestas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                estudiante_id INTEGER NOT NULL,
                nivel INTEGER NOT NULL,
                juego INTEGER NOT NULL,
                respuesta_correcta INTEGER,
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
            )";
            $this->pdo->exec($sql4);
            
            // Crear tabla de evaluaciones
            $sql5 = "CREATE TABLE IF NOT EXISTS evaluaciones (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                estudiante_id INTEGER NOT NULL,
                puntos_totales INTEGER,
                estrellas_totales INTEGER,
                promedio REAL,
                letra TEXT,
                fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
            )";
            $this->pdo->exec($sql5);
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error creando tablas: ' . $e->getMessage()
            ]);
            exit;
        }
    }
    
    public function agregarEstudiante($nombre, $puntos, $estrellas, $promedio, $letra) {
        $fecha = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO estudiantes (nombre, puntos, estrellas, promedio, letra, fecha) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nombre, $puntos, $estrellas, $promedio, $letra, $fecha]);
        
        return $this->pdo->lastInsertId();
    }
    
    public function obtenerEstudiantes() {
        $sql = "SELECT * FROM estudiantes ORDER BY creado_en DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPDO() {
        return $this->pdo;
    }
}

// Crear instancia global
$db = new Database();
?>
