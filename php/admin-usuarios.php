<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
$administrador = sigtur_requerir_rol($pdo, ['administrador'], 'login.php', 'eventos.php');
$csrfToken = sigtur_csrf_token();
$rolesDisponibles = ['turista', 'organizador', 'administrador'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? null;
    if (!sigtur_validar_csrf(is_string($csrf) ? $csrf : null)) {
        http_response_code(403);
        exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
    }

    $usuarioId = is_string($_POST['usuario_id'] ?? null)
        ? filter_var($_POST['usuario_id'], FILTER_VALIDATE_INT)
        : false;
    $rolNuevo = $_POST['rol'] ?? '';
    if (!$usuarioId || !is_string($rolNuevo) || !in_array($rolNuevo, $rolesDisponibles, true)) {
        http_response_code(422);
        exit('El usuario o el rol seleccionado no son válidos.');
    }

    try {
        $pdo->beginTransaction();
        $buscarUsuario = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id FOR UPDATE');
        $buscarUsuario->execute([':id' => $usuarioId]);
        $rolActual = $buscarUsuario->fetchColumn();
        if ($rolActual === false) {
            $pdo->rollBack();
            http_response_code(404);
            exit('No se encontró el usuario que se quiere modificar.');
        }

        $rolActualNormalizado = sigtur_normalizar_rol((string) $rolActual);
        if ($rolActualNormalizado === $rolNuevo) {
            $pdo->rollBack();
            header('Location: admin-usuarios.php?estado=sin-cambios');
            exit;
        }

        if ($rolActualNormalizado === 'administrador' && $rolNuevo !== 'administrador') {
            $administradoresRestantes = (int) $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol IN ('administrador', 'admin', 'moderador')")->fetchColumn();
            if ($administradoresRestantes <= 1) {
                $pdo->rollBack();
                header('Location: admin-usuarios.php?estado=ultimo-administrador');
                exit;
            }
        }

        if ($rolActualNormalizado === 'organizador' && $rolNuevo === 'turista') {
            $desvincularEventos = $pdo->prepare('UPDATE eventos SET organizador_id = 0 WHERE organizador_id = :usuario');
            $desvincularEventos->execute([':usuario' => $usuarioId]);
        }

        $actualizarRol = $pdo->prepare('UPDATE usuarios SET rol = :rol WHERE id = :id');
        $actualizarRol->execute([':rol' => $rolNuevo, ':id' => $usuarioId]);
        $pdo->commit();

        if ((int) $administrador['id'] === $usuarioId) {
            $_SESSION['rol'] = $rolNuevo;
            if ($rolNuevo !== 'administrador') {
                header('Location: eventos.php?estado=rol-actualizado');
                exit;
            }
        }
        header('Location: admin-usuarios.php?estado=actualizado');
        exit;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error al cambiar el rol de usuario: ' . $exception->getMessage());
        http_response_code(500);
        exit('No se pudo cambiar el rol. Inténtalo nuevamente.');
    }
}

