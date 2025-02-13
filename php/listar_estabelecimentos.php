<?php
require_once 'conexao.php';

$search = isset($_GET['search']) ? $_GET['search'] : ''; // Captura a busca

$sql = "
    SELECT 
        e.id, 
        e.nome_fantasia, 
        e.cnpj, 
        e.endereco, 
        e.horarios_recebimento, 
        e.agilidade_atendimento, 
        e.condicoes_local, 
        e.mapa,
        COALESCE(AVG(a.nota), 0) AS media_avaliacao, 
        COUNT(a.id) AS total_avaliacoes
    FROM estabelecimentos e
    LEFT JOIN avaliacoes a ON e.id = a.id_estabelecimento
    WHERE e.nome_fantasia LIKE '%" . $mysqli->real_escape_string($search) . "%'  -- Filtra pelo nome_fantasia
    GROUP BY e.id
    ORDER BY media_avaliacao DESC
";

$result = $mysqli->query($sql);

$estabelecimentos = [];

while ($row = $result->fetch_assoc()) {
    $estabelecimentos[] = [
        'nome_fantasia' => $row['nome_fantasia'],
        'cnpj' => $row['cnpj'],
        'endereco' => $row['endereco'],
        'horarios_recebimento' => $row['horarios_recebimento'],
        'agilidade_atendimento' => $row['agilidade_atendimento'],
        'condicoes_local' => $row['condicoes_local'],
        'mapa' => $row['mapa'],
        'media_avaliacao' => number_format($row['media_avaliacao'], 1), // Média formatada com 1 casa decimal
        'total_avaliacoes' => $row['total_avaliacoes']
    ];
}

header('Content-Type: application/json');
echo json_encode($estabelecimentos);
?>