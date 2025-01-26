<?php
require 'db_config.php';
session_start();

$error_message = '';

// Detalhes do servidor Discord via Widget
$discord_api_url = 'https://discord.com/api/v9/invites/FZt5GpkHaT';  // URL da API de convite do servidor
$discord_data = file_get_contents($discord_api_url);
$discord_server = json_decode($discord_data, true);

$online_members = isset($discord_server['approximate_presence_count']) ? $discord_server['approximate_presence_count'] : 'N/A';
$total_members = isset($discord_server['approximate_member_count']) ? $discord_server['approximate_member_count'] : 'N/A';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $playername = trim($_POST['playername']);
    $password = trim($_POST['password']);

    if (!empty($playername) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM playerpassword WHERE playername = :playername");
        $stmt->bindParam(':playername', $playername);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($password === $row['password']) {
                $_SESSION['playername'] = $playername;
                header("Location: /painel");
                exit;
            } else {
                $error_message = 'Senha incorreta.';
            }
        } else {
            $error_message = 'Jogador não encontrado.';
        }
    } else {
        $error_message = 'Por favor, preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <button id="login-btn">Login</button>

    <div id="login-card">
        <h1>Login</h1>
        <?php if (!empty($error_message)) : ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="index.php" method="POST">
            <label for="playername">Nome do Jogador:</label>
            <input type="text" id="playername" name="playername" required>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Entrar</button>
        </form>
    </div>

    <!-- Link para o servidor do Discord -->
    <div class="discord-link">
        <p>Participe do nosso servidor no Discord:</p>
        <a href="https://discord.gg/FZt5GpkHaT" target="_blank" class="discord-btn">Acessar Discord</a>
    </div>

    <!-- Detalhes do servidor Discord -->
    <div class="discord-details">
        <h3>Detalhes do Servidor Discord:</h3>
        <p><strong>Total de membros:</strong> <?php echo htmlspecialchars($total_members); ?></p>
        <p><strong>Membros online:</strong> <?php echo htmlspecialchars($online_members); ?></p>
    </div>

    <!-- Widget Discord -->
    <div class="discord-widget">
        <h3>Servidor Discord - Widget</h3>
        <iframe src="https://discord.com/widget?id=FZt5GpkHaT" width="350" height="500" allowtransparency="true" frameborder="0"></iframe>
    </div>

    <script src="script.js"></script>
</body>
</html>
