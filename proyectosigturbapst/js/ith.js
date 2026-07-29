const lat = -31.387491347822888;
const lon = -57.97041480342443;
/* determina los valores de latitud y longitud para el lugar */

const API_KEY = "d7867090f2eccb9f15f94d808abaf192";

function ITH(temperatura, humedad) { /* calcula el ith con la formula q nos dio el profe */
    return temperatura - (0.55 - 0.0055 * humedad) * (temperatura - 14.5);
} 

/* async function = funcion que usa internet */
async function obtenerClima(lat, lon) {
    const url =
        `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric`;

    const response = await fetch(url); /* le pide los datos a la api y espera respuesta */
    const data = await response.json();
    return {
        temperatura: data.main.temp,
        humedad: data.main.humidity,
        estado: data.weather?.[0]?.main || 'Clear',
        icono: data.weather?.[0]?.icon || '01d'
    };
}

function obtenerEmojiClima(estado, icono) {
    const esNoche = icono?.endsWith('n');
    if (/rain|drizzle/i.test(estado)) return '🌧️';
    if (/snow/i.test(estado)) return '❄️';
    if (/thunderstorm/i.test(estado)) return '⛈️';
    if (/clouds/i.test(estado)) return esNoche ? '☁️' : '⛅';
    if (/mist|fog|haze|smoke|dust|sand|ash|tornado/i.test(estado)) return '🌫️';
    return esNoche ? '🌙' : '☀️';
}

async function cargarclima() {
    const widget = document.getElementById("clima-widget"); /* agarra el cosito del clima en el html */

    if (!widget) return; /* si no hay widget, no hace nada */

    try {
        const { temperatura, humedad, estado, icono } = await obtenerClima(lat, lon); /* trae datos de la API */
        const ith = ITH(temperatura, humedad); /* calcula el índice de temperatura-humedad */
        const emoji = obtenerEmojiClima(estado, icono);

        widget.innerHTML = `
            <span class="clima-icon">${emoji}</span>
            <span class="clima-temp">${temperatura.toFixed(1)}°C</span>
            <span class="clima-humedad">Humedad: ${humedad}%</span>
            <span class="clima-ith">ITH: ${ith.toFixed(1)}</span>
        `;
    } catch (error) { /* si la API falla, muestra mensaje simple */
        widget.innerHTML = '<span class="clima-cargando">Clima no disponible</span>';
    }
}

document.addEventListener("DOMContentLoaded", cargarclima); /* hace todo lo de arriba cuando la pagina termina de cargar */