const lat = -31.387491347822888;
const lon = -57.97041480342443;
const API_KEY = "d7867090f2eccb9f15f94d808abaf192";

function obtenerEmojiClima(estado, icono) {
    const esNoche = icono?.endsWith('n');
    if (/rain|drizzle/i.test(estado)) return '🌧️';
    if (/snow/i.test(estado)) return '❄️';
    if (/thunderstorm/i.test(estado)) return '⛈️';
    if (/clouds/i.test(estado)) return esNoche ? '☁️' : '⛅';
    if (/mist|fog|haze|smoke|dust|sand|ash|tornado/i.test(estado)) return '🌫️';
    return esNoche ? '🌙' : '☀️';
}

async function obtenerClima(lat, lon) {
    const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric`;
    const response = await fetch(url);
    if (!response.ok) throw new Error(`OpenWeather respondió ${response.status}`);
    const data = await response.json();
    return {
        temperatura: data.main.temp,
        humedad: data.main.humidity,
        estado: data.weather?.[0]?.main || 'Clear',
        icono: data.weather?.[0]?.icon || '01d'
    };
}

function calcularIth(temperatura, humedad) {
    const ith = temperatura - (0.55 - humedad / 100) * (temperatura - 14.5);
    return Number(ith.toFixed(1));
}

async function cargarclima() {
    const icono = document.getElementById('clima-icon');
    const temp = document.getElementById('clima-temp');
    const humedad = document.getElementById('clima-humedad');
    const ith = document.getElementById('clima-ith');

    if (!icono || !temp || !humedad || !ith) return;

    try {
        const { temperatura, humedad: humedadActual, estado, icono: iconoClima } = await obtenerClima(lat, lon);
        const emoji = obtenerEmojiClima(estado, iconoClima);
        const ithCalculado = calcularIth(temperatura, humedadActual);

        icono.textContent = emoji;
        temp.textContent = `${temperatura.toFixed(1)}°C`;
        humedad.textContent = `Humedad: ${humedadActual}%`;
        ith.textContent = `ITH: ${ithCalculado}`;
    } catch (error) {
        icono.textContent = '☁️';
        temp.textContent = '--°C';
        humedad.textContent = 'Humedad: --%';
        ith.textContent = 'ITH: --';
    }
}

document.addEventListener('DOMContentLoaded', cargarclima);