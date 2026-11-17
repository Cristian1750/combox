
<?php
require('../config/database.php');

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    die("No se recibió el ID del usuario.");
}

// Consultar usuario
$sql = "SELECT * FROM users WHERE id = $1";
$res = pg_query_params($supa_conn, $sql, [$id]);

if (!$res || pg_num_rows($res) == 0) {
    die("Usuario no encontrado.");
}

$user = pg_fetch_assoc($res);
$mensaje = "";

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $mobile_phone = trim($_POST['mobile_phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = $_POST['status'] ?? 'true';

    $photo_url = $user['photo_url'] ?? '';

    // Seleccionar nueva fotografía
    if (isset($_FILES['photo']) &&
        $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $mensaje = "Error al cargar la fotografía.";

        } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            $mensaje = "La foto supera los 5 MB.";

        } else {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);

            $extensiones = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];

            if (!isset($extensiones[$mime])) {
                $mensaje = "Formato de imagen no permitido.";

            } else {

                $carpeta = __DIR__ . '/photos/';

                if (!is_dir($carpeta)) {
                    mkdir($carpeta, 0755, true);
                }

                $nombreFoto = 'user_' . $id . '_' .
                              uniqid() . '.' . $extensiones[$mime];

                if (move_uploaded_file(
                    $_FILES['photo']['tmp_name'],
                    $carpeta . $nombreFoto
                )) {
                    $photo_url = 'photos/' . $nombreFoto;

                } else {
                    $mensaje = "No se pudo guardar la fotografía.";
                }
            }
        }
    }

    if ($mensaje === '') {

        if ($firstname == '' || $lastname == '' || $email == '') {
            $mensaje = "Completa los campos obligatorios.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "Correo electrónico inválido.";

        } else {

            $update = "UPDATE users SET
                firstname = $1,
                lastname = $2,
                mobile_phone = $3,
                email = $4,
                status = $5,
                photo_url = $6
                WHERE id = $7";

            $result = pg_query_params($supa_conn, $update, [
                $firstname,
                $lastname,
                $mobile_phone,
                $email,
                $status === 'true' ? 'true' : 'false',
                $photo_url,
                $id
            ]);

            if ($result) {
                header("Location: list_users.php");
                exit;

            } else {
                $mensaje = "Error al actualizar: " .
                           pg_last_error($supa_conn);
            }
        }
    }

    // Mantener datos escritos
    $user['firstname'] = $firstname;
    $user['lastname'] = $lastname;
    $user['mobile_phone'] = $mobile_phone;
    $user['email'] = $email;
    $user['status'] = $status === 'true' ? 't' : 'f';
    $user['photo_url'] = $photo_url;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit User Combox</title>

    <link rel="icon" type="image/png" href="icons/my logo 0.1.png">

    <!-- Estilos de la tabla -->
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6fa;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .table {
            width: 75%;
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

    </style>

</head>

<body>

<center>

    <h2>Edit User</h2>

    <?php if ($mensaje != '') { ?>
        <p style="color:red;">
            <?php echo htmlspecialchars($mensaje); ?>
        </p>
    <?php } ?>

    <form method="POST"
          action="edit_user.php"
          enctype="multipart/form-data">

        <input type="hidden" name="id"
               value="<?php echo htmlspecialchars($user['id']); ?>">

        <table class="table theme-primary">

            <thead>
                <tr>
                    <th>Field</th>
                    <th>Current Data</th>
                    <th>Edit Data</th>
                </tr>
            </thead>

            <tbody>

                <!-- ID -->
                <tr>
                    <td>ID</td>
                    <td>
                        <?php echo htmlspecialchars($user['id']); ?>
                    </td>
                    <td>
                        <input type="text"
                               value="<?php echo htmlspecialchars($user['id']); ?>"
                               readonly>
                    </td>
                </tr>

                <!-- Firstname -->
                <tr>
                    <td>Firstname</td>
                    <td>
                        <?php echo htmlspecialchars($user['firstname']); ?>
                    </td>
                    <td>
                        <input type="text" name="firstname"
                        value="<?php echo htmlspecialchars($user['firstname']); ?>"
                        required>
                    </td>
                </tr>

                <!-- Lastname -->
                <tr>
                    <td>Lastname</td>
                    <td>
                        <?php echo htmlspecialchars($user['lastname']); ?>
                    </td>
                    <td>
                        <input type="text" name="lastname"
                        value="<?php echo htmlspecialchars($user['lastname']); ?>"
                        required>
                    </td>
                </tr>

                <!-- Mobile Phone -->
                <tr>
                    <td>Mobile Phone</td>
                    <td>
                        <?php echo htmlspecialchars($user['mobile_phone'] ?? ''); ?>
                    </td>
                    <td>
                        <input type="text" name="mobile_phone"
                        value="<?php echo htmlspecialchars($user['mobile_phone'] ?? ''); ?>">
                    </td>
                </tr>

                <!-- Email -->
                <tr>
                    <td>Email</td>
                    <td>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </td>
                    <td>
                        <input type="email" name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>"
                        required>
                    </td>
                </tr>

                <!-- Status -->
                <tr>
                    <td>Status</td>
                    <td>
                        <?php echo ($user['status'] === 't') ? 'Active' : 'Inactive'; ?>
                    </td>
                    <td>
                        <select name="status">

                            <option value="true"
                            <?php echo ($user['status'] === 't') ? 'selected' : ''; ?>>
                                Active
                            </option>

                            <option value="false"
                            <?php echo ($user['status'] === 'f') ? 'selected' : ''; ?>>
                                Inactive
                            </option>

                        </select>
                    </td>
                </tr>

                <!-- Photo -->
                <tr>
                    <td>Photo</td>

                    <td>
                        <?php
                        $foto = $user['photo_url'] ?? '';

                        if (strpos($foto, 'photo/') === 0) {
                            $foto = 'photos/' . basename($foto);
                        }

                        if (empty($foto)) {
                            $foto = 'photos/user_default.png';
                        }
                        ?>

                        <img src="<?php echo htmlspecialchars($foto); ?>"
                             width="60"
                             height="60"
                             style="object-fit:cover; border-radius:50%;"
                             alt="User Photo">
                    </td>

                    <td>
                        <input type="file"
                               name="photo"
                               accept="image/*">
                    </td>
                </tr>

            </tbody>

        </table>

        <br>

        <button type="submit">Update User</button>

        <a href="list_users.php">
            <button type="button">Back</button>
        </a>

    </form>

</center>

</body>
</html>
