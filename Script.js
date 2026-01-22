
// Variables globales
let niveles = [];
let estudiantes = [];
let nivelActual = 0, juegoActual = 0;
let puntos = 0, estrellas = 0;
let correcto = null;
let estudiante = "";

console.log('Script.js cargado correctamente');

// Cargar niveles al iniciar
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado');
    cargarNiveles();
    cargarEstudiantes();
});

const sonidoBien = new Audio("sonidos/aplausos.mp3");
const sonidoMal = new Audio("sonidos/error.mp3");

// FUNCIONES DE API
async function cargarNiveles() {
    try {
        const response = await fetch('api.php?accion=obtenerNiveles');
        const result = await response.json();
        if (result.exito) {
            niveles = result.datos;
        }
    } catch (error) {
        console.error('Error cargando niveles:', error);
    }
}

async function cargarEstudiantes() {
    try {
        const response = await fetch('api.php?accion=obtenerEstudiantes');
        const result = await response.json();
        if (result.exito) {
            estudiantes = result.datos;
            actualizarHistorial();
        }
    } catch (error) {
        console.error('Error cargando estudiantes:', error);
    }
}

function verificarClave(){
    console.log('Botón presionado');
    const clave = document.getElementById('claveInput').value;
    console.log('Clave ingresada:', clave);
    
    if (!clave) {
        alert('Por favor ingresa la clave');
        return;
    }
    
    const formData = new FormData();
    formData.append('clave', clave);
    
    console.log('Enviando fetch a api.php');
    
    fetch('api.php?accion=verificarClave', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Respuesta recibida, status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Texto recibido:', text);
        const result = JSON.parse(text);
        console.log('JSON parseado:', result);
        if (result.exito) {
            document.getElementById('clavePantalla').style.display = "none";
        } else {
            alert("Clave incorrecta");
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        alert('Error: ' + error.message);
    });
}
function irRegistro(){
    portada.style.display="none";
    registro.style.display="flex";
}
function comenzar(){
    estudiante=nombre.value.trim();
    if(!estudiante) return alert("Escribe tu nombre");
    registro.style.display="none";
    juego.style.display="block";
    saludo.innerText="👋 Hola "+estudiante;
    mostrarPregunta();
}
function hablar(texto){
    const voz=new SpeechSynthesisUtterance(texto);
    voz.lang="es-ES"; voz.rate=0.8;
    speechSynthesis.cancel();
    speechSynthesis.speak(voz);
}
function mostrarPregunta(){
    let nivel=niveles[nivelActual];
    correcto=nivel.juegos[juegoActual];
    textoPregunta.innerText=correcto.es+" / "+correcto.en;
    dibujo.style.backgroundImage = `url(${correcto.img})`;
    dibujo.style.backgroundColor = correcto.color;
    dibujo.classList.remove("acierto");
    opciones.innerHTML="";
    hablar(correcto.es);

    nivel.juegos.forEach((c,i)=>{
        let b=document.createElement("button");
        b.className="colorBtn";
        b.style.background=c.color;
        b.innerText=c.es+" / "+c.en;
        b.style.animationDelay = `${i*0.2}s`;
        b.onclick=()=>evaluar(c);
        opciones.appendChild(b);
    });
    resultadoFinal.style.display="none";
}

function evaluar(c){
    if(c!==correcto){
        hablar("Intenta otra vez");
        sonidoMal.play();
        return;
    }
    puntos++;
    estrellas = Math.min(puntos,10);
    dibujo.classList.add("acierto");
    puntaje.innerText="Puntos: "+puntos;
    hablar("¡Muy bien!");
    
    setTimeout(()=>{
        juegoActual++;
        if(juegoActual>=niveles[nivelActual].juegos.length){
            juegoActual=0;
            nivelActual++;
        }
        nivelActual>=niveles.length ? finalizarEvaluacion() : mostrarPregunta();
    },700);
}

// FUNCION LETRA
function obtenerLetra(promedio){
    promedio = parseFloat(promedio);
    if(promedio >= 9) return "A";
    if(promedio >= 8) return "B";
    if(promedio >= 7) return "C";
    if(promedio >= 6) return "D";
    return "F";
}

function finalizarEvaluacion(){
    let promedio = (estrellas/10*10).toFixed(2);
    let letra = obtenerLetra(promedio);

    // Guardar en la base de datos
    const datos = {
        nombre: estudiante,
        puntos: puntos,
        estrellas: estrellas
    };
    
    fetch('api.php?accion=guardarEstudiante', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(result => {
        if (result.exito) {
            estudiantes.push(result.datos);
            
            resultadoFinal.innerHTML = 
            `🎉 ${estudiante}<br>
            📊 Promedio: ${promedio} / 10<br>
            🏅 Calificación: ${letra}
            <br><br>

            <br><br>
            <button onclick="nuevoEstudiante()">➡ Registrar otro estudiante</button>`;

            resultadoFinal.style.display="block";
            actualizarHistorial();

            // Reiniciar variables del juego
            nivelActual=0; 
            juegoActual=0; 
            puntos=0; 
            estrellas=0; 
            estudiante="";
        }
    })
    .catch(error => console.error('Error:', error));
}

// Función para volver al registro de otro estudiante
function nuevoEstudiante(){
    resultadoFinal.style.display="none";
    juego.style.display="none";
    registro.style.display="flex";
    nombre.value=""; // limpiar el input
    nombre.focus();
}



function actualizarHistorial(){
    tarjetasEstudiantes.innerHTML="";
    estudiantes.forEach((e,i)=>{
        tarjetasEstudiantes.innerHTML+=`
        <div class="estudianteCard">
            <h4>${i+1}. ${e.nombre}</h4>
            <p>📊 Promedio: ${e.promedio} / 10</p>
            <p>🏅 Calificación: ${e.letra}</p>
            <p>🕒 ${e.fecha}</p>
        </div>`;
    });
}

// DESCARGAR CALIFICACIONES
function descargarCalificaciones() {
    if(estudiantes.length === 0){
        alert("No hay estudiantes registrados");
        return;
    }
    
    // Redirigir a la descarga desde PHP
    window.location.href = 'api.php?accion=descargarCalificaciones';
}
