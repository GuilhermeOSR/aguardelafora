<?php
require_once '../php/conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Garante que seja um número inteiro

    $sql = "DELETE FROM estabelecimentos WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Estabelecimento excluído com sucesso!'); window.location.href = '../admin/estabelecimentos.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir estabelecimento.'); window.location.href = '../admin/estabelecimentos.php';</script>";
    }
}
?>
