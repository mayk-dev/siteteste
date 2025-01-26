<?php
session_start();

if (!isset($_SESSION['playername'])) {
    header("Location: login.php");
    exit;
}

$playername = $_SESSION['playername'];

// Obter o score do jogo
if (isset($_POST['score'])) {
    $score = (int)$_POST['score'];

    // Configurações do banco de dados
    $host = "190.102.40.72:3306";
    $dbname = "s13949_playermoney";
    $username = "u13949_t0TMhJWuxh";
    $password = "BeuGYHF.7oo@!mIvC=ezcYkB";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }

    // Obter o valor atual das moedas
    $stmt = $pdo->prepare("SELECT valor FROM solaryeconomy WHERE name = :playername");
    $stmt->bindParam(':playername', $playername);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_balance = $row['valor'];

        // Adicionar as moedas ganhas no jogo
        $new_balance = $current_balance + $score;

        // Atualizar o valor das moedas no banco de dados
        $update_stmt = $pdo->prepare("UPDATE solaryeconomy SET valor = :new_balance WHERE name = :playername");
        $update_stmt->bindParam(':new_balance', $new_balance);
        $update_stmt->bindParam(':playername', $playername);
        $update_stmt->execute();

        echo "<p>Parabéns! Você ganhou $score moedas no jogo e elas foram adicionadas ao seu saldo!</p>";
    } else {
        echo "<p>Erro: Jogador não encontrado na base de dados.</p>";
    }
} else {
    echo "<p>Erro: Nenhum score recebido.</p>";
}

echo "<p><a href='painel/dashboard.php'>Voltar ao Dashboard</a></p>";
?>
