<?php
session_start();

// Verificar se o jogador está logado
if (!isset($_SESSION['playername'])) {
    header("Location: login.php");
    exit;
}

$playername = $_SESSION['playername'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz de Matemática</title>
</head>
<body>
    <a href="dashboard.php">Voltar</a>
    <h1>Quiz de Matemática</h1>
    <p>Complete o quiz e ganhe 100 moedas!</p>

    <form action="quiz_result.php" method="POST">
        <p>1. Qual é o resultado de 5 + 3?</p>
        <input type="text" name="q1" required>

        <p>2. Qual é o resultado de 12 - 4?</p>
        <input type="text" name="q2" required>

        <p>3. Qual é o resultado de 7 x 6?</p>
        <input type="text" name="q3" required>

        <p>4. Qual é o resultado de 9 ÷ 3?</p>
        <input type="text" name="q4" required>

        <p>5. Qual é o resultado de 15 + 5 - 7?</p>
        <input type="text" name="q5" required>

        <input type="submit" value="Finalizar Quiz">
    </form>
</body>
</html>
