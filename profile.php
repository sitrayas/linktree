<?php
session_start();
include 'config.php';

$user_id = $_GET['id'] ?? null;

if ($user_id === null) {
    echo "Usuario no encontrado.";
    exit;
}

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Usuario no encontrado.";
    exit;
}

// Obtener enlaces del usuario
$stmt = $pdo->prepare("SELECT * FROM links WHERE user_id = ?");
$stmt->execute([$user_id]);
$links = $stmt->fetchAll();

// Arreglo que relaciona las redes sociales con sus respectivos íconos
$socialIcons = [
    'facebook.com' => 'facebook.svg',
    'twitter.com' => 'twitter.svg',
    'instagram.com' => 'instagram.svg',
    'onlyfans.com' => 'onlyfans.svg',
    'spotify.com' => 'spotify.svg',
    'tiktok.com' => 'tiktok.svg',
    'youtube.com' => 'youtube.svg'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Perfil Público - Linktree</title>
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
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin-top: 20px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        #links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        .tile {
            background-color: rgb(37, 37, 37);
            border-radius: 10px;
            margin: 10px;
            padding: 15px;
            text-align: center;
            flex: 1 0 200px;
            max-width: 150px;
            transition: transform 0.2s;
            color: rgb(240, 240, 240);
            text-decoration: none;
        }

        .tile:hover {
            transform: scale(1.05);
        }

        .icon {
            margin-bottom: 10px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn {
            margin-top: 20px;
            background-color: rgb(240, 240, 240);
            color: rgb(0, 0, 0);
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            text-align: center;
        }

        .btn:hover {
            background-color: rgb(220, 220, 220);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .tile {
                flex: 1 0 45%;
                max-width: 200px;
            }
        }

        @media (max-width: 480px) {
            .tile {
                flex: 1 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Perfil de <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['username']) ?>)</h1>

        <?php if (count($links) > 0): ?>
            <div id="links" class="row">
                <?php foreach ($links as $link): ?>
                    <a class="tile col-12 col-sm-6 col-md-4 col-lg-3" href="<?= htmlspecialchars($link['url']) ?>" target="_blank">
                        <div class="icon">
                            <?php
                            $url = strtolower($link['url']);
                            $iconPath = '';

                            // Buscar el ícono correspondiente a la URL
                            foreach ($socialIcons as $social => $icon) {
                                if (strpos($url, $social) !== false) {
                                    $iconPath = 'IconosRedes/' . $icon;
                                    break;
                                }
                            }

                            // Mostrar el ícono si existe
                            if ($iconPath && file_exists($iconPath)) {
                                echo '<img src="' . htmlspecialchars($iconPath) . '" alt="Icono de ' . htmlspecialchars($social) . '" width="40" height="40">';
                            } else {
                                echo '<span>Icono no disponible</span>';
                            }
                            ?>
                        </div>
                        <p><?= htmlspecialchars($link['name']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No se encontraron enlaces.</p>
        <?php endif; ?>

        <!-- Botón de regreso al dashboard -->
        <a href="dashboard.php" class="btn">Regresar al Dashboard</a>
    </div>
</body>
</html>
