<?php
// Conectar ao banco de dados
require_once 'conexao.php';

// Recuperar os dados do formulário
$id_estabelecimento = $_POST['id_estabelecimento'];
$comentario = $_POST['comentario'];
$nota = $_POST['nota'];
$data = date('Y-m-d H:i:s');  // Data e hora atual

// Inserir dados na tabela "avaliacoes"
$sql = "INSERT INTO avaliacoes (id_estabelecimento, comentario, nota, data) 
        VALUES (?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("isis", $id_estabelecimento, $comentario, $nota, $data);

if ($stmt->execute()) {
    // Redirecionar para a página de consulta após o envio da avaliação
    header("Location: ../detalhes_estabelecimento.php?id=" . $id_estabelecimento);
    exit();  // Certificar-se de que o script pare após o redirecionamento
} else {
    // Em caso de erro, exibe a mensagem de erro
    echo "Erro ao cadastrar a avaliação: " . $stmt->error;
}

// Fechar conexão
$stmt->close();
$mysqli->close();
?>
