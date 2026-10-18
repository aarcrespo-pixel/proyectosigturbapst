<form class="admin-form admin-content-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
    <input type="hidden" name="seccion" value="<?= $escape($seccion) ?>">
    <input type="hidden" name="accion" value="<?= (int) $contenido['id'] > 0 ? 'editar' : 'crear' ?>">
    <input type="hidden" name="id" value="<?= (int) $contenido['id'] ?>">
    <label>Slug<input name="slug" maxlength="120" pattern="[a-z0-9]+(-[a-z0-9]+)*" required value="<?= $escape((string) $contenido['slug']) ?>"></label>
    <label>Nombre<input name="nombre" maxlength="160" required value="<?= $escape((string) $contenido['nombre']) ?>"></label>
    <label>Imagen (ruta del sitio o URL HTTPS)<input name="imagen" maxlength="500" required value="<?= $escape((string) $contenido['imagen']) ?>"></label>
    <label>Orden<input name="orden" type="number" min="0" value="<?= (int) $contenido['orden'] ?>"></label>
    <?php if ($seccion === 'turismo'): ?>
        <label>Sección<select name="tipo"><option value="escapada" <?= $contenido['tipo'] === 'escapada' ? 'selected' : '' ?>>Escapadas recomendadas</option><option value="ruta" <?= $contenido['tipo'] === 'ruta' ? 'selected' : '' ?>>Rutas y experiencias</option><option value="termas" <?= $contenido['tipo'] === 'termas' ? 'selected' : '' ?>>Parques acuáticos y termas</option></select></label>
        <label>Detalle / categoría<input name="detalle" maxlength="255" value="<?= $escape((string) $contenido['detalle']) ?>"></label>
        <label>Estrellas (solo termas)<input name="estrellas" type="number" min="1" max="5" value="<?= $escape((string) ($contenido['estrellas'] ?? '')) ?>"></label>
    <?php else: ?>
        <label>Categoría<input name="categoria" maxlength="80" required value="<?= $escape((string) $contenido['categoria']) ?>"></label>
        <label>Enlace a Google Maps (opcional)<input name="url_mapa" type="url" maxlength="500" value="<?= $escape((string) $contenido['url_mapa']) ?>"></label>
    <?php endif; ?>
    <label class="admin-form__wide">Descripción<textarea name="descripcion" required><?= $escape((string) $contenido['descripcion']) ?></textarea></label>
    <div class="admin-form__wide admin-form__actions"><button class="admin-button" type="submit"><?= (int) $contenido['id'] > 0 ? 'Guardar cambios' : 'Crear contenido' ?></button></div>
</form>
