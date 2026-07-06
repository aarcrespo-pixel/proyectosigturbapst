/**
 * ARCHIVO: script.js - Funcionalidad general: menú, tema oscuro, carruseles, info
 */

// Selecciona elementos principales del DOM
const perfil = document.querySelector(".perfil"); // Caja del perfil de usuario
const botonPerfil = document.querySelector(".perfil-btn"); // Botón para abrir/cerrar perfil
const perfilMenu = document.querySelector(".perfil-menu"); // Menú desplegable del perfil
const hamburguesa = document.querySelector(".hamburguesa"); // Botón menú móvil
const menu = document.querySelector(".menu"); // Barra principal de navegación
const body = document.body; // Elemento body del HTML

// Detecta en qué página estamos
const isIndex = !document.querySelector("main.pagina-eventos, main.pagina-turismo, main.pagina-lugares"); // Página principal
const isEventos = !!document.querySelector("main.pagina-eventos"); // Página de eventos
const isTurismo = !!document.querySelector("main.pagina-turismo"); // Página de turismo
const isLugares = !!document.querySelector("main.pagina-lugares"); // Página de lugares

// Elementos de información e interfaz
const infoButton = document.querySelector('.info-btn'); // Botón para mostrar info
const infoPanel = document.querySelector('.info-panel'); // Panel de información
const bottomNavUsuario = document.querySelector('.bottom-nav-item[href$="login.html"]'); // Botón usuario en nav inferior

// Elementos del carrusel de inicio
const slides = document.querySelectorAll('.slide'); // Diapositivas del carrusel
const indicadores = document.querySelectorAll('.indicador'); // Puntos indicadores del carrusel
const flechaIzquierda = document.querySelector('.flecha.izquierda'); // Flecha navegación anterior
const flechaDerecha = document.querySelector('.flecha.derecha'); // Flecha navegación siguiente
let indiceActivo = 0; // Índice de diapositiva activa

// Función auxiliar para establecer estilos CSS
const setStyle = (elemento, propiedad, valor) => {
    if (elemento) {
        elemento.style[propiedad] = valor; // Cambia estilo directo con .style
    }
};

// Clave para guardar tema en localStorage
const storageClaveTema = 'modo-oscuro'; // Persistencia de tema en navegador

// Guarda la preferencia de tema en localStorage
const guardarTema = (oscuro) => {
    localStorage.setItem(storageClaveTema, oscuro ? '1' : '0'); // 1 = oscuro, 0 = claro
};

// Lee la preferencia de tema del navegador
const leerTema = () => {
    return localStorage.getItem(storageClaveTema) === '1'; // true si estaba en modo oscuro
};

// Verifica si un elemento está dentro de la navegación
const esNav = (elemento) => {
    return !!elemento.closest('header, nav, .menu, .bottom-nav, .perfil-menu');
};

// Verifica si es el botón de información
const esInfo = (elemento) => {
    return elemento && elemento.classList.contains('info-btn');
};

