const lat = -31.387491347822888;
const lon = -57.97041480342443;
// determina los valores de latitud y longitud para el lugar

const API_KEY = "d7867090f2eccb9f15f94d808abaf192";

function ITH(temperatura, humedad) { //calcula el ith con la formula q nos dio el profe
    return temperatura - (0.55 - 0.0055 * humedad) * (temperatura - 14.5);
} 

// async function = funcion que usa internet
async function obtenerClima(lat, lon) {
    const url =
        `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${API_KEY}&units=metric`;

    const response = await fetch(url); // le pide los datos a la api y espera respuesta
    const data = await response.json();
    return {
        temperatura: data.main.temp,
        humedad: data.main.humidity // devuelve los valores de temperatura y humedad en objetos
    };
}

async function cargarclima() {
    const widget = document.getElementById("clima-widget"); // agarra el cosito del clima en el html

    if (!widget) return; //si existe intenta lo de abajo

    try {
        const clima = await obtenerClima(lat, lon); // corre la funcion de arriba y guarda los datos en una variable
        const ith = ITH(clima.temperatura, clima.humedad); // variable que calcula el ith con la funcion ITH
        widget.innerHTML = `
            <span class="clima-temp">ITH: ${ith.toFixed(1)}</span>   
        `; // pone el ith en el html
    } catch (error) { // si el try no esta funcionando muestra esto
        widget.innerHTML = '<span class="clima-cargando">ITH no disponible</span>';
    }
}

document.addEventListener("DOMContentLoaded", cargarclima); // hace todo lo de arriba cuando la pagina termina de cargar