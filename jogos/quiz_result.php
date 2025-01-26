<?php
session_start();

if (!isset($_SESSION['playername'])) {
    header("Location: login.php");
    exit;
}

$playername = $_SESSION['playername'];

// Definir as respostas corretas
$respostas_corretas = [
    'q1' => 8,    // 5 + 3
    'q2' => 8,    // 12 - 4
    'q3' => 42,   // 7 x 6
    'q4' => 3,    // 9 ÷ 3
    'q5' => 13    // 15 + 5 - 7
];

// Verificar as respostas do jogador
$acertos = 0;
foreach ($respostas_corretas as $key => $valor_correto) {
    if (isset($_POST[$key]) && $_POST[$key] == $valor_correto) {
        $acertos++;
    }
}

// Se o jogador acertou todas as questões, adicionar 100 moedas
if ($acertos == 5) {
    // Conectar ao banco de dados
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

        // Adicionar 100 moedas
        $new_balance = $current_balance + 100;

        // Atualizar o valor das moedas no banco de dados
        $update_stmt = $pdo->prepare("UPDATE solaryeconomy SET valor = :new_balance WHERE name = :playername");
        $update_stmt->bindParam(':new_balance', $new_balance);
        $update_stmt->bindParam(':playername', $playername);
        $update_stmt->execute();
    }

    echo "<p>Parabéns! Você completou o quiz corretamente e ganhou 100 moedas!</p>";
} else {
    echo "<p>Você não acertou todas as questões. Tente novamente.</p>";
}

echo "<p><a href='dashboard.php'>Voltar ao Dashboard</a></p>";
?>
