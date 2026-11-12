<form class="admin-form admin-content-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
    <input type="hidden" name="seccion" value="lugares">
    <input type="hidden" name="accion" value="<?= (int) $lugar['id'] > 0 ? 'editar' : 'crear' ?>">
    <input type="hidden" name="id" value="<?= (int) $lugar['id'] ?>">
    <label>Slug<input name="slug" maxlength="120" pattern="[a-z0-9]+(-[a-z0-9]+)*" required value="<?= $escape((string) $lugar['slug']) ?>"></label>
    <label>Nombre<input name="nombre" maxlength="160" required value="<?= $escape((string) $lugar['nombre']) ?>"></label>
    <label>Categoría<input name="categoria" maxlength="80" required value="<?= $escape((string) $lugar['categoria']) ?>"></label>
    <label>Sección pública<select name="seccion_publica"><option value="emblematico" <?= $lugar['seccion_publica'] === 'emblematico' ? 'selected' : '' ?>>Lugares emblemáticos</option><option value="recreacion" <?= $lugar['seccion_publica'] === 'recreacion' ? 'selected' : '' ?>>Lugares para distraerse</option><option value="oculto" <?= $lugar['seccion_publica'] === 'oculto' ? 'selected' : '' ?>>No mostrar en la página pública</option></select></label>
    <label>Dirección<input name="direccion" maxlength="255" required value="<?= $escape((string) $lugar['direccion']) ?>"></label>
    <label class="admin-form__wide">Imagen actual (ruta del sitio o URL HTTPS)
        <?php if ($lugar['imagen'] !== ''): ?>
            <img class="admin-event__image-preview" src="<?= $escape((string) $lugar['imagen']) ?>" alt="Imagen actual de <?= $escape((string) $lugar['nombre']) ?>" loading="lazy">
        <?php endif; ?>
        <input name="imagen" maxlength="500" value="<?= $escape((string) $lugar['imagen']) ?>">
    </label>
    <label class="admin-form__wide">Subir imagen nueva (JPG, PNG o WEBP)
        <input class="admin-event__image-input" type="file" name="imagen_archivo" accept="image/jpeg,image/png,image/webp">
        <span class="admin-field-hint">Si no seleccionas una imagen, se conserva la ruta o URL actual.</span>
    </label>
    <label>Horarios<input name="horarios" maxlength="255" required value="<?= $escape((string) $lugar['horarios']) ?>"></label>
    <label>Latitud de OpenStreetMap<input name="latitud" type="number" min="-90" max="90" step="any" value="<?= $escape((string) ($lugar['latitud'] ?? '')) ?>"></label>
    <label>Longitud de OpenStreetMap<input name="longitud" type="number" min="-180" max="180" step="any" value="<?= $escape((string) ($lugar['longitud'] ?? '')) ?>"></label>
    <label>Tipo de objeto OSM<select name="osm_type"><option value="">Sin referencia OSM</option><?php foreach (['node' => 'Punto (node)', 'way' => 'Línea o área (way)', 'relation' => 'Relación (relation)'] as $tipoOsm => $etiquetaOsm): ?><option value="<?= $escape($tipoOsm) ?>" <?= ($lugar['osm_type'] ?? '') === $tipoOsm ? 'selected' : '' ?>><?= $escape($etiquetaOsm) ?></option><?php endforeach; ?></select></label>
    <label>ID del objeto OSM<input name="osm_id" inputmode="numeric" pattern="[0-9]+" maxlength="32" value="<?= $escape((string) ($lugar['osm_id'] ?? '')) ?>"></label>
    <p class="admin-field-hint admin-form__wide">Las coordenadas se usan en el comparador solo cuando incluyen una referencia de objeto OpenStreetMap válida. Encontrá el tipo y el ID en el enlace del objeto del mapa.</p>
    <label class="admin-form__wide">Descripción<textarea name="descripcion" required><?= $escape((string) $lugar['descripcion']) ?></textarea></label>
    <label class="admin-form__wide">Historia<textarea name="historia" required><?= $escape((string) $lugar['historia']) ?></textarea></label>
    <label class="admin-form__wide">Atractivos<textarea name="atractivos" required><?= $escape((string) $lugar['atractivos']) ?></textarea></label>
    <label class="admin-form__wide">Recomendaciones<textarea name="recomendaciones" required><?= $escape((string) $lugar['recomendaciones']) ?></textarea></label>
    <div class="admin-form__wide admin-form__actions"><button class="admin-button" type="submit"><?= (int) $lugar['id'] > 0 ? 'Guardar cambios' : 'Crear lugar' ?></button></div>
</form>
