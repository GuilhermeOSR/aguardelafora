<?php
require_once 'conexao.php'; // Arquivo de conexão com o banco

$nome_usuario = $_POST['nome_usuario'];
$email = $_POST['email'];
$senha = $_POST['senha'];

// Hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Ajustando o código para usar a variável $mysqli corretamente
$sql = "INSERT INTO usuarios (nome_usuario, email, senha) VALUES (?, ?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $nome_usuario, $email, $senha_hash); // Para strings, use "sss"

if ($stmt->execute()) {
    // Redirecionar para a página de consulta após o envio da avaliação
    header("Location: ../inicio.php");
    exit();  // Certificar-se de que o script pare após o redirecionamento
} else {
    // Em caso de erro, exibe a mensagem de erro
    echo "Erro ao cadastrar o usuário: " . $stmt->error;
}

// Fechar conexão
$stmt->close();
$mysqli->close();
?>