$usuarios = $pdo->query('SELECT id, nombre_completo, email, rol FROM usuarios ORDER BY nombre_completo ASC')->fetchAll();
$mensajesEstado = [
    'actualizado' => 'El rol del usuario se actualizó correctamente.',
    'sin-cambios' => 'El usuario ya tiene ese rol.',
    'ultimo-administrador' => 'No se puede quitar el rol al único administrador del sistema.',
];
$estado = is_string($_GET['estado'] ?? null) ? $_GET['estado'] : '';
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Usuarios | SIGTUR</title><link rel="stylesheet" href="../css/estilos.css"><style>
body{background:#f4f6f8;color:#18212a}.panel{max-width:1100px;margin:0 auto;padding:2rem 1.25rem 5rem}.head{display:flex;justify-content:space-between;align-items:center;gap:1rem}.links{display:flex;gap:.6rem;flex-wrap:wrap}.links a{display:inline-block;padding:.7rem 1rem;border:0;border-radius:8px;background:#152d3d;color:#fff;text-decoration:none;font-weight:700}.user{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:1rem;align-items:center;margin:.75rem 0;padding:1rem;background:#fff;border:1px solid #dce3e8;border-radius:8px}.user p{margin:.25rem 0;color:#65727d}.role{display:inline-flex;align-items:center;gap:.65rem;padding:.65rem .9rem;border:1px solid #174b68;border-radius:9px;background:#1e607f;color:#fff;font:inherit;font-weight:700;cursor:pointer;box-shadow:0 2px 5px #152d3d26;transition:background .18s,transform .18s,box-shadow .18s}.role:hover{background:#174b68;box-shadow:0 4px 10px #152d3d38;transform:translateY(-1px)}.role:focus-visible{outline:3px solid #f0ad3d;outline-offset:3px}.role-action{padding-left:.65rem;border-left:1px solid #ffffff80;font-size:.88rem}.role-editor{display:flex;gap:.5rem;align-items:center}.role-select{padding:.45rem;border:1px solid #9aabb5;border-radius:6px;background:#fff;color:#18212a;font:inherit}.empty,.notice{padding:1rem;background:#fff;border:1px solid #dce3e8;border-radius:8px;color:#65727d}.notice{margin:1rem 0;color:#24495e}.role-dialog{position:fixed;inset:0;width:min(460px,calc(100% - 2rem));margin:auto;padding:2rem;border:1px solid #e0e8ed;border-radius:18px;background:linear-gradient(145deg,#fff,#f4f8fa);box-shadow:0 24px 80px #071a2c66;color:#18212a}.role-dialog[open]{animation:dialog-in .2s ease-out}.role-dialog::backdrop{background:#10202db8;backdrop-filter:blur(4px)}.role-dialog h2{margin:0 0 .75rem;color:#173b51;font-size:1.45rem}.role-dialog p{margin:0;line-height:1.6;color:#4b5c66}.role-dialog-actions{display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.75rem}.role-dialog-actions button{padding:.75rem 1.1rem;border:0;border-radius:8px;font:inherit;font-weight:700;cursor:pointer;transition:filter .15s,transform .15s}.role-dialog-actions button:hover{filter:brightness(.94);transform:translateY(-1px)}.role-dialog-actions button:focus-visible{outline:3px solid #f0ad3d;outline-offset:3px}.confirm-role{background:#176b46;color:#fff}.cancel-role{background:#e8edf0;color:#18212a}@keyframes dialog-in{from{opacity:0;transform:translateY(8px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}@media(prefers-reduced-motion:reduce){.role,.role-dialog[open],.role-dialog-actions button{transition:none;animation:none}}@media(max-width:640px){.head,.user{display:flex;align-items:flex-start;flex-direction:column}.role-editor{flex-wrap:wrap}.role-dialog{padding:1.5rem}.role-dialog-actions{flex-direction:column-reverse}.role-dialog-actions button{width:100%}}
</style><link rel="stylesheet" href="../css/admin-legacy.css"></head><body><main class="panel"><header class="head"><div><p>Administración</p><h1>Usuarios registrados</h1><p>Selecciona el rol actual para cambiar el rol de una cuenta.</p></div><nav class="links"><a href="panel-admin.php">Panel admin</a><a href="../index.php">Inicio</a></nav></header>
<?php if (isset($mensajesEstado[$estado])): ?><p class="notice" role="status"><?= htmlspecialchars($mensajesEstado[$estado], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
<section aria-label="Lista de usuarios registrados"><?php if (!$usuarios): ?><p class="empty">Todavía no hay usuarios registrados.</p><?php else: ?><?php foreach ($usuarios as $usuario): $rol = sigtur_normalizar_rol((string) $usuario['rol']); ?><article class="user" data-user-name="<?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>"><div><strong><?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?></p></div><div class="role-editor"><button class="role" type="button" aria-label="Cambiar rol de <?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false"><span>Rol actual: <?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?></span><span class="role-action" aria-hidden="true">✎ Cambiar</span></button><select class="role-select" aria-label="Nuevo rol para <?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>" data-user-id="<?= (int) $usuario['id'] ?>" data-current-role="<?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?>" hidden><?php foreach ($rolesDisponibles as $rolDisponible): ?><option value="<?= htmlspecialchars($rolDisponible, ENT_QUOTES, 'UTF-8') ?>"<?= $rolDisponible === $rol ? ' selected' : '' ?>><?= htmlspecialchars(ucfirst($rolDisponible), ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div></article><?php endforeach; ?><?php endif; ?></section></main>
<form id="role-change-form" method="post" hidden><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="usuario_id"><input type="hidden" name="rol"></form>
<dialog class="role-dialog" id="role-confirm-dialog" aria-labelledby="role-confirm-title"><h2 id="role-confirm-title">Confirmar cambio de rol</h2><p id="role-confirm-message"></p><div class="role-dialog-actions"><button class="cancel-role" id="cancel-role-change" type="button">Cancelar</button><button class="confirm-role" id="confirm-role-change" type="button">Sí, cambiar rol</button></div></dialog>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dialog = document.getElementById('role-confirm-dialog');
    const message = document.getElementById('role-confirm-message');
    const form = document.getElementById('role-change-form');
    let pendingSelect = null;

    document.querySelectorAll('.role').forEach((button) => {
        button.addEventListener('click', () => {
            const select = button.nextElementSibling;
            select.hidden = !select.hidden;
            button.setAttribute('aria-expanded', String(!select.hidden));
            if (!select.hidden) select.focus();
        });
    });

    document.querySelectorAll('.role-select').forEach((select) => {
        const roleOriginal = select.dataset.currentRole;
        select.addEventListener('change', () => {
            if (select.value === roleOriginal) return;
            pendingSelect = select;
            const nombre = select.closest('.user').dataset.userName;
            message.textContent = `¿Estás seguro de que quieres cambiar el rol de ${nombre} de ${roleOriginal} a ${select.value}?`;
            dialog.showModal();
        });
    });

    document.getElementById('cancel-role-change').addEventListener('click', () => {
        if (pendingSelect) {
            pendingSelect.value = pendingSelect.dataset.currentRole;
            pendingSelect.previousElementSibling.setAttribute('aria-expanded', 'false');
            pendingSelect.hidden = true;
        }
        pendingSelect = null;
        dialog.close();
    });

    dialog.addEventListener('close', () => {
        if (!pendingSelect) return;
        pendingSelect.value = pendingSelect.dataset.currentRole;
        pendingSelect.previousElementSibling.setAttribute('aria-expanded', 'false');
        pendingSelect.hidden = true;
        pendingSelect = null;
    });

    document.getElementById('confirm-role-change').addEventListener('click', () => {
        if (!pendingSelect) return;
        form.elements.usuario_id.value = pendingSelect.dataset.userId;
        form.elements.rol.value = pendingSelect.value;
        form.requestSubmit();
    });
});
</script><script src="../js/tema.js" defer></script><script src="../js/translator.js" defer></script></body></html>
