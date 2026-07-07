const perfil = document.querySelector(".perfil"); // caja del perfil
const botonPerfil = document.querySelector(".perfil-btn"); // boton del perfil
const perfilMenu = document.querySelector(".perfil-menu"); // menu desplegable del perfil
const hamburguesa = document.querySelector(".hamburguesa"); // boton menu movil
const menu = document.querySelector(".menu"); // barra principal del menu
const body = document.body; // cuerpo del html

const isIndex = !document.querySelector("main.pagina-eventos, main.pagina-turismo, main.pagina-lugares"); // pagina principal, usa body en vez de main en el index
const isEventos = !!document.querySelector("main.pagina-eventos"); // pagina de eventos
const isTurismo = !!document.querySelector("main.pagina-turismo"); // pagina de turismo
const isLugares = !!document.querySelector("main.pagina-lugares"); // pagina de lugares

const infoButton = document.querySelector('.info-btn'); // boton info en el index
const infoPanel = document.querySelector('.info-panel'); // panel info en el index
const bottomNavUsuario = document.querySelector('.bottom-nav-item[href$="login.html"]'); // boton usuario en menu inferior

const slides = document.querySelectorAll('.slide'); // slides del carrusel
const indicadores = document.querySelectorAll('.indicador'); // puntos del carrusel
const flechaIzquierda = document.querySelector('.flecha.izquierda'); // flecha anterior
const flechaDerecha = document.querySelector('.flecha.derecha'); // flecha siguiente
let indiceActivo = 0; // index de slide activo

const setStyle = (elemento, propiedad, valor) => {
    if (elemento) {
        elemento.style[propiedad] = valor; // cambia estilo directo con .style
    }
};

const storageClaveTema = 'modo-oscuro'; // guardamos si estamos en dark mode o no

const guardarTema = (oscuro) => {
    localStorage.setItem(storageClaveTema, oscuro ? '1' : '0'); // persistencia en el navegador
};

const leerTema = () => {
    return localStorage.getItem(storageClaveTema) === '1'; // true si ya habiamos dejado oscuro
};

// chequea si el nodo esta dentro de la barra de navegacion o el perfil
const esNav = (elemento) => {
    return !!elemento.closest('header, nav, .menu, .bottom-nav, .perfil-menu');
};

// chequea si es el boton de informacion, para no tocarlo con el modo oscuro
const esInfo = (elemento) => {
    return elemento && elemento.classList.contains('info-btn');
};

const aplicarTema = (oscuro, botonModoOscuro) => {
    const colorTexto = oscuro ? '#ffffff' : ''; // si estamos en modo oscuro, el texto debe ser blanco
    const colorFondo = oscuro ? '#050505' : ''; // fondo negro mate para toda la pagina
    const fondoBoton = oscuro ? 'rgba(255,255,255,0.12)' : ''; // fondo tenue para botones menos importantes

    setStyle(body, 'backgroundColor', colorFondo); // aplico el fondo oscuro al body
    setStyle(body, 'backgroundImage', oscuro ? 'none' : ''); // saco imagen de fondo si hay una
    body.dataset.tema = oscuro ? 'oscuro' : 'claro'; // marca el tema actual para CSS

    if (menu) {
        setStyle(menu, 'backgroundColor', 'transparent'); // la nav siempre queda transparente, no la lleno de oscuro
    }

    if (isIndex) {
        // index no tiene <main>, por eso usamos body para seleccionar los textos
        const textos = document.querySelectorAll('body h1, body h2, body h3, body h4, body h5, body h6, body p, body span, body a, body label, body li, body small');
        textos.forEach((texto) => {
            if (esNav(texto)) return; // no tocamos nada dentro de la barra de navegacion
            if (esInfo(texto)) return; // no tocamos el boton de info y su panel
            if (texto.closest('.feature')) return; // excluyo todo el bloque de feature para que siga en su estilo original
            if (texto.closest('.info-panel')) return; // mantengo el texto del panel siempre oscuro
            setStyle(texto, 'color', colorTexto); // cambio el color de texto al modo oscuro
        });

        if (infoPanel) {
            const infoTextos = infoPanel.querySelectorAll('h3, p');
            infoTextos.forEach((texto) => {
                setStyle(texto, 'color', '#111'); // el texto del panel siempre queda negro
            });
        }
    }

    const flechas = document.querySelectorAll('.flecha');
    flechas.forEach((flecha) => {
        setStyle(flecha, 'backgroundColor', oscuro ? '#ffffff' : '#111111');
        setStyle(flecha, 'color', oscuro ? '#cccccc' : '#111111');
    });
    
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
        // en eventos cambiamos el h2 de la galería y las cabeceras de seccion según el tema
        const titulosGaleria = document.querySelectorAll('main.pagina-eventos .galeria-eventos h2');
        titulosGaleria.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });

        const cabecerasSeccion = document.querySelectorAll('main.pagina-eventos .encabezado-categoria h3');
        cabecerasSeccion.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });

        const indicadoresActivos = document.querySelectorAll('main.pagina-eventos .indicador-netflix.activo');
        indicadoresActivos.forEach((indicador) => {
            setStyle(indicador, 'backgroundColor', oscuro ? '#ffffff' : '#111111');
        });
    } else if (isTurismo || isLugares) {
        // en turismo/lugares aplico el cambio a todos los h2, porque esas paginas no tienen excepcion especial
        const selector = isTurismo
            ? 'main.pagina-turismo h2'
            : 'main.pagina-lugares h2';
        const titulos = document.querySelectorAll(selector);
        titulos.forEach((titulo) => {
            setStyle(titulo, 'color', colorTexto);
        });
    }

    const botones = document.querySelectorAll('button');
    botones.forEach((boton) => {
        if (esInfo(boton) || esNav(boton)) {
            setStyle(boton, 'backgroundColor', '');
            return; // no cambiamos botones de nav ni info
        }
        setStyle(boton, 'backgroundColor', fondoBoton); // damos un fondo tenue a los botones del contenido
    });

    if (botonModoOscuro) {
        botonModoOscuro.textContent = oscuro ? 'Modo Claro' : 'Modo Oscuro'; // muestro la etiqueta correcta en el boton
    }
};

