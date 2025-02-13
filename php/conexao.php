<?php
// Definindo as variáveis para a conexão
$host = 'localhost'; // Ou o IP do servidor MySQL
$user = 'root'; // Seu usuário do MySQL
$pass = ''; // Senha do MySQL (deixe vazio se não houver senha)
$db = 'waitoutside'; // O nome do banco de dados

// Estabelecendo a conexão com o banco
$mysqli = new mysqli($host, $user, $pass, $db);

// Verificando se houve erro na conexão
if ($mysqli->connect_error) {
    die("Erro na conexão: " . $mysqli->connect_error);
}
?>