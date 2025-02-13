<?php
include 'conexao.php';

function checkAuth($token) {
    global $mysqli;  // Usando a variável de conexão definida em 'conexao.php'

    // Verifica se o token existe no banco e se ainda é válido
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE token = ? AND token_expira > NOW()");
    $stmt->bind_param("s", $token);  // "s" indica que o parâmetro é uma string
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        // Token inválido ou expirado
        return ['erro' => 'Token inválido ou expirado'];
    }

    // Token válido
    return ['sucesso' => 'Token válido'];
}

// Verifica se o token foi enviado
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
    
    // Verificar o token no banco de dados
    $result = checkAuth($token);

    if (isset($result['erro'])) {
        // Retorna erro se o token for inválido
        echo json_encode($result);
    } else {
        // Retorna sucesso se o token for válido
        echo json_encode($result);
    }
} else {
    // Caso o token não tenha sido fornecido
    echo json_encode(['erro' => 'Token não fornecido']);
}
?>
