<?php
require 'db_config.php';
session_start();

$error_message = '';

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
                header("Location: dashboard.php");
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
        <form action="login.php" method="POST">
            <label for="playername">Nome do Jogador:</label>
            <input type="text" id="playername" name="playername" required>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>