// Aplica tema oscuro/claro a toda la página
const aplicarTema = (oscuro, botonModoOscuro) => {
    const colorTexto = oscuro ? '#ffffff' : ''; // Texto blanco en modo oscuro
    const colorFondo = oscuro ? '#050505' : ''; // Fondo negro mate
    const fondoBoton = oscuro ? 'rgba(255,255,255,0.12)' : ''; // Fondo tenue para botones

    setStyle(body, 'backgroundColor', colorFondo); // Aplica fondo al body
    setStyle(body, 'backgroundImage', oscuro ? 'none' : ''); // Saca imagen de fondo
    body.dataset.tema = oscuro ? 'oscuro' : 'claro'; // Marca tema actual para CSS

    if (menu) {
        setStyle(menu, 'backgroundColor', 'transparent'); // Nav siempre transparente
    }

    if (isIndex) {
        // Index no tiene <main>, selecciona textos del body
        const textos = document.querySelectorAll('body h1, body h2, body h3, body h4, body h5, body h6, body p, body span, body a, body label, body li, body small');
        textos.forEach((texto) => {
            if (esNav(texto)) return; // No toca navegación
            if (esInfo(texto)) return; // No toca botón de info
            if (texto.closest('.feature')) return; // Excluye feature blocks
            if (texto.closest('.info-panel')) return; // Mantiene panel oscuro
            setStyle(texto, 'color', colorTexto); // Aplica color de texto
        });

        if (infoPanel) {
            const infoTextos = infoPanel.querySelectorAll('h3, p');
            infoTextos.forEach((texto) => {
                setStyle(texto, 'color', '#111'); // Texto del panel siempre negro
            });
        }
    }

    // Actualiza flechas de carruseles
    const flechas = document.querySelectorAll('.flecha');
    flechas.forEach((flecha) => {
        setStyle(flecha, 'backgroundColor', oscuro ? '#ffffff' : '#111111');
        setStyle(flecha, 'color', oscuro ? '#cccccc' : '#111111');
    });
    
    // Actualiza indicadores de carruseles
    const indicadoresActivos = document.querySelectorAll('main.pagina-eventos .indicador-netflix.activo');
    indicadoresActivos.forEach((indicador) => {
        setStyle(indicador, 'backgroundColor', oscuro ? '#ffffff' : '#111111');
    });

    if (botonModoOscuro) {
        setStyle(botonModoOscuro, 'background', oscuro ? '#ffffff' : '#111111');
        setStyle(botonModoOscuro, 'color', '#111111');
        setStyle(botonModoOscuro, 'borderColor', oscuro ? '#111111' : '#ffffff');
    }

    if (isEventos) {
        // En eventos cambia títulos según tema
        const titulosGaleria = document.querySelectorAll('main.pagina-eventos .galeria-eventos h2');
        titulosGaleria.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });

        const cabecerasSeccion = document.querySelectorAll('main.pagina-eventos .encabezado-categoria h3');
        cabecerasSeccion.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });
    } else if (isTurismo || isLugares) {
        // En turismo/lugares aplica a todos los h2
        const selector = isTurismo ? 'main.pagina-turismo h2' : 'main.pagina-lugares h2';
        const titulos = document.querySelectorAll(selector);
        titulos.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });
    }

    // Actualiza botones del contenido
    const botones = document.querySelectorAll('button');
    botones.forEach((boton) => {
        if (esInfo(boton) || esNav(boton)) {
            setStyle(boton, 'backgroundColor', ''); // No cambia nav ni info
            return;
        }
        setStyle(boton, 'backgroundColor', fondoBoton); // Fondo tenue
    });

    if (botonModoOscuro) {
        botonModoOscuro.textContent = oscuro ? 'Modo Claro' : 'Modo Oscuro'; // Etiqueta correcta
    }
};

// Crea botón de modo oscuro en el menú de perfil
const crearBotonModoOscuro = () => {
    if (!perfilMenu) return; // Sale si no hay menú de perfil

    const botonModoOscuro = document.createElement('button'); // Crea el botón
    botonModoOscuro.type = 'button';
    botonModoOscuro.textContent = 'Modo Oscuro';
    botonModoOscuro.style.width = '100%';
    botonModoOscuro.style.padding = '0.8rem 1rem';
    botonModoOscuro.style.margin = '0.6rem 0 0 0';
    botonModoOscuro.style.border = '1px solid rgba(0,0,0,0.25)';
    botonModoOscuro.style.borderRadius = '0.75rem';
    botonModoOscuro.style.backgroundColor = 'rgba(255,255,255,0.92)';
    botonModoOscuro.style.color = '#111111';
    botonModoOscuro.style.cursor = 'pointer';
    botonModoOscuro.style.fontWeight = '600';
    botonModoOscuro.style.fontFamily = 'Instrument Sans, sans-serif';
    botonModoOscuro.style.textAlign = 'center';
    perfilMenu.appendChild(botonModoOscuro); // Agrega botón al menú

    const tema = { oscuro: leerTema() }; // Lee tema guardado
    aplicarTema(tema.oscuro, botonModoOscuro); // Aplica tema inicial

    // Al hacer click invierte el tema
    botonModoOscuro.addEventListener('click', () => {
        tema.oscuro = !tema.oscuro; // Invierte estado
        aplicarTema(tema.oscuro, botonModoOscuro); // Re-aplica tema
        guardarTema(tema.oscuro); // Guarda preferencia
    });
};

// Activa interacción del perfil
const activarPerfil = () => {
    if (!botonPerfil || !perfil) return;
    botonPerfil.addEventListener('click', () => {
        perfil.classList.toggle('activo'); // Abre/cierra perfil
    });
};

