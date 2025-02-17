<?php
require_once 'conexao.php';

// Consulta para contar estabelecimentos cadastrados por mês
$sql = "SELECT DATE_FORMAT(data_criacao, '%b') AS mes, COUNT(*) AS total
        FROM estabelecimentos
        WHERE YEAR(data_criacao) = YEAR(CURDATE())
        GROUP BY mes
        ORDER BY MONTH(data_criacao)";
$result = $mysqli->query($sql);

$labels = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['mes'];
    $data[] = $row['total'];
}

// Consulta para contar estabelecimentos pendentes por mês
$sql_pendencias = "SELECT DATE_FORMAT(data_cadastro, '%b') AS mes, COUNT(*) AS total
                   FROM estabelecimentos_pendentes
                   WHERE YEAR(data_cadastro) = YEAR(CURDATE())
                   GROUP BY mes
                   ORDER BY MONTH(data_cadastro)";
$result_pendencias = $mysqli->query($sql_pendencias);

$labels_pendencias = [];
$data_pendencias = [];

while ($row = $result_pendencias->fetch_assoc()) {
    $labels_pendencias[] = $row['mes'];
    $data_pendencias[] = $row['total'];
}

// Consulta para contagem total
$sql_total = "SELECT COUNT(*) AS total FROM estabelecimentos";
$result_total = $mysqli->query($sql_total);
$estabelecimentos_count = $result_total->fetch_assoc()['total'] ?? 0;

$sql_pendentes = "SELECT COUNT(*) AS total FROM estabelecimentos_pendentes";
$result_pendentes = $mysqli->query($sql_pendentes);
$pendentes_count = $result_pendentes->fetch_assoc()['total'] ?? 0;

// Consultar estabelecimentos pendentes recentes
$sql_pendentes_recentes = "SELECT id, nome_fantasia, data_cadastro FROM estabelecimentos_pendentes ORDER BY data_cadastro DESC LIMIT 2";
$result_pendentes_recentes = $mysqli->query($sql_pendentes_recentes);

$pendentes_recentes = [];
while ($row = $result_pendentes_recentes->fetch_assoc()) {
    $pendentes_recentes[] = $row;
}

echo json_encode([
    'labels' => $labels,
    'data' => $data,
    'labels_pendencias' => $labels_pendencias,
    'data_pendencias' => $data_pendencias,
    'estabelecimentos_count' => $estabelecimentos_count,
    'pendentes_count' => $pendentes_count,
    'pendentes_recentes' => $pendentes_recentes
]);
?>