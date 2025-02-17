<?php
include '../php/conexao.php';  // Conexão com o banco de dados

// Buscar estabelecimento pelo ID
if (isset($_GET['id'])) {
    $id_estabelecimento = $_GET['id'];
    $sql = "SELECT * FROM estabelecimentos_pendentes WHERE id = $id_estabelecimento";
    $result = mysqli_query($mysqli, $sql);

    if ($result) {
        $estabelecimento = mysqli_fetch_assoc($result);
    } else {
        die("Estabelecimento não encontrado.");
    }
} else {
    die("ID não fornecido.");
}

mysqli_close($mysqli);

// URL do mapa estático usando a coluna 'mapa' diretamente
$google_maps_url = $estabelecimento['mapa'];  // Assume-se que o valor da coluna 'mapa' já é um link válido para o Google Maps

if (strpos($google_maps_url, 'embed?pb=') === false) {
    // Caso não seja um link de incorporação do Google Maps, tenta construir a URL para o mapa
    // Aqui assumimos que o valor da coluna 'mapa' contém coordenadas como 'latitude,longitude'
    $google_maps_url = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyDA9zdFMI_vSBSFp1U4Afc3D8Loib2-wQI&q=' . urlencode($google_maps_url);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Estabelecimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav class="bg-blue-600 text-white w-64 p-5 hidden md:block">
            <h1 class="text-2xl font-bold mb-5">Admin</h1>
            <ul>
                <li class="mb-3"><a href="./" class="block p-2 hover:bg-blue-500 rounded">Dashboard</a></li>
                <li class="mb-3"><a href="./estabelecimentos.php" class="block p-2 hover:bg-blue-500 rounded">Estabelecimentos</a></li>
                <li class="mb-3"><a href="#" class="block p-2 hover:bg-blue-500 rounded">Ajudantes</a></li>
                <li class="mb-3 relative">
                    <a href="./configuracoes.php" class="block p-2 bg-blue-700 rounded">Pendências</a>
                </li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="flex-1 p-5 lg:p-6">
            <h2 class="text-2xl font-semibold mb-5">Detalhes do Estabelecimento</h2>
            
            <!-- Botão de Voltar -->
            <a href="./configuracoes.php" class="text-blue-600 hover:text-blue-800 text-sm mb-3 inline-block">
                &#8592; Voltar para Configurações
            </a>

            <div class="bg-white dark:bg-gray-800 p-5 rounded shadow-md">
                <h3 class="text-xl font-bold mb-3"><?= $estabelecimento['nome_fantasia']; ?></h3>
                <p><strong>CNPJ:</strong> <?= $estabelecimento['cnpj']; ?></p>
                <p><strong>Endereço:</strong> <?= $estabelecimento['endereco']; ?></p>
                <p><strong>Horários de Funcionamento:</strong> <?= $estabelecimento['horarios_recebimento']; ?></p>
                <p><strong>Agilidade no Atendimento:</strong> <?= $estabelecimento['agilidade_atendimento']; ?></p>
                <p><strong>Condições do Local:</strong> <?= $estabelecimento['condicoes_local']; ?></p>

                <!-- Mapa do Google (Incorporado com iframe) -->
        <div class="bg-white p-3 rounded-xl shadow-md mt-4" id="card-localizacao">
            <h2 class="text-lg font-semibold">Localização</h2>
            <div id="localizacao" class="mt-2">
                <!-- Mapa do Google (Incorporado com iframe) -->
                <div class="mt-5">
                    <iframe
                        src="<?= $google_maps_url ?>" 
                        class="w-full h-64 sm:h-70 lg:h-50"  <!-- Responsividade do mapa -->
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
       
                <!-- Botões de Aprovar/Rejeitar -->
                <div class="mt-5">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_estabelecimento" value="<?= $estabelecimento['id']; ?>">
                        <button type="submit" name="aprovar" class="bg-green-500 text-white px-4 py-2 rounded">Aprovar</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_estabelecimento" value="<?= $estabelecimento['id']; ?>">
                        <button type="submit" name="rejeitar" class="bg-red-500 text-white px-4 py-2 rounded ml-2">Rejeitar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
