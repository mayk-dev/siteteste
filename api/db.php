<?php
$host = "190.102.40.72:3306";
$dbname = "s13949_playermoney";
$username = "u13949_t0TMhJWuxh";
$password = "BeuGYHF.7oo@!mIvC=ezcYkB";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>