// Activa menú hamburguesa para móviles
const activarHamburguesa = () => {
    if (!hamburguesa || !menu) return;
    hamburguesa.addEventListener('click', () => {
        menu.classList.toggle('activo'); // Abre/cierra menú móvil
    });
};

// Activa botón de usuario en navegación inferior
const activarBotonUsuarioInferior = () => {
    if (!bottomNavUsuario || !perfil) return;
    bottomNavUsuario.addEventListener('click', (evento) => {
        evento.preventDefault();
        perfil.classList.toggle('activo'); // Abre/cierra perfil
    });
};

// Cierra menú/perfil al hacer click afuera
const cerrarClickAfuera = () => {
    document.addEventListener('click', (evento) => {
        const nodo = evento.target;
        if (perfil && botonPerfil && !perfil.contains(nodo) && !botonPerfil.contains(nodo)) {
            perfil.classList.remove('activo'); // Cierra perfil
        }
        if (menu && hamburguesa && !menu.contains(nodo) && !hamburguesa.contains(nodo)) {
            menu.classList.remove('activo'); // Cierra menú
        }
    });
};

// Cambia a una diapositiva específica del carrusel
const cambiarSlide = (nuevoIndice) => {
    if (slides.length === 0 || indicadores.length === 0) return;
    slides[indiceActivo].classList.remove('activo'); // Desactiva anterior
    indicadores[indiceActivo].classList.remove('activo');
    indiceActivo = (nuevoIndice + slides.length) % slides.length; // Calcula índice con ciclo
    slides[indiceActivo].classList.add('activo'); // Activa nueva
    indicadores[indiceActivo].classList.add('activo');
};

// Activa navegación del carrusel de inicio
const activarCarrusel = () => {
    if (flechaIzquierda) {
        flechaIzquierda.addEventListener('click', () => {
            cambiarSlide(indiceActivo - 1); // Anterior
        });
    }
    if (flechaDerecha) {
        flechaDerecha.addEventListener('click', () => {
            cambiarSlide(indiceActivo + 1); // Siguiente
        });
    }
    // Indicadores (puntos)
    indicadores.forEach((indicador, index) => {
        indicador.addEventListener('click', () => {
            cambiarSlide(index); // Navega a índice
        });
    });
};

// Activa toggles de features (blocks expandibles)
const activarToggleFeature = () => {
    const togglesFeature = document.querySelectorAll('.feature-toggle');
    togglesFeature.forEach((toggle) => {
        toggle.addEventListener('click', (evento) => {
            const boton = evento.currentTarget;
            const feature = boton.closest('.feature');
            const expandido = boton.getAttribute('aria-expanded') === 'true';
            const abrir = !expandido; // Invierte estado
            if (!feature) return;
            feature.classList.toggle('activo', abrir);
            feature.classList.toggle('open', abrir);
            boton.classList.toggle('open', abrir);
            boton.setAttribute('aria-expanded', String(abrir)); // Actualiza accesibilidad
        });
    });
};

// Activa toggles del footer
const activarToggleFooter = () => {
    const togglesFooter = document.querySelectorAll('.footer-toggle');
    togglesFooter.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const footerCol = toggle.closest('.footer-col');
            const expandido = toggle.getAttribute('aria-expanded') === 'true';
            const abrir = !expandido;
            if (!footerCol) return;
            footerCol.classList.toggle('open', abrir);
            toggle.classList.toggle('open', abrir);
            toggle.setAttribute('aria-expanded', String(abrir));
        });
    });
};

// Activa panel de información
const activarInfo = () => {
    if (!infoButton || !infoPanel) return;
    infoButton.addEventListener('click', () => {
        const expandido = infoButton.getAttribute('aria-expanded') === 'true';
        const abrir = !expandido;
        infoButton.setAttribute('aria-expanded', String(abrir));
        infoPanel.classList.toggle('open', abrir);
    });
};

// Ejecuta todas las inicializaciones
activarPerfil();
crearBotonModoOscuro();
activarHamburguesa();
activarBotonUsuarioInferior();
cerrarClickAfuera();
activarCarrusel();
activarToggleFeature();
activarToggleFooter();
activarInfo();
