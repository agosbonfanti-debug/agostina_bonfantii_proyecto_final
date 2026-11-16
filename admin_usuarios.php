<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereAdmin();
$buscar = trim($_GET['buscar'] ?? '');
$sql = 'SELECT id, nombre, apellido, dni, email, rol, creado_en FROM usuarios WHERE archivado_en IS NULL';
$parametros = [];
if ($buscar !== '') {
    $sql .= ' AND (nombre LIKE :nombre OR apellido LIKE :apellido OR dni LIKE :dni OR email LIKE :email)';
    $termino = '%' . $buscar . '%';
    $parametros = ['nombre'=>$termino, 'apellido'=>$termino, 'dni'=>$termino, 'email'=>$termino];
}
$sql .= ' ORDER BY creado_en DESC';
$stmt = $conexion->prepare($sql);
$stmt->execute($parametros);
$usuarios = $stmt->fetchAll();
$tituloPagina = 'Administrar usuarios | NIDO';
require 'encabezado.php';
?>
<section class="admin-cabecera"><div><p class="sobrelinea">PANEL ADMINISTRATIVO</p><h1>Usuarios registrados</h1><p>Buscá, editá y administrá las cuentas del sitio.</p></div><a class="boton" href="nuevo_usuario.php">+ Registrar usuario</a></section>
<?php mostrarMensaje(); ?>
<form method="GET" class="buscador"><input type="search" name="buscar" maxlength="100" placeholder="Nombre, apellido, DNI o correo" value="<?= escapar($buscar) ?>"><button class="boton" type="submit">Buscar</button><?php if ($buscar !== ''): ?><a href="admin_usuarios.php">Limpiar</a><?php endif; ?></form>
<div class="tabla-contenedor"><table><thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>DNI</th><th>Correo</th><th>Rol</th><th>Acciones</th></tr></thead><tbody>
<?php if (!$usuarios): ?><tr><td colspan="7">No se encontraron usuarios.</td></tr><?php else: foreach ($usuarios as $u): ?>
<tr><td><?= (int)$u['id'] ?></td><td><?= escapar($u['nombre']) ?></td><td><?= escapar($u['apellido']) ?></td><td><?= escapar($u['dni']) ?></td><td><?= escapar($u['email']) ?></td><td><span class="tag"><?= escapar($u['rol']) ?></span></td><td class="acciones-tabla"><a href="editar_usuario.php?id=<?= (int)$u['id'] ?>">Editar</a><form method="POST" action="archivar_usuario.php" onsubmit="return confirm('¿Archivar esta cuenta?');"><input type="hidden" name="csrf_token" value="<?= escapar(crearToken()) ?>"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>"><button class="link-peligro" type="submit">Archivar</button></form></td></tr>
<?php endforeach; endif; ?>
</tbody></table></div>
<div class="panel-enlaces"><a href="usuarios_archivados.php">Consultar usuarios archivados</a><a href="panel.php">Volver a mi espacio</a></div>
<?php require 'pie.php'; ?>
