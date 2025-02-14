<?php
require_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $cnpj = trim($_POST['cnpj']);
    $nome_fantasia = trim($_POST['nome_fantasia']);
    $endereco = trim($_POST['endereco']);
    $horarios_recebimento = trim($_POST['horarios_recebimento']);
    $agilidade_atendimento = trim($_POST['agilidade_atendimento']);

    $sql = "UPDATE estabelecimentos 
            SET cnpj=?, nome_fantasia=?, endereco=?, horarios_recebimento=?, agilidade_atendimento=? 
            WHERE id=?";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssssi", $cnpj, $nome_fantasia, $endereco, $horarios_recebimento, $agilidade_atendimento, $id);

    if ($stmt->execute()) {
        header("Location: ../admin/estabelecimentos.php");
        exit();
    } else {
        echo "Erro ao atualizar o estabelecimento.";
    }
}
?>
