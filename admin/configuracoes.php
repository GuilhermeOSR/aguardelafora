<?php
include '../php/conexao.php';  // Conexão com o banco de dados

// Lógica para buscar estabelecimentos pendentes do banco
$pendentes = []; // Inicializa o array de pendentes

// Buscar todos os estabelecimentos pendentes
$sql = "SELECT * FROM estabelecimentos_pendentes";
$result = mysqli_query($mysqli, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pendentes[] = $row;
    }
}

// Aprovar ou Rejeitar estabelecimento
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['aprovar'])) {
        $id_estabelecimento = $_POST['id_estabelecimento'];

        // Aprovar: Move da tabela pendentes para estabelecimentos
        $sqlAprovar = "INSERT INTO estabelecimentos (cnpj, nome_fantasia, endereco, horarios_recebimento, agilidade_atendimento, condicoes_local, mapa) 
                       SELECT cnpj, nome_fantasia, endereco, horarios_recebimento, agilidade_atendimento, condicoes_local, mapa 
                       FROM estabelecimentos_pendentes WHERE id = $id_estabelecimento";
        $sqlExcluir = "DELETE FROM estabelecimentos_pendentes WHERE id = $id_estabelecimento";

        mysqli_query($mysqli, $sqlAprovar);
        mysqli_query($mysqli, $sqlExcluir);
    }

    if (isset($_POST['rejeitar'])) {
        $id_estabelecimento = $_POST['id_estabelecimento'];
        // Rejeitar: Remove da tabela pendentes
        $sqlExcluir = "DELETE FROM estabelecimentos_pendentes WHERE id = $id_estabelecimento";
        mysqli_query($mysqli, $sqlExcluir);
    }
}

mysqli_close($mysqli);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Admin</title>
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
                    <a href="#" class="block p-2 bg-blue-700 rounded flex justify-between">
                        <span>Pendências</span>
                        <span id="notification" class="hidden bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded-full ml-2">0</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <h2 class="text-2xl font-semibold mb-5">Configurações</h2>
            
            <!-- Aprovação de estabelecimentos -->
            <div>
                <h3 class="text-xl font-bold mb-3">Aprovar Estabelecimentos</h3>
                <div class="bg-white dark:bg-gray-800 p-5 rounded shadow-md">
                    <!-- Exibindo a lista de estabelecimentos pendentes -->
<!-- Exibindo a lista de estabelecimentos pendentes -->
<ul id="pendingEstablishments">
    <?php foreach ($pendentes as $estabelecimento): ?>
        <li class="flex flex-col sm:flex-row items-center sm:items-start p-3 border-b dark:border-gray-700">
            <!-- Nome do Estabelecimento -->
            <span class="text-center sm:text-left w-full mb-3 sm:mb-0"><?= $estabelecimento['nome_fantasia']; ?></span>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row sm:space-x-4 w-full sm:w-auto">
            <form method="POST" class="w-full sm:w-auto mb-2 sm:mb-0">
    <input type="hidden" name="id_estabelecimento" value="<?= $estabelecimento['id']; ?>">
    <button type="submit" name="aprovar" class="bg-green-500 text-white px-4 py-2 rounded w-full sm:w-auto">Aprovar</button>
</form>
<form method="POST" class="w-full sm:w-auto mb-2 sm:mb-0">
    <input type="hidden" name="id_estabelecimento" value="<?= $estabelecimento['id']; ?>">
    <button type="submit" name="rejeitar" class="bg-red-500 text-white px-4 py-2 rounded w-full sm:w-auto">Rejeitar</button>
</form>

<!-- Link para página de detalhes, estilizado como um botão -->
<a href="detalhes_estabelecimento.php?id=<?= $estabelecimento['id']; ?>" class="bg-blue-500 text-white px-4 py-2 rounded w-full sm:w-auto mt-2 sm:mt-0 text-center inline-block">
    Detalhes
</a>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Atualizar notificação de pendências (exemplo estático para simulação)
        let pendencias = <?= count($pendentes); ?>;
        const notification = document.getElementById('notification');

        if (pendencias > 0) {
            notification.textContent = pendencias;
            notification.classList.remove('hidden');
        }

        function atualizarPendencias() {
            fetch('../php/ajax_atualizar.php')
                .then(response => response.json())
                .then(data => {
                    const notification = document.getElementById('notification');
                    if (data.pendentes_count > 0) {
                        notification.textContent = data.pendentes_count;
                        notification.classList.remove('hidden');
                    } else {
                        notification.classList.add('hidden');
                    }
                })
                .catch(error => console.error('Erro ao atualizar pendências:', error));
        }

        // Atualizar a cada 10 segundos
        setInterval(atualizarPendencias, 500);
    </script>
</body>
</html>
