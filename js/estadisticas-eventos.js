(() => {
    const panel = document.getElementById('estadisticas-eventos');
    const selectorPeriodo = document.getElementById('estadisticas-periodo');
    const estado = document.getElementById('estadisticas-estado');
    if (!panel || !selectorPeriodo || !estado) return;

    const api = panel.dataset.endpoint;
    const svgNamespace = 'http://www.w3.org/2000/svg';
    const formatoNumero = new Intl.NumberFormat('es-UY', { maximumFractionDigits: 1 });
    let solicitudActual;

    function elementoSvg(etiqueta, atributos = {}, texto = '') {
        const elemento = document.createElementNS(svgNamespace, etiqueta);
        Object.entries(atributos).forEach(([nombre, valor]) => elemento.setAttribute(nombre, String(valor)));
        if (texto !== '') elemento.textContent = texto;
        return elemento;
    }

    function mostrarEstado(mensaje, tipo = '') {
        estado.textContent = mensaje;
        if (tipo) estado.dataset.estado = tipo;
        else delete estado.dataset.estado;
    }

    function cambiarComparacion(tarjeta, cambio, actual, anterior) {
        if (!tarjeta) return;
        const etiqueta = tarjeta.querySelector('.estadistica-card__comparacion');
        if (!etiqueta) return;
        etiqueta.removeAttribute('data-tendencia');
        if (anterior === null || anterior === undefined) {
            etiqueta.textContent = 'Sin período comparable';
            return;
        }
        if (cambio === null || cambio === undefined) {
            etiqueta.textContent = actual > 0 && anterior === 0
                ? 'Nuevo período con actividad'
                : 'Sin período comparable';
            return;
        }
        const signo = cambio > 0 ? '+' : '';
        const indicador = cambio > 0 ? '↗' : cambio < 0 ? '↘' : '→';
        etiqueta.textContent = `${indicador} ${signo}${formatoNumero.format(cambio)}% frente al período anterior`;
        if (cambio > 0) etiqueta.dataset.tendencia = 'sube';
        if (cambio < 0) etiqueta.dataset.tendencia = 'baja';
    }

    function actualizarIndicadores(datos) {
        const valores = {
            totalEventos: datos.totalEventos,
            eventosActivos: datos.eventosActivos,
            visitas: datos.visitas,
            usuariosGuardaron: datos.usuariosGuardaron,
            registrados: datos.registrados,
            promedioVisitas: datos.promedioVisitas,
            conversion: datos.conversion,
            crecimiento: datos.eventosCreadosPeriodo
        };
        Object.entries(valores).forEach(([clave, valor]) => {
            const tarjeta = panel.querySelector(`[data-indicador="${clave}"]`);
            if (!tarjeta) return;
            const salida = tarjeta.querySelector('.estadistica-card__valor');
            if (!salida) return;
            salida.textContent = valor === null || valor === undefined
                ? '—'
                : clave === 'conversion'
                    ? `${formatoNumero.format(valor)}%`
                    : formatoNumero.format(valor);
        });

        cambiarComparacion(
            panel.querySelector('[data-indicador="visitas"]'),
            datos.cambioVisitas,
            datos.visitas,
            datos.visitasAnteriores
        );
        cambiarComparacion(
            panel.querySelector('[data-indicador="usuariosGuardaron"]'),
            datos.cambioGuardados,
            datos.usuariosGuardaron,
            datos.usuariosGuardaronAnteriores
        );
        cambiarComparacion(
            panel.querySelector('[data-indicador="registrados"]'),
            datos.cambioRegistrados,
            datos.registradosPeriodo,
            datos.registradosPeriodoAnterior
        );
        cambiarComparacion(
            panel.querySelector('[data-indicador="crecimiento"]'),
            datos.cambioEventos,
            datos.eventosCreadosPeriodo,
            datos.eventosCreadosAnteriores
        );

        const conversionNota = panel.querySelector('[data-indicador="conversion"] .estadistica-card__nota');
        if (conversionNota) {
            conversionNota.textContent = datos.conversion === null
                ? 'Sin suficientes visitas identificables para calcularla'
                : `${formatoNumero.format(datos.conversionMuestra)} usuarios visitaron eventos`;
        }
    }

    function addLine(parent, x1, y1, x2, y2, className = 'estadistica-grilla') {
        parent.append(elementoSvg('line', { x1, y1, x2, y2, class: className }));
    }

    function addText(parent, x, y, texto, className = 'estadistica-eje', anchor = 'middle') {
        parent.append(elementoSvg('text', { x, y, class: className, 'text-anchor': anchor }, texto));
    }

    function escalaConteos(valores) {
        const datoMaximo = Math.max(0, ...valores);
        const paso = datoMaximo <= 4 ? 1 : Math.ceil(datoMaximo / 4);
        return {
            maximo: Math.max(1, Math.ceil(datoMaximo / paso) * paso),
            paso
        };
    }

    function resolverRecta(valores) {
        const n = valores.length;
        if (n < 2) return null;
        const mediaX = (n - 1) / 2;
        const mediaY = valores.reduce((total, valor) => total + valor, 0) / n;
        let numerador = 0;
        let denominador = 0;
        valores.forEach((valor, indice) => {
            numerador += (indice - mediaX) * (valor - mediaY);
            denominador += (indice - mediaX) ** 2;
        });
        const pendiente = denominador ? numerador / denominador : 0;
        return { pendiente, intercepto: mediaY - pendiente * mediaX };
    }

    function resolverCuadratica(valores) {
        if (valores.length < 6) return null;
        const n = valores.length;
        const sumas = [0, 0, 0, 0, 0];
        let sumaY = 0;
        let sumaXY = 0;
        let sumaX2Y = 0;
        valores.forEach((valor, indice) => {
            const x = indice / Math.max(1, n - 1);
            const x2 = x * x;
            sumas[0] += 1;
            sumas[1] += x;
            sumas[2] += x2;
            sumas[3] += x2 * x;
            sumas[4] += x2 * x2;
            sumaY += valor;
            sumaXY += x * valor;
            sumaX2Y += x2 * valor;
        });

        const matriz = [
            [sumas[0], sumas[1], sumas[2], sumaY],
            [sumas[1], sumas[2], sumas[3], sumaXY],
            [sumas[2], sumas[3], sumas[4], sumaX2Y]
        ];
        for (let columna = 0; columna < 3; columna++) {
            let pivote = columna;
            for (let fila = columna + 1; fila < 3; fila++) {
                if (Math.abs(matriz[fila][columna]) > Math.abs(matriz[pivote][columna])) pivote = fila;
            }
            if (Math.abs(matriz[pivote][columna]) < 1e-10) return null;
            [matriz[columna], matriz[pivote]] = [matriz[pivote], matriz[columna]];
            const divisor = matriz[columna][columna];
            for (let celda = columna; celda < 4; celda++) matriz[columna][celda] /= divisor;
            for (let fila = 0; fila < 3; fila++) {
                if (fila === columna) continue;
                const factor = matriz[fila][columna];
                for (let celda = columna; celda < 4; celda++) {
                    matriz[fila][celda] -= factor * matriz[columna][celda];
                }
            }
        }
        const [c, b, a] = matriz.map(fila => fila[3]);
        const promedio = sumaY / n;
        const total = valores.reduce((suma, valor) => suma + (valor - promedio) ** 2, 0);
        const residual = valores.reduce((suma, valor, indice) => {
            const x = indice / Math.max(1, n - 1);
            return suma + (valor - (a * x * x + b * x + c)) ** 2;
        }, 0);
        const vertex = a ? -b / (2 * a) : -1;
        if (!Number.isFinite(a) || Math.abs(a) < 1e-8 || vertex <= 0 || vertex >= 1 || total === 0) return null;
        if (1 - residual / total < .7) return null;
        return { a, b, c };
    }

    function leyendaLinea(incluirCuadratica) {
        const leyenda = document.createElement('div');
        leyenda.className = 'estadistica-leyenda';
        [
            ['Visitas reales', ''],
            ['Tendencia lineal', 'leyenda-tendencia'],
            ['Tangente local', 'leyenda-tangente'],
            ...(incluirCuadratica ? [['Modelo cuadrático estimado', 'leyenda-cuadratica']] : [])
        ].forEach(([texto, clase]) => {
            const item = document.createElement('span');
            if (clase) item.className = clase;
            const muestra = document.createElement('i');
            item.append(muestra, document.createTextNode(texto));
            leyenda.append(item);
        });
        return leyenda;
    }

    function leyendaActividad() {
        const leyenda = document.createElement('div');
        leyenda.className = 'estadistica-leyenda';
        [
            ['Eventos creados', 'leyenda-eventos'],
            ['Tendencia de eventos', 'leyenda-tendencia-eventos'],
            ['Visitas', 'leyenda-visitas'],
            ['Tendencia de visitas', 'leyenda-tendencia-visitas']
        ].forEach(([texto, clase]) => {
            const item = document.createElement('span');
            item.className = clase;
            const muestra = document.createElement('i');
            item.append(muestra, document.createTextNode(texto));
            leyenda.append(item);
        });
        return leyenda;
    }

    function dibujarLinea(contenedor, etiquetas, series, opciones = {}) {
        contenedor.replaceChildren();
        if (!etiquetas.length || !series.length) {
            contenedor.textContent = 'No hay registros para mostrar en este período.';
            contenedor.className = 'estadistica-grafica estadistica-grafica__vacio';
            return null;
        }
        contenedor.className = 'estadistica-grafica';
        const valoresTotales = series.flatMap(serie => serie.valores);
        const { maximo, paso } = escalaConteos(valoresTotales);
        const minX = 52;
        const maxX = 742;
        const minY = 20;
        const maxY = 206;
        const ancho = maxX - minX;
        const alto = maxY - minY;
        const posicionX = indice => etiquetas.length === 1
            ? minX + ancho / 2
            : minX + indice * ancho / (etiquetas.length - 1);
        const posicionY = valor => maxY - (valor / maximo) * alto;
        const svg = elementoSvg('svg', { viewBox: '0 0 760 250', role: 'presentation', focusable: 'false' });
        const rejilla = elementoSvg('g');
        for (let valor = maximo; valor >= 0; valor -= paso) {
            const y = minY + ((maximo - valor) / maximo) * alto;
            addLine(rejilla, minX, y, maxX, y);
            addText(rejilla, minX - 9, y + 4, formatoNumero.format(valor), 'estadistica-eje', 'end');
        }
        const cantidadEtiquetas = Math.min(7, etiquetas.length);
        for (let indice = 0; indice < cantidadEtiquetas; indice++) {
            const posicion = cantidadEtiquetas === 1
                ? 0
                : Math.round(indice * (etiquetas.length - 1) / (cantidadEtiquetas - 1));
            addText(rejilla, posicionX(posicion), 232, etiquetas[posicion]);
        }
        svg.append(rejilla);

        series.forEach((serie, serieIndice) => {
            const puntos = serie.valores.map((valor, indice) => [posicionX(indice), posicionY(valor)]);
            const ruta = puntos.map(([x, y], indice) => `${indice ? 'L' : 'M'} ${x} ${y}`).join(' ');
            const lineaSerie = elementoSvg('path', {
                d: ruta,
                class: serie.color ? 'estadistica-linea-secundaria' : 'estadistica-linea-real'
            });
            if (serie.color) lineaSerie.style.stroke = serie.color;
            svg.append(lineaSerie);
            puntos.forEach(([x, y], indice) => {
                const punto = elementoSvg('circle', {
                    cx: x,
                    cy: y,
                    r: serieIndice === 0 ? 3.2 : 2.4,
                    class: 'estadistica-punto',
                    tabindex: '0'
                });
                if (serie.color) punto.style.stroke = serie.color;
                const titulo = elementoSvg('title', {}, `${etiquetas[indice]}: ${formatoNumero.format(serie.valores[indice])} ${serie.unidad || ''}`.trim());
                punto.append(titulo);
                svg.append(punto);
            });
        });

        if (opciones.tendencias) {
            series.forEach(serie => {
                if (serie.valores.length < 2 || serie.valores.every(valor => valor === serie.valores[0])) return;
                const recta = resolverRecta(serie.valores);
                if (!recta) return;
                const yInicio = posicionY(recta.intercepto);
                const yFinal = posicionY(recta.intercepto + recta.pendiente * (serie.valores.length - 1));
                const tendencia = elementoSvg('path', {
                    d: `M ${minX} ${Math.max(minY, Math.min(maxY, yInicio))} L ${maxX} ${Math.max(minY, Math.min(maxY, yFinal))}`,
                    class: 'estadistica-linea-tendencia'
                });
                if (serie.color) tendencia.style.stroke = serie.color;
                svg.append(tendencia);
            });
            contenedor.prepend(leyendaActividad());
        }

        const valores = series[0].valores;
        if (opciones.analisis && valores.length > 1 && valores.some(valor => valor !== valores[0])) {
            const recta = resolverRecta(valores);
            if (recta) {
                const yInicio = posicionY(recta.intercepto);
                const yFinal = posicionY(recta.intercepto + recta.pendiente * (valores.length - 1));
                svg.append(elementoSvg('path', {
                    d: `M ${minX} ${Math.max(minY, Math.min(maxY, yInicio))} L ${maxX} ${Math.max(minY, Math.min(maxY, yFinal))}`,
                    class: 'estadistica-linea-tendencia'
                }));

                if (valores.length > 1) {
                    const ultimoIndice = valores.length - 1;
                    const pendienteLocal = valores[ultimoIndice] - valores[ultimoIndice - 1];
                    const inicioTangente = Math.max(0, ultimoIndice - Math.min(2, ultimoIndice));
                    const finTangente = Math.min(valores.length - 1, ultimoIndice + 1.5);
                    const yTangenteInicio = valores[ultimoIndice] + pendienteLocal * (inicioTangente - ultimoIndice);
                    const yTangenteFinal = valores[ultimoIndice] + pendienteLocal * (finTangente - ultimoIndice);
                    svg.append(elementoSvg('path', {
                        d: `M ${posicionX(inicioTangente)} ${posicionY(Math.max(0, yTangenteInicio))} L ${posicionX(finTangente)} ${posicionY(Math.max(0, yTangenteFinal))}`,
                        class: 'estadistica-linea-tangente'
                    }));
                }
                const unidad = etiquetas.length <= 7 && etiquetas.some(etiqueta => /\d{1,2}\s/.test(etiqueta))
                    ? 'día'
                    : 'mes';
                if (opciones.tasa) {
                    const tasa = recta.pendiente;
                    opciones.tasa.textContent = `Tendencia ≈ ${tasa > 0 ? '+' : ''}${formatoNumero.format(tasa)} visitas / ${unidad}`;
                }
            }

            const maxValor = Math.max(...valores);
            const minValor = Math.min(...valores);
            const indiceMaximo = valores.indexOf(maxValor);
            const indiceMinimo = valores.indexOf(minValor);
            if (maxValor !== minValor) {
                [
                    [indiceMaximo, maxValor, 'estadistica-punto estadistica-punto--max', 'Máximo'],
                    [indiceMinimo, minValor, 'estadistica-punto estadistica-punto--min', 'Mínimo']
                ].forEach(([indice, valor, clase, nombre]) => {
                    const punto = elementoSvg('circle', {
                        cx: posicionX(indice),
                        cy: posicionY(valor),
                        r: 5.5,
                        class: clase
                    });
                    punto.append(elementoSvg('title', {}, `${nombre}: ${etiquetas[indice]}, ${formatoNumero.format(valor)} visitas`));
                    svg.append(punto);
                    addText(svg, posicionX(indice), posicionY(valor) - 10, nombre, 'estadistica-eje');
                });
            }

            const cuadratica = resolverCuadratica(valores);
            if (cuadratica) {
                const muestras = 40;
                const ruta = [];
                for (let indice = 0; indice <= muestras; indice++) {
                    const fraccion = indice / muestras;
                    const xDatos = fraccion * (valores.length - 1);
                    const xNormalizado = fraccion;
                    const estimado = cuadratica.a * xNormalizado ** 2 + cuadratica.b * xNormalizado + cuadratica.c;
                    ruta.push(`${indice ? 'L' : 'M'} ${posicionX(xDatos)} ${posicionY(Math.max(0, estimado))}`);
                }
                svg.append(elementoSvg('path', { d: ruta.join(' '), class: 'estadistica-linea-cuadratica' }));
                contenedor.prepend(leyendaLinea(true));
            } else {
                contenedor.prepend(leyendaLinea(false));
            }
        }
        contenedor.append(svg);

        if (opciones.analisis && valoresTotales.every(valor => valor === 0)) {
            const nota = document.createElement('p');
            nota.className = 'estadistica-panel__nota';
            nota.textContent = 'No hay visitas registradas en este período; se muestra la escala real desde cero.';
            contenedor.append(nota);
        }
        return { maximo: Math.max(...(series[0]?.valores ?? [0])), minimo: Math.min(...(series[0]?.valores ?? [0])) };
    }

    function dibujarBarrasMensuales(contenedor, etiquetas, valores) {
        contenedor.replaceChildren();
        if (!etiquetas.length) {
            contenedor.textContent = 'No hay eventos creados en este período.';
            contenedor.className = 'estadistica-grafica estadistica-grafica__vacio';
            return;
        }
        contenedor.className = 'estadistica-grafica';
        const svg = elementoSvg('svg', { viewBox: '0 0 760 250', role: 'presentation', focusable: 'false' });
        const { maximo: mayor, paso } = escalaConteos(valores);
        const left = 46;
        const top = 18;
        const bottom = 205;
        const ancho = 690;
        const espacio = ancho / etiquetas.length;
        for (let valor = mayor; valor >= 0; valor -= paso) {
            const y = top + ((mayor - valor) / mayor) * (bottom - top);
            addLine(svg, left, y, left + ancho, y);
            addText(svg, left - 8, y + 4, formatoNumero.format(valor), 'estadistica-eje', 'end');
        }
        etiquetas.forEach((etiqueta, indice) => {
            const alto = (valores[indice] / mayor) * (bottom - top);
            const x = left + indice * espacio + espacio * .22;
            const barra = elementoSvg('rect', {
                x,
                y: bottom - alto,
                width: Math.max(3, espacio * .56),
                height: alto,
                rx: 5,
                fill: '#d49a32'
            });
            barra.append(elementoSvg('title', {}, `${etiqueta}: ${formatoNumero.format(valores[indice])} eventos creados`));
            svg.append(barra);
            if (etiquetas.length <= 8 || indice % Math.ceil(etiquetas.length / 8) === 0 || indice === etiquetas.length - 1) {
                addText(svg, x + Math.max(3, espacio * .28), 230, etiqueta);
            }
        });
        contenedor.append(svg);
    }

    function dibujarPopulares(contenedor, eventos) {
        contenedor.replaceChildren();
        if (!eventos.length) {
            contenedor.textContent = 'No hay eventos para comparar.';
            contenedor.className = 'estadistica-grafica estadistica-grafica__vacio';
            return;
        }
        contenedor.className = 'estadistica-grafica';
        const leyenda = document.createElement('div');
        leyenda.className = 'estadistica-panel__leyenda';
        [['Visitas', 'estadistica-barra__relleno'], ['Guardados', 'estadistica-barra__relleno estadistica-barra__relleno--guardados']].forEach(([texto, clase]) => {
            const elemento = document.createElement('span');
            const muestra = document.createElement('i');
            muestra.className = clase;
            elemento.append(muestra, document.createTextNode(texto));
            leyenda.append(elemento);
        });
        const barras = document.createElement('div');
        barras.className = 'estadistica-barras';
        const maximo = Math.max(1, ...eventos.flatMap(evento => [evento.visitas, evento.guardados]));
        eventos.forEach(evento => {
            const fila = document.createElement('div');
            fila.className = 'estadistica-barra__fila';
            const nombre = document.createElement('span');
            nombre.className = 'estadistica-barra__nombre';
            nombre.textContent = evento.titulo;
            nombre.title = evento.titulo;

            const pista = document.createElement('span');
            pista.className = 'estadistica-barra__pista';
            const visitas = document.createElement('span');
            visitas.className = 'estadistica-barra__relleno';
            visitas.style.width = `${Math.max(0, evento.visitas / maximo * 100)}%`;
            visitas.title = `${formatoNumero.format(evento.visitas)} visitas`;
            pista.append(visitas);

            const cifras = document.createElement('span');
            cifras.className = 'estadistica-barra__valores';
            const visitasTexto = document.createElement('strong');
            visitasTexto.textContent = formatoNumero.format(evento.visitas);
            visitasTexto.title = 'Visitas';
            const guardadosTexto = document.createElement('span');
            guardadosTexto.textContent = `· ${formatoNumero.format(evento.guardados)} guardados`;
            cifras.append(visitasTexto, guardadosTexto);
            fila.append(nombre, pista, cifras);
            barras.append(fila);
        });
        contenedor.append(leyenda, barras);
    }

    function dibujarInteraccion(contenedor, visitas, guardados) {
        contenedor.replaceChildren();
        contenedor.className = 'estadistica-grafica';
        const leyenda = document.createElement('div');
        leyenda.className = 'estadistica-panel__leyenda';
        [['Visitas', 'estadistica-interaccion__relleno'], ['Guardados', 'estadistica-interaccion__relleno estadistica-interaccion__relleno--guardados']].forEach(([texto, clase]) => {
            const elemento = document.createElement('span');
            const muestra = document.createElement('i');
            muestra.className = clase;
            elemento.append(muestra, document.createTextNode(texto));
            leyenda.append(elemento);
        });
        const grupo = document.createElement('div');
        grupo.className = 'estadistica-interaccion__metricas';
        const maximo = Math.max(1, visitas, guardados);
        [
            ['Visitas', visitas, ''],
            ['Guardados', guardados, ' estadistica-interaccion__relleno--guardados']
        ].forEach(([nombre, cantidad, modificador]) => {
            const fila = document.createElement('div');
            fila.className = 'estadistica-interaccion__fila';
            const etiqueta = document.createElement('span');
            etiqueta.textContent = nombre;
            const pista = document.createElement('span');
            pista.className = 'estadistica-interaccion__pista';
            const barra = document.createElement('span');
            barra.className = `estadistica-interaccion__relleno${modificador}`;
            barra.style.width = `${Math.max(0, cantidad / maximo * 100)}%`;
            barra.title = `${formatoNumero.format(cantidad)} ${nombre.toLowerCase()}`;
            pista.append(barra);
            const valor = document.createElement('strong');
            valor.className = 'estadistica-interaccion__valor';
            valor.textContent = formatoNumero.format(cantidad);
            fila.append(etiqueta, pista, valor);
            grupo.append(fila);
        });
        contenedor.append(leyenda, grupo);
    }

    function actualizarGraficas(datos) {
        const selectorTasa = document.getElementById('estadisticas-tasa-visitas');
        dibujarLinea(
            document.getElementById('estadisticas-visitas-grafica'),
            datos.series.etiquetas,
            [{ valores: datos.series.visitas, unidad: 'visitas' }],
            { analisis: true, tasa: selectorTasa }
        );
        if (!datos.visitas && selectorTasa) selectorTasa.textContent = 'Sin visitas registradas';
        dibujarBarrasMensuales(
            document.getElementById('estadisticas-creacion-grafica'),
            datos.series.meses,
            datos.series.eventosPorMes
        );
        dibujarPopulares(document.getElementById('estadisticas-populares-grafica'), datos.populares);
        dibujarInteraccion(
            document.getElementById('estadisticas-interaccion-grafica'),
            datos.visitas,
            datos.totalGuardados
        );
        dibujarLinea(
            document.getElementById('estadisticas-actividad-grafica'),
            datos.series.etiquetas,
            [
                { valores: datos.series.eventos, unidad: 'eventos', color: '#d49a32' },
                { valores: datos.series.visitas, unidad: 'visitas', color: '#d75f36' }
            ],
            { tendencias: true }
        );

        const conversionDetalle = document.getElementById('estadisticas-conversion-detalle');
        if (conversionDetalle) {
            conversionDetalle.textContent = datos.conversion === null
                ? 'Conversión no disponible'
                : `${formatoNumero.format(datos.conversion)}% de usuarios identificables`;
        }
    }

    async function cargarEstadisticas() {
        if (solicitudActual) solicitudActual.abort();
        const controlador = new AbortController();
        solicitudActual = controlador;
        selectorPeriodo.disabled = true;
        mostrarEstado('Actualizando estadísticas...');
        try {
            const url = new URL(api, window.location.href);
            url.searchParams.set('periodo', selectorPeriodo.value);
            const respuesta = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
                signal: controlador.signal
            });
            const datos = await respuesta.json();
            if (!respuesta.ok) throw new Error(datos.error || 'No se pudieron cargar las estadísticas.');
            actualizarIndicadores(datos);
            actualizarGraficas(datos);
            mostrarEstado(`Datos reales del ${datos.rango.desde} al ${datos.rango.hasta}.`);
        } catch (error) {
            if (error.name === 'AbortError') return;
            panel.querySelectorAll('.estadistica-card__valor').forEach(valor => {
                valor.textContent = '—';
            });
            panel.querySelectorAll('.estadistica-grafica').forEach(grafica => {
                grafica.className = 'estadistica-grafica estadistica-grafica__vacio';
                grafica.textContent = 'No se pudieron cargar los datos.';
            });
            mostrarEstado(error.message || 'No se pudieron cargar las estadísticas.', 'error');
        } finally {
            if (solicitudActual === controlador) selectorPeriodo.disabled = false;
        }
    }

    selectorPeriodo.addEventListener('change', cargarEstadisticas);
    cargarEstadisticas();
})();
