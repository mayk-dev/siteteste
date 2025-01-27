<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT playername, password, ip FROM playerpassword");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erro ao buscar dados: " . $e->getMessage()]);
}
?>
