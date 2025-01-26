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
    <title>Jogo de Clique Rápido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .dot {
            width: 30px;
            height: 30px;
            background-color: red;
            border-radius: 50%;
            position: absolute;
            cursor: pointer;
        }
        #game-area {
            position: relative;
            width: 100%;
            height: 500px;
            background-color: #f0f0f0;
            border: 2px solid #ccc;
            margin: 20px auto;
            overflow: hidden;
        }
        #timer {
            font-size: 24px;
            margin: 20px;
        }
        #score {
            font-size: 24px;
        }
        #claim-form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Jogo de Clique Rápido</h1>
    <p>Clique nos pontos (".") o máximo que puder em 30 segundos!</p>
    <div id="timer">Tempo restante: 30s</div>
    <div id="score">Pontuação: 0</div>
    <div id="game-area"></div>

    <!-- Formulário para fazer o claim das moedas -->
    <form id="claim-form" action="claim_rewards.php" method="POST" style="display: none;">
        <input type="hidden" name="score" id="final-score">
        <button type="submit">Claimar Moedas</button>
    </form>

    <script>
        const gameArea = document.getElementById('game-area');
        const timerElement = document.getElementById('timer');
        const scoreElement = document.getElementById('score');
        const claimForm = document.getElementById('claim-form');
        const finalScoreInput = document.getElementById('final-score');

        let score = 0;
        let timeLeft = 30;
        let gameInterval;

        // Função para criar um ponto aleatório na tela
        function createDot() {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            dot.style.top = Math.random() * (gameArea.offsetHeight - 30) + 'px';
            dot.style.left = Math.random() * (gameArea.offsetWidth - 30) + 'px';

            // Quando o ponto for clicado
            dot.addEventListener('click', () => {
                score++;
                scoreElement.textContent = `Pontuação: ${score}`;
                gameArea.removeChild(dot);
                createDot(); // Criar outro ponto após o clique
            });

            gameArea.appendChild(dot);
        }

        // Iniciar o jogo
        function startGame() {
            createDot();

            gameInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = `Tempo restante: ${timeLeft}s`;

                if (timeLeft <= 0) {
                    clearInterval(gameInterval);
                    endGame();
                }
            }, 1000);
        }

        // Finalizar o jogo
        function endGame() {
            timerElement.textContent = 'Tempo esgotado!';
            gameArea.innerHTML = '';
            claimForm.style.display = 'block';
            finalScoreInput.value = score;
        }

        // Iniciar o jogo automaticamente ao carregar a página
        startGame();
    </script>
</body>
</html>
