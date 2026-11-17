<?php
require('../config/database.php');

$user_id = $_POST['user_id'];
$accion = $_POST['accion'];

if ($accion == 'inactivar') {
    $sql = "UPDATE users SET status = false WHERE id = $1";
    $mensaje = "Usuario inactivado correctamente.";
} elseif ($accion == 'activar') {
    $sql = "UPDATE users SET status = true WHERE id = $1";
    $mensaje = "Usuario activado correctamente.";
} else {
    $sql = "DELETE FROM users WHERE id = $1";
    $mensaje = "Usuario eliminado correctamente.";
}

$res = pg_query_params($supa_conn, $sql, array($user_id));

if ($res) {
    echo "<script>alert('$mensaje'); window.location.href='list_users.php';</script>";
} else {
    echo "<script>alert('Error al procesar la solicitud.'); window.location.href='list_users.php';</script>";
}
?>
