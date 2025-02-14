<?php
include 'conexao.php';  // Conexão com o banco de dados

// Consultar todos os estabelecimentos pendentes
$sql = "SELECT * FROM estabelecimentos_pendentes";
$result = mysqli_query($mysqli, $sql);

$pendentes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pendentes[] = $row;
}

mysqli_close($mysqli);

// Retornar os dados como JSON
echo json_encode($pendentes);
?>
