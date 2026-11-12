```php
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
/fast

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Users Combox</title>
    <link rel="icon" type="image/png" href="icons/my logo 0.1.png">
</head>

<body>

    <center>
        <h3>List all</h3>
    </center>

    <table border="1" align="center">

        <tr>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Status</th>
            <th>Photo</th>
            <th>Actions</th>
        </tr>

        <?php
        while ($row = pg_fetch_assoc($res)) {
        ?>

        <tr>

            <td><?php echo htmlspecialchars($row['firstname']); ?></td>

            <td><?php echo htmlspecialchars($row['lastname']); ?></td>

            <td><?php echo htmlspecialchars($row['mobile_phone']); ?></td>

            <td><?php echo htmlspecialchars($row['email']); ?></td>

            <td><?php echo htmlspecialchars($row['users_status']); ?></td>

            <!-- Foto del usuario -->
            <td align="center">

                <?php
                $foto = !empty($row['photo_url'])
                    ? $row['photo_url']
                    : 'photos/user_default.png';

                // Si la base de datos guarda photo/user_default.png
                // se utiliza la carpeta local photos.
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

            <td>

                <!-- Botón Ver -->
                <a href="view_user.php?id=<?php echo urlencode($row['id']); ?>">
                    <button type="button">
                        <img src="icons/lupa.png" width="15">
                        Ver
                    </button>
                </a>

                <!-- Botón Editar -->
                <a href="edit_user.php?id=<?php echo urlencode($row['id']); ?>">
                    <button type="button">
                        <img src="icons/lapiz.png" width="15">
                        Editar
                    </button>
                </a>

                <!-- Botón Eliminar -->
                <form action="delete_user.php" method="POST"
                      style="display:inline;"
                      onsubmit="return confirm('¿Deseas eliminar este usuario?');">

                    <input type="hidden" name="id"
                           value="<?php echo htmlspecialchars($row['id']); ?>">

                    <button type="submit">
                        <img src="icons/basura.png" width="15">
                        Eliminar
                    </button>

                </form>

            </td>

        </tr>

        <?php
        }
        ?>

    </table>

</body>
</html>
```