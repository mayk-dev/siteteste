<?php
session_start();

// Verificar se o jogador está logado
if (!isset($_SESSION['playername'])) {
    header("Location: index.php");
    exit;
}

$playername = $_SESSION['playername'];

// Configurações do banco de dados
$host = "190.102.40.72:3306";
$dbname = "s13949_playermoney";
$username = "u13949_t0TMhJWuxh";
$password = "BeuGYHF.7oo@!mIvC=ezcYkB";

// Conectar ao banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Função para obter o status do servidor Minecraft e jogadores online usando cURL
function getMinecraftServerStatus($ip, $port) {
    $url = "https://api.mcsrvstat.us/2/{$ip}:{$port}"; // API pública para status do servidor Minecraft
    
    // Inicializar o cURL
    $ch = curl_init();
    
    // Definir opções do cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36");
    
    // Executar cURL e pegar a resposta
    $data = curl_exec($ch);
    
    // Verificar se houve erro na requisição
    if (curl_errno($ch)) {
        return null; // Em caso de erro com o cURL
    }
    
    // Fechar a sessão cURL
    curl_close($ch);
    
    // Decodificar o JSON e retornar
    return json_decode($data, true);
}

// Defina o IP e a porta do servidor
$server_ip = '190.102.40.96';
$server_port = '26646';

// Obter status do servidor
$server_status = getMinecraftServerStatus($server_ip, $server_port);

// Verificar se o servidor retornou dados válidos
if ($server_status && isset($server_status['players']['online'])) {
    $online_players = $server_status['players']['online'];
    $player_list = $server_status['players']['list'];
} else {
    $online_players = 0;
    $player_list = [];
}

// Gerar a URL da imagem da cabeça do jogador (skin)
$head_url = "https://minotar.net/helm/" . urlencode($playername) . "/100.png"; // Tamanho da imagem = 100px

// Consultar o valor das moedas do jogador na tabela 'solaryeconomy'
$stmt = $pdo->prepare("SELECT valor FROM solaryeconomy WHERE name = :playername");
$stmt->bindParam(':playername', $playername);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $money = $row['valor']; // O valor das moedas do jogador
} else {
    $money = 0; // Caso o jogador não tenha saldo registrado
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r136/three.min.js"></script>

</head>
<body>
    <div class="cordefundo"></div>
    <h1>Bem-vindo ao Dashboard</h1>
    <div><a href="quiz.php">Responda e Ganhe Coins</a></div>
    <div><a href="game.php">Responda e Ganhe Coins 2</a></div>

    <p class="jogador"><strong>Jogador:</strong> <?php echo htmlspecialchars($playername); ?></p>

    <!-- Exibir a imagem da cabeça do jogador -->
    <h2></h2>
    <img class="cabeca" src="<?php echo $head_url; ?>" alt="Cabeça do jogador" style="width: 100px; height: 100px; border-radius: 50%;">

    <h2>Jogadores Online no Servidor Minecraft</h2>
    <p class="online"><strong>Total de Jogadores Online:</strong> <?php echo $online_players; ?></p>

    <?php if ($online_players > 0): ?>
        <ul>
            <?php foreach ($player_list as $player): ?>
                <li><?php echo htmlspecialchars($player); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Não há jogadores online no momento.</p>
    <?php endif; ?>

    <!-- Exibir o saldo do jogador -->
    <h2>Saldo de Moedas</h2>
    <p class="moedas"><strong>Coins :</strong> <?php echo number_format($money, 2, ',', '.'); ?> </p>

    <p><a href="logout.php">Sair</a></p>

    <script>
        // Nome do jogador
        const playername = "<?php echo htmlspecialchars($playername); ?>";

        // URL da skin do jogador
        const skinUrl = `https://minotar.net/skin/${playername}`;

        // Configurar a cena 3D
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, 300 / 400, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer();

        // Configurar o canvas 3D
        const skinViewer = document.getElementById('skin-viewer');
        renderer.setSize(skinViewer.offsetWidth, skinViewer.offsetHeight);
        skinViewer.appendChild(renderer.domElement);

        // Adicionar luz
        const light = new THREE.AmbientLight(0xffffff, 1); // Luz ambiente
        scene.add(light);

        // Carregar textura da skin
        const loader = new THREE.TextureLoader();
        loader.load(skinUrl, (texture) => {
            // Criar o modelo do jogador (caixa básica com a skin aplicada)
            const geometry = new THREE.BoxGeometry(1, 2, 1);
            const materials = [
                new THREE.MeshBasicMaterial({ map: texture }), // Frente
                new THREE.MeshBasicMaterial({ map: texture }), // Verso
                new THREE.MeshBasicMaterial({ map: texture }), // Topo
                new THREE.MeshBasicMaterial({ map: texture }), // Fundo
                new THREE.MeshBasicMaterial({ map: texture }), // Lado esquerdo
                new THREE.MeshBasicMaterial({ map: texture }), // Lado direito
            ];
            const player = new THREE.Mesh(geometry, materials);
            scene.add(player);

            // Animação da skin
            function animate() {
                requestAnimationFrame(animate);
                player.rotation.y += 0.01; // Rotação contínua
                renderer.render(scene, camera);
            }

            animate();
        });

        // Configurar a câmera
        camera.position.z = 3;
    </script>
</body>
</html>
