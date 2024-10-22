<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['linkName']) && isset($_POST['linkUrl'])) {
        $name = $_POST['linkName'];
        $url = $_POST['linkUrl'];

        $stmt = $pdo->prepare("INSERT INTO links (user_id, name, url) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $name, $url]);
    } elseif (isset($_POST['updateLinkId']) && isset($_POST['updateLinkName']) && isset($_POST['updateLinkUrl'])) {
        $updateId = $_POST['updateLinkId'];
        $updateName = $_POST['updateLinkName'];
        $updateUrl = $_POST['updateLinkUrl'];

        $stmt = $pdo->prepare("UPDATE links SET name = ?, url = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$updateName, $updateUrl, $updateId, $user_id]);
    } elseif (isset($_POST['deleteLinkId'])) {
        $deleteId = $_POST['deleteLinkId'];

        $stmt = $pdo->prepare("DELETE FROM links WHERE id = ? AND user_id = ?");
        $stmt->execute([$deleteId, $user_id]);
    }
}

$stmt = $pdo->prepare("SELECT * FROM links WHERE user_id = ?");
$stmt->execute([$user_id]);
$links = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: rgb(10, 10, 10);
            color: rgb(240, 240, 240);
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Verdana, Tahoma, sans-serif;
            height: 100vh;
        }

        h1 {
            margin-top: 20px;
            font-size: 28px;
        }

        form {
            display: flex;
            flex-direction: column;
            width: 90%;
            max-width: 400px;
            margin: 20px auto;
        }

        .form-control {
            border-radius: 5px;
            background-color: rgb(52, 52, 52);
            color: rgb(240, 240, 240);
            border: 1px solid rgb(80, 80, 80);
            margin-bottom: 10px;
        }

        .btn {
            background-color: rgb(240, 240, 240);
            color: rgb(0, 0, 0);
            border: none;
            border-radius: 5px;
            padding: 10px;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn:hover {
            background-color: rgb(220, 220, 220);
            transform: translateY(-2px);
        }

        #links {
            width: 90%;
            max-width: 600px;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tile {
            background-color: rgb(37, 37, 37);
            border-radius: 10px;
            margin: 10px;
            padding: 15px;
            text-align: center;
            width: 100%;
            transition: transform 0.2s;
            color: rgb(240, 240, 240);
            text-decoration: none;
        }

        .tile:hover {
            transform: scale(1.05);
        }

        .edit-options {
            display: none;
            flex-direction: column;
            margin-top: 10px;
        }

        .edit-options.active {
            display: flex;
        }

        .select-tile {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .select-tile input[type="radio"] {
            margin-right: 10px;
        }
    </style>
    <script>
        function showEditOptions(linkId, name, url) {
            const options = document.querySelectorAll('.edit-options');
            options.forEach(option => option.classList.remove('active'));

            const editForm = document.getElementById('edit-' + linkId);
            editForm.classList.add('active');

            editForm.querySelector('input[name="updateLinkName"]').value = name;
            editForm.querySelector('input[name="updateLinkUrl"]').value = url;
        }
    </script>
</head>
<body>
    <h1>Bienvenido</h1>
    <form method="POST">
        <input type="text" name="linkName" class="form-control" placeholder="Nombre del enlace" required>
        <input type="url" name="linkUrl" class="form-control" placeholder="URL del enlace" required>
        <button type="submit" class="btn btn-primary">Agregar Enlace</button>
    </form>

    <h2>Mis Enlaces</h2>
    <div id="links">
        <?php foreach ($links as $link): ?>
            <div class="tile select-tile">
                <label>
                    <input type="radio" name="selectedLink" value="<?= htmlspecialchars($link['id']) ?>" onclick="showEditOptions(<?= htmlspecialchars($link['id']) ?>, '<?= htmlspecialchars($link['name']) ?>', '<?= htmlspecialchars($link['url']) ?>')">
                    <?= htmlspecialchars($link['name']) ?>
                </label>
            </div>
            <form method="POST" class="edit-options" id="edit-<?= htmlspecialchars($link['id']) ?>">
                <input type="hidden" name="updateLinkId" value="<?= htmlspecialchars($link['id']) ?>">
                <input type="text" name="updateLinkName" class="form-control" placeholder="Nuevo Nombre" required>
                <input type="url" name="updateLinkUrl" class="form-control" placeholder="Nueva URL" required>
                <button type="submit" class="btn btn-warning">Actualizar</button>
                <input type="hidden" name="deleteLinkId" value="<?= htmlspecialchars($link['id']) ?>">
                <button type="submit" class="btn btn-danger" style="margin-top: 10px;">Eliminar</button>
            </form>
        <?php endforeach; ?>
    </div>

    <div class="buttons">
        <a href="profile.php?id=<?= $_SESSION['user_id'] ?>" class="btn btn-secondary">Compartir mi perfil</a>
        <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
