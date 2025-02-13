<?php
require_once 'conexao.php'; // Arquivo de conexão com o banco

// Recebe os dados do login
if (isset($_POST['email']) && isset($_POST['senha'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
} else {
    echo "Erro: Dados do formulário não recebidos!";
    exit();  // Para interromper a execução, caso o formulário não tenha sido enviado corretamente
}

// Verifica se o usuário existe no banco
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Verifica a senha
if ($usuario && password_verify($senha, $usuario['senha'])) {
    // Gerar o token (pode ser JWT ou qualquer outra estratégia)
    $token = bin2hex(random_bytes(16)); // Exemplo de token gerado

    // Atualizar o token no banco para o usuário logado
    $sql = "UPDATE usuarios SET token = ?, token_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('si', $token, $usuario['id']);
    $stmt->execute();

    // Não precisamos mais atualizar o banco, pois o token será enviado diretamente para o cliente
    // Envia o token para o cliente
    echo json_encode(['message' => 'Login bem-sucedido', 'token' => $token]);
} else {
    // Caso de erro de login
    echo json_encode(['message' => 'Usuário ou senha inválidos']);
}

// Fechar conexões
$stmt->close();
$mysqli->close();
?>
