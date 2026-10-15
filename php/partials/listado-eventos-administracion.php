<section class="admin-section" id="eventos" aria-labelledby="events-title">
    <div class="admin-section__heading">
        <div>
            <p class="admin-eyebrow">Contenido del sitio</p>
            <h2 id="events-title">Eventos</h2>
            <p>Los próximos eventos aparecen primero. Abrí “Editar evento” para modificar sus datos.</p>
        </div>
        <span class="admin-count"><?= count($todosEventos) ?> eventos</span>
    </div>
    <?php if (!$todosEventos): ?>
        <p class="admin-empty">Todavía no hay eventos cargados.</p>
    <?php else: ?>
        <div class="admin-event-list" id="admin-event-list">
            <?php $grupoEventosActual = null; $indiceEvento = 0; ?>
            <?php foreach ($todosEventos as $evento):
                $fechaEvento = strtotime($evento['fecha']);
                $esFuturo = $fechaEvento !== false && $fechaEvento >= strtotime('today');
                if ($grupoEventosActual !== $esFuturo):
                    $grupoEventosActual = $esFuturo;
                    $cantidadGrupo = count(array_filter($todosEventos, static function ($eventoGrupo) use ($esFuturo) {
                        $fechaGrupo = strtotime($eventoGrupo['fecha']);
                        return $fechaGrupo !== false && ($fechaGrupo >= strtotime('today')) === $esFuturo;
                    }));
            ?>
                    <h3 class="admin-event-group-title"><?= $esFuturo ? 'Próximos eventos' : 'Eventos finalizados' ?><span><?= $cantidadGrupo ?></span></h3>
                <?php endif; ?>
                <article class="admin-event" data-event-index="<?= $indiceEvento++ ?>">
                    <div class="admin-event__summary">
                        <div class="admin-event__date">
                            <span><?= $fechaEvento ? htmlspecialchars(date('d', $fechaEvento), ENT_QUOTES, 'UTF-8') : '—' ?></span>
                            <small><?= $fechaEvento ? htmlspecialchars(date('M Y', $fechaEvento), ENT_QUOTES, 'UTF-8') : 'Sin fecha' ?></small>
                        </div>
                        <div class="admin-event__info">
                            <div class="admin-event__badges">
                                <span class="admin-status <?= $esFuturo ? 'admin-status--upcoming' : 'admin-status--past' ?>"><?= $esFuturo ? 'Próximo' : 'Finalizado' ?></span>
                                <span class="admin-category"><?= htmlspecialchars($evento['categoria'] ?: 'Sin categoría', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <h3><?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?> <span>·</span> Organizador #<?= (int) $evento['organizador_id'] ?></p>
                        </div>
                        <a class="admin-text-link admin-event__open" href="evento.php?id=<?= (int) $evento['id'] ?>">Ver evento</a>
                    </div>
                    <details class="admin-event__editor">
                        <summary>Editar evento</summary>
                        <form class="admin-form" method="post" enctype="multipart/form-data" action="crear-evento.php">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                            <label class="admin-form__wide">Descripción
                                <textarea name="descripcion" maxlength="5000" required><?= htmlspecialchars($evento['descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea>
                            </label>
                            <label>Título
                                <input name="titulo" maxlength="255" required value="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                            </label>
                            <label>Fecha
                                <input type="date" name="fecha" required value="<?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8') ?>">
                            </label>
                            <label>Ubicación
                                <input name="ubicacion" maxlength="255" required value="<?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?>">
                            </label>
                            <label>Categoría
                                <input name="categoria" maxlength="100" required value="<?= htmlspecialchars($evento['categoria'], ENT_QUOTES, 'UTF-8') ?>">
                            </label>
                            <label>Tipo de entrada
                                <select name="tipoEntrada">
                                    <option value="Gratuito" <?= $evento['tipo_entrada'] === 'Gratuito' ? 'selected' : '' ?>>Gratuito</option>
                                    <option value="De Pago" <?= $evento['tipo_entrada'] === 'De Pago' ? 'selected' : '' ?>>De Pago</option>
                                </select>
                            </label>
                            <label>Precio
                                <input type="number" min="0" step="0.01" name="precio" value="<?= htmlspecialchars((string) ($evento['precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            </label>
                            <label>Organizador
                                <select name="organizador_id">
                                    <option value="0" <?= (int) $evento['organizador_id'] === 0 ? 'selected' : '' ?>>Sin asignar</option>
                                    <?php foreach ($organizadores as $organizador): ?>
                                        <option value="<?= (int) $organizador['id'] ?>" <?= (int) $evento['organizador_id'] === (int) $organizador['id'] ? 'selected' : '' ?>><?= htmlspecialchars($organizador['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label class="admin-form__wide admin-event__image-field">Foto del evento
                                <img
                                    class="admin-event__image-preview"
                                    src="<?= htmlspecialchars($evento['imagen_portada'] ?: '../img/porco.avif', ENT_QUOTES, 'UTF-8') ?>"
                                    alt="Foto actual de <?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                    loading="lazy"
                                >
                                <input class="admin-event__image-input" type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
                                <span class="admin-field-hint">Seleccioná una nueva foto JPG, PNG o WEBP. Si no elegís otra, se conserva la actual.</span>
                            </label>
                            <div class="admin-form__wide admin-form__actions">
                                <button class="admin-button" type="submit">Guardar cambios</button>
                                <button class="admin-button admin-button--danger" type="submit" form="delete-event-<?= (int) $evento['id'] ?>" onclick="return confirm('¿Eliminar este evento? Esta acción no se puede deshacer.')">Eliminar evento</button>
                            </div>
                        </form>
                        <form id="delete-event-<?= (int) $evento['id'] ?>" method="post" action="crear-evento.php">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                        </form>
                    </details>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (count($todosEventos) > 4): ?>
            <button class="admin-button admin-events-toggle" type="button" data-admin-events-toggle aria-expanded="false" aria-controls="admin-event-list">Ver más eventos</button>
        <?php endif; ?>
    <?php endif; ?>
</section>
