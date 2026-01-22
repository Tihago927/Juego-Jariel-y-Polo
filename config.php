<?php
// Configuración de la aplicación
define('DB_PATH', __DIR__ . '/DatosNinos.db');
define('CLAVE', '1234');
$NIVELES = [
    [
        'nombre' => 'Colores',
        'juegos' => [
            ['es' => 'ROJO', 'en' => 'RED', 'color' => 'red', 'img' => 'img/red.png'],
            ['es' => 'AZUL', 'en' => 'BLUE', 'color' => 'blue', 'img' => 'img/blue.png'],
            ['es' => 'AMARILLO', 'en' => 'YELLOW', 'color' => 'gold', 'img' => 'img/yellow.png']
        ]
    ],
    [
        'nombre' => 'Números',
        'juegos' => [
            ['es' => 'UNO', 'en' => 'ONE', 'color' => 'orange', 'img' => 'img/uno.jpg'],
            ['es' => 'DOS', 'en' => 'TWO', 'color' => 'purple', 'img' => 'img/dos.jpg'],
            ['es' => 'TRES', 'en' => 'THREE', 'color' => 'brown', 'img' => 'img/tres.jpg']
        ]
    ],
    [
        'nombre' => 'Animales',
        'juegos' => [
            ['es' => 'PERRO', 'en' => 'DOG', 'color' => '#a0522d', 'img' => 'img/perro.jpg'],
            ['es' => 'GATO', 'en' => 'CAT', 'color' => 'gray', 'img' => 'img/gato.jpg'],
            ['es' => 'PEZ', 'en' => 'FISH', 'color' => '#00bfff', 'img' => 'img/pez.jpg']
        ]
    ],
    [
        'nombre' => 'Frutas',
        'juegos' => [
            ['es' => 'MANZANA', 'en' => 'APPLE', 'color' => 'red', 'img' => 'img/manzana.jpg'],
            ['es' => 'PLÁTANO', 'en' => 'BANANA', 'color' => 'yellow', 'img' => 'img/banana.jpg'],
            ['es' => 'NARANJA', 'en' => 'ORANGE', 'color' => 'orange', 'img' => 'img/naranja.jpg']
        ]
    ],
    [
        'nombre' => 'Cielo',
        'juegos' => [
            ['es' => 'LUNA', 'en' => 'MOON', 'color' => 'silver', 'img' => 'img/luna.jpg'],
            ['es' => 'SOL', 'en' => 'SUN', 'color' => 'gold', 'img' => 'img/sol.jpg'],
            ['es' => 'ESTRELLA', 'en' => 'STAR', 'color' => 'yellow', 'img' => 'img/estrella.jpg']
        ]
    ]
];
?>
