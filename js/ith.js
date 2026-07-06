/**
 * ARCHIVO: ith.js - Calcula clima y índice de temperatura-humedad (ITH) para Salto
 */

// Coordenadas geográficas de Salto, Uruguay
const lat = -31.387491347822888; // Latitud
const lon = -57.97041480342443; // Longitud

// Clave API para OpenWeatherMap
const API_KEY = "d7867090f2eccb9f15f94d808abaf192";

// Calcula el Índice de Temperatura-Humedad
function ITH(temperatura, humedad) {
    return temperatura - (0.55 - 0.0055 * humedad) * (temperatura - 14.5); // Fórmula ITH
}

// Obtiene datos de clima de la API
async function obtenerClima(lat, lon) {
    const url =
        `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric`;

    const response = await fetch(url); // Realiza petición HTTP
    const data = await response.json(); // Parsea respuesta JSON
    return {
        temperatura: data.main.temp, // Temperatura actual
        humedad: data.main.humidity, // Humedad relativa
        estado: data.weather?.[0]?.main || 'Clear', // Condición climática
        icono: data.weather?.[0]?.icon || '01d' // Código de icono
    };
}

// Retorna emoji según condición climática
function obtenerEmojiClima(estado, icono) {
    const esNoche = icono?.endsWith('n'); // Verifica si es de noche
    if (/rain|drizzle/i.test(estado)) return '🌧️'; // Lluvia
    if (/snow/i.test(estado)) return '❄️'; // Nieve
    if (/thunderstorm/i.test(estado)) return '⛈️'; // Tormenta
    if (/clouds/i.test(estado)) return esNoche ? '☁️' : '⛅'; // Nublado
    if (/mist|fog|haze|smoke|dust|sand|ash|tornado/i.test(estado)) return '🌫️'; // Bruma
    return esNoche ? '🌙' : '☀️'; // Noche o día despejado
}

// Carga el widget de clima en la página
async function cargarclima() {
    const widget = document.getElementById("clima-widget"); // Obtiene contenedor

    if (!widget) return; // Sale si no existe el widget

    try {
        // Obtiene datos de clima
        const { temperatura, humedad, estado, icono } = await obtenerClima(lat, lon);
        const ith = ITH(temperatura, humedad); // Calcula ITH
        const emoji = obtenerEmojiClima(estado, icono); // Obtiene emoji

        // Inserta HTML en el widget
        widget.innerHTML = `
            <span class="clima-icon">${emoji}</span>
            <span class="clima-temp">${temperatura.toFixed(1)}°C</span>
            <span class="clima-humedad">Humedad: ${humedad}%</span>
            <span class="clima-ith">ITH: ${ith.toFixed(1)}</span>
        `;
    } catch (error) {
        // Si la API falla muestra mensaje de error
        widget.innerHTML = '<span class="clima-cargando">Clima no disponible</span>';
    }
}

// Ejecuta la carga del clima cuando carga completamente el DOM
document.addEventListener("DOMContentLoaded", cargarclima);