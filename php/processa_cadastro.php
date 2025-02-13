<?php
include 'conexao.php'; // Arquivo de conexão com o banco

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cnpj = $_POST['cnpj'];
    $nome_fantasia = $_POST['nome_fantasia'];
    $endereco = $_POST['endereco'];
    $horarios_recebimento = $_POST['horarios_recebimento'];
    $agilidade_atendimento = $_POST['agilidade_atendimento'];
    $condicoes_local = $_POST['condicoes_local'];
    $mapa = $_POST['mapa'];

    // Ajustando o código para usar a variável $mysqli corretamente
    $sql = "INSERT INTO estabelecimentos (cnpj, nome_fantasia, endereco, horarios_recebimento, agilidade_atendimento, condicoes_local, mapa) 
            VALUES ('$cnpj', '$nome_fantasia', '$endereco', '$horarios_recebimento', '$agilidade_atendimento', '$condicoes_local', '$mapa')";

    if (mysqli_query($mysqli, $sql)) {
        // Cadastro realizado com sucesso, redireciona para a página principal
        header('Location: /WaitOutSide/index.php');
        exit(); // Certifica-se de que o script seja interrompido após o redirecionamento
    } else {
        // Em caso de erro, exibe a mensagem de erro na mesma página
        echo "Erro: " . mysqli_error($mysqli);
    }

    // Fechando a conexão com o banco de dados
    mysqli_close($mysqli);
}
?>