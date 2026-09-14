const weatherCoordinates = {
    latitud: -31.387491347822888,
    longitud: -57.97041480342443
};
const weatherApiKey = 'd7867090f2eccb9f15f94d808abaf192';

/*
 * Esta función consulta el clima actual de Salto, transforma la respuesta de
 * OpenWeather en los cuatro datos que necesita la cápsula y deja el cálculo
 * del ITH en el navegador para actualizar el widget sin recargar la vista.
 */
async function obtenerDatosClima() {
    const { latitud, longitud } = weatherCoordinates;
    const endpoint = `https://api.openweathermap.org/data/2.5/weather?lat=${latitud}&lon=${longitud}&appid=${weatherApiKey}&units=metric&lang=es`;
    const respuesta = await fetch(endpoint);
    if (!respuesta.ok) throw new Error(`La API meteorológica respondió ${respuesta.status}`);
    const datos = await respuesta.json();
    return {
        temperatura: Number(datos.main?.temp),
        humedad: Number(datos.main?.humidity),
        estado: datos.weather?.[0]?.description || 'Condición no disponible',
        icono: datos.weather?.[0]?.icon || '01d'
    };
}

function calcularIndiceIth(temperatura, humedad) {
    return Number((temperatura - (0.55 - humedad / 100) * (temperatura - 14.5)).toFixed(1));
}

function obtenerIconoClima(icono) {
    if (icono.startsWith('09') || icono.startsWith('10')) return '🌧️';
    if (icono.startsWith('11')) return '⛈️';
    if (icono.startsWith('13')) return '❄️';
    if (icono.startsWith('50')) return '🌫️';
    if (icono.startsWith('02') || icono.startsWith('03') || icono.startsWith('04')) return '⛅';
    return icono.endsWith('n') ? '🌙' : '☀️';
}

function clasificarIth(ith) {
    if (ith >= 30) return { etiqueta: 'Alerta', clase: 'weather-alert' };
    if (ith >= 26) return { etiqueta: 'Precaución', clase: 'weather-caution' };
    return { etiqueta: 'Confort', clase: 'weather-comfort' };
}

/*
 * El DOM se actualiza nodo por nodo para conservar la estructura accesible:
 * icono y estado describen el cielo, temperatura y humedad muestran medidas,
 * e ITH recibe una clase de color que comunica confort, precaución o alerta.
 */
async function cargarWidgetClima() {
    const widget = document.getElementById('weather-widget');
    if (!widget) return;

    try {
        const clima = await obtenerDatosClima();
        const ith = calcularIndiceIth(clima.temperatura, clima.humedad);
        const nivel = clasificarIth(ith);
        document.getElementById('weather-icon').textContent = obtenerIconoClima(clima.icono);
        document.getElementById('weather-condition').textContent = clima.estado;
        document.getElementById('weather-temperature').textContent = `${clima.temperatura.toFixed(1)}°C`;
        document.getElementById('weather-humidity').textContent = `Humedad: ${clima.humedad}%`;
        const indicadorIth = document.getElementById('weather-ith');
        indicadorIth.textContent = `ITH: ${ith} · ${nivel.etiqueta}`;
        indicadorIth.className = `weather-ith ith-badge ${nivel.clase}`;
    } catch (error) {
        widget.classList.add('weather-unavailable');
        document.getElementById('weather-condition').textContent = 'Clima no disponible';
        document.getElementById('weather-temperature').textContent = '--°C';
        document.getElementById('weather-humidity').textContent = 'Humedad: --%';
        document.getElementById('weather-ith').textContent = 'ITH: --';
    }
}

document.addEventListener('DOMContentLoaded', cargarWidgetClima);