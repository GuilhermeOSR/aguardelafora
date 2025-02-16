<?php
include 'conexao.php'; // Inclua o arquivo de configuração para conexão com o banco de dados

// Definir o cabeçalho como JSON
header('Content-Type: application/json');

// Obtenha o ID do estabelecimento da URL
$id_estabelecimento = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Se o ID não for válido, retorne um erro em JSON
if ($id_estabelecimento <= 0) {
    echo json_encode(['erro' => 'Estabelecimento não encontrado!']);
    exit;
}

try {
    // Consulta ao banco para obter os dados do estabelecimento
    $query = "SELECT * FROM estabelecimentos WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id_estabelecimento);
    $stmt->execute();
    $result = $stmt->get_result();
    $estabelecimento = $result->fetch_assoc();

    // Se o estabelecimento não for encontrado, retorne um erro em JSON
    if (!$estabelecimento) {
        echo json_encode(['erro' => 'Estabelecimento não encontrado!']);
        exit;
    }

    // Consulta para as avaliações
    $query_avaliacoes = "SELECT * FROM avaliacoes WHERE id_estabelecimento = ?";
    $stmt_avaliacoes = $mysqli->prepare($query_avaliacoes);
    $stmt_avaliacoes->bind_param('i', $id_estabelecimento);
    $stmt_avaliacoes->execute();
    $result_avaliacoes = $stmt_avaliacoes->get_result();

    $avaliacoes = $result_avaliacoes->fetch_all(MYSQLI_ASSOC);

    // Calcular média de avaliações
    $media_avaliacoes = 0;
    $total_avaliacoes = count($avaliacoes);
    if ($total_avaliacoes > 0) {
        $soma_avaliacoes = 0;
        foreach ($avaliacoes as $avaliacao) {
            $soma_avaliacoes += $avaliacao['nota'];
        }
        $media_avaliacoes = round($soma_avaliacoes / $total_avaliacoes, 1);
    }

    // Se o campo de horários de recebimento for um texto, converta para array
    $horarios_recebimento = isset($estabelecimento['horarios_recebimento']) ? explode(',', $estabelecimento['horarios_recebimento']) : [];

    // Resposta JSON com os dados do estabelecimento, avaliações e horários
    echo json_encode([
        'estabelecimento' => [
            'nome_fantasia' => $estabelecimento['nome_fantasia'],
            'media_avaliacoes' => $media_avaliacoes,
            'total_avaliacoes' => $total_avaliacoes,
            'horarios' => $horarios_recebimento,
            'mapa' => $estabelecimento['mapa']
        ],
        'avaliacoes' => $avaliacoes
    ]);
} catch (Exception $e) {
    // Caso ocorra algum erro, retorne o erro em JSON
    echo json_encode(['erro' => 'Erro ao processar a solicitação: ' . $e->getMessage()]);
}
?>