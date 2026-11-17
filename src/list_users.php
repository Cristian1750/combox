
<?php
require('../config/database.php');

$sql = "
    SELECT
        id,
        firstname,
        lastname,
        mobile_phone,
        email,
        CASE
            WHEN u.status = true THEN 'Active'
            ELSE 'Inactive'
        END AS users_status,
        photo_url
    FROM users u
    ORDER BY lastname ASC
";

$res = pg_query($supa_conn, $sql);

if (!$res) {
    die("Error en la consulta: " . pg_last_error($supa_conn));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>List Users Combox</title>

    <link rel="icon" type="image/png" href="icons/my logo 0.1.png">

    <!-- Estilos de la tabla -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            padding: 20px;
        }

        h3 {
            text-align: center;
        }

        .table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            text-align: center;
        }

        .table th,
        .table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .table th {
            font-weight: bold;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Temas de tabla */
        .theme-primary th {
            background-color: #0d6efd;
            color: white;
        }

        .theme-accent th {
            background-color: #6610f2;
            color: white;
        }

        .theme-success th {
            background-color: #198754;
            color: white;
        }

        .theme-danger th {
            background-color: #dc3545;
            color: white;
        }

        .theme-warning th {
            background-color: #ffc107;
            color: #212529;
        }

        .theme-info th {
            background-color: #0dcaf0;
            color: #212529;
        }

        .theme-inverse th {
            background-color: #212529;
            color: white;
        }

        .theme-secondary th {
            background-color: #6c757d;
            color: white;
        }

        /* Botones */
        button {
            padding: 6px 10px;
            margin: 2px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }

        button:hover {
            opacity: 0.8;
        }

        img {
            vertical-align: middle;
        }

        /* Casillas de opciones */
        .opciones-box {
            display: none;
            margin-top: 6px;
        }

        .btn-inactivar {
            background-color: #ffc107;
            color: #212529;
            font-weight: bold;
        }

        .btn-activar {
            background-color: #198754;
            color: white;
            font-weight: bold;
        }

        .btn-eliminar {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }
    </style>

</head>

<body>

    <h3>List all Users</h3>

    <table class="table theme-primary">

        <thead>
            <tr>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Mobile</th>
                <th>Email</th>
                <th>Status</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            <?php
            while ($row = pg_fetch_assoc($res)) {
            ?>

            <tr>

                <!-- Firstname -->
                <td><?php echo htmlspecialchars($row['firstname']); ?></td>

                <!-- Lastname -->
                <td><?php echo htmlspecialchars($row['lastname']); ?></td>

                <!-- Mobile -->
                <td><?php echo htmlspecialchars($row['mobile_phone']); ?></td>

                <!-- Email -->
                <td><?php echo htmlspecialchars($row['email']); ?></td>

                <!-- Status -->
                <td><?php echo htmlspecialchars($row['users_status']); ?></td>

                <!-- Photo -->
                <td align="center">

                    <?php
                    $foto = !empty($row['photo_url'])
                        ? $row['photo_url']
                        : 'photos/user_default.png';

                    if (strpos($foto, 'photo/') === 0) {
                        $foto = 'photos/' . basename($foto);
                    }
                    ?>

                    <img
                        src="<?php echo htmlspecialchars($foto); ?>"
                        width="50"
                        height="50"
                        style="object-fit:cover; border-radius:50%;"
                        alt="Foto del usuario"
                    >

                </td>

                <!-- Actions -->
                <td>

                    <!-- View -->
                    <a href="view_user.php?id=<?php echo urlencode($row['id']); ?>">
                        <button type="button" title="Ver usuario">
                            <img src="icons/lupa.png" width="15" alt="Ver">
                        </button>
                    </a>

                    <!-- Edit -->
                    <a href="edit_user.php?id=<?php echo urlencode($row['id']); ?>">
                        <button type="button" title="Editar usuario">
                            <img src="icons/lapiz.png" width="15" alt="Editar">
                        </button>
                    </a>

                    <!-- Botón que despliega las opciones -->
                    <button type="button" title="Opciones"
                            onclick="mostrarOpciones('<?php echo htmlspecialchars($row['id']); ?>')">
                        <img src="icons/bote.png" width="20" height="20" alt="Opciones">
                    </button>

                    <!-- Casillas de activar/inactivar y eliminar -->
                    <div id="opciones-<?php echo htmlspecialchars($row['id']); ?>" class="opciones-box">

                        <form action="delete_users.php" method="POST" style="display:inline;">

                            <input type="hidden" name="user_id"
                                   value="<?php echo htmlspecialchars($row['id']); ?>">

                            <?php if ($row['users_status'] === 'Active') { ?>

                                <button type="submit" name="accion" value="inactivar"
                                        class="btn-inactivar"
                                        onclick="return confirm('¿Inactivar este usuario?');">
                                    Inactivar
                                </button>

                            <?php } else { ?>

                                <button type="submit" name="accion" value="activar"
                                        class="btn-activar"
                                        onclick="return confirm('¿Activar nuevamente este usuario?');">
                                    Activar
                                </button>

                            <?php } ?>

                            <button type="submit" name="accion" value="eliminar"
                                    class="btn-eliminar"
                                    onclick="return confirm('¿Eliminar definitivamente este usuario?');">
                                Eliminar
                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            <?php
            }
            ?>

        </tbody>

    </table>

    <script>
        function mostrarOpciones(id) {
            var caja = document.getElementById('opciones-' + id);

            if (caja.style.display === 'block') {
                caja.style.display = 'none';
            } else {
                caja.style.display = 'block';
            }
        }
    </script>

</body>
</html>