const crearBotonModoOscuro = () => {
    if (!perfilMenu) return; // si no hay menu de perfil no tiene sentido

    const botonModoOscuro = document.createElement('button'); // boton para activar modo
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
    perfilMenu.appendChild(botonModoOscuro);

    const tema = { oscuro: leerTema() }; // leo si ya habia activado dark mode antes y guardo el estado inicial
    aplicarTema(tema.oscuro, botonModoOscuro); // aplico ese estado al cargar la pagina

    botonModoOscuro.addEventListener('click', () => {
        tema.oscuro = !tema.oscuro; // al click invierto el tema
        aplicarTema(tema.oscuro, botonModoOscuro); // reaplico el tema actualizado
        guardarTema(tema.oscuro); // guardo la eleccion para que persista entre paginas
    });
};

const activarPerfil = () => {
    if (!botonPerfil || !perfil) return;
    botonPerfil.addEventListener('click', () => {
        perfil.classList.toggle('activo'); // abre/cierra el perfil
    });
};

const activarHamburguesa = () => {
    if (!hamburguesa || !menu) return;
    hamburguesa.addEventListener('click', () => {
        menu.classList.toggle('activo'); // abre/cierra menu movil
    });
};

const activarBotonUsuarioInferior = () => {
    if (!bottomNavUsuario || !perfil) return;
    bottomNavUsuario.addEventListener('click', (evento) => {
        evento.preventDefault();
        perfil.classList.toggle('activo');
    });
};

const cerrarClickAfuera = () => {
    document.addEventListener('click', (evento) => {
        const nodo = evento.target;
        if (perfil && botonPerfil && !perfil.contains(nodo) && !botonPerfil.contains(nodo)) {
            perfil.classList.remove('activo');
        }
        if (menu && hamburguesa && !menu.contains(nodo) && !hamburguesa.contains(nodo)) {
            menu.classList.remove('activo');
        }
    });
};

const cambiarSlide = (nuevoIndice) => {
    if (slides.length === 0 || indicadores.length === 0) return;
    slides[indiceActivo].classList.remove('activo');
    indicadores[indiceActivo].classList.remove('activo');
    indiceActivo = (nuevoIndice + slides.length) % slides.length; // Calcula índice con ciclo
    slides[indiceActivo].classList.add('activo'); // Activa nueva
    indicadores[indiceActivo].classList.add('activo');
};

const activarCarrusel = () => {
    if (flechaIzquierda) {
        flechaIzquierda.addEventListener('click', () => {
            cambiarSlide(indiceActivo - 1);
        });
    }
    if (flechaDerecha) {
        flechaDerecha.addEventListener('click', () => {
            cambiarSlide(indiceActivo + 1);
        });
    }
    indicadores.forEach((indicador, index) => {
        indicador.addEventListener('click', () => {
            cambiarSlide(index);
        });
    });
};

const activarToggleFeature = () => {
    const togglesFeature = document.querySelectorAll('.feature-toggle');
    togglesFeature.forEach((toggle) => {
        toggle.addEventListener('click', (evento) => {
            const boton = evento.currentTarget;
            const feature = boton.closest('.feature');
            const expandido = boton.getAttribute('aria-expanded') === 'true';
            const abrir = !expandido;
            if (!feature) return;
            feature.classList.toggle('activo', abrir);
            feature.classList.toggle('open', abrir);
            boton.classList.toggle('open', abrir);
            boton.setAttribute('aria-expanded', String(abrir));
        });
    });
};

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

const activarInfo = () => {
    if (!infoButton || !infoPanel) return;
    infoButton.addEventListener('click', () => {
        const expandido = infoButton.getAttribute('aria-expanded') === 'true';
        const abrir = !expandido;
        infoButton.setAttribute('aria-expanded', String(abrir));
        infoPanel.classList.toggle('open', abrir);
    });
};

activarPerfil();
crearBotonModoOscuro();
activarHamburguesa();
activarBotonUsuarioInferior();
cerrarClickAfuera();
activarCarrusel();
activarToggleFeature();
activarToggleFooter();
activarInfo();
