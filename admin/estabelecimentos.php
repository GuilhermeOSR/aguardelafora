<?php
require_once '../php/conexao.php';

// Consulta para obter os estabelecimentos
$sql = "SELECT id, nome_fantasia, endereco, horarios_recebimento FROM estabelecimentos";
$result = $mysqli->query($sql);


// Consulta para contar o número de estabelecimentos pendentes
$sql_pendentes = "SELECT COUNT(*) AS total FROM estabelecimentos_pendentes";
$result_pendentes = $mysqli->query($sql_pendentes);
$pendentes_count = $result_pendentes->fetch_assoc()['total'] ?? 0;




?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estabelecimentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav class="bg-blue-600 text-white w-64 p-5 hidden md:block">
            <h1 class="text-2xl font-bold mb-5">Admin</h1>
            <ul>
                <li class="mb-3"><a href="./" class="block p-2 hover:bg-blue-500 rounded">Dashboard</a></li>
                <li class="mb-3"><a href="#" class="block p-2 bg-blue-500 rounded">Estabelecimentos</a></li>
                <li class="mb-3"><a href="#" class="block p-2 hover:bg-blue-500 rounded">Ajudantes</a></li>
                <li class="mb-3 relative">
                    <a href="./configuracoes.php" class="block p-2 bg-blue-700 rounded flex justify-between">
                        <span>Pendências</span>
                        <?php if ($pendentes_count > 0): ?>
                            <span id="notification" class="bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded-full ml-2">
                                <?php echo $pendentes_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <h2 class="text-2xl font-semibold mb-5">Estabelecimentos</h2>
            
            <!-- Botão Adicionar -->
            <div class="mb-5">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" onclick="window.location.href='./cadastroestabelecimento.php'"> Adicionar Novo</button>
            </div>
            
            <!-- Tabela de Estabelecimentos -->
            <div class="overflow-x-auto bg-white p-5 rounded shadow-md">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="py-2 px-4 text-left">Nome</th>
                            <th class="py-2 px-4 text-left">Endereço</th>
                            <th class="py-2 px-4 text-left">Horários</th>
                            <th class="py-2 px-4 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr class="border-t">
                                <td class="py-2 px-4"><?php echo htmlspecialchars($row['nome_fantasia']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($row['endereco']); ?></td>
                                <td class="py-2 px-4"><?php echo htmlspecialchars($row['horarios_recebimento']); ?></td>
                                <td class="py-2 px-4 text-center">
                                    <button onclick="window.location.href='./editestabelecimentos.php?id=<?php echo $row['id']; ?>'" class="text-blue-600 hover:underline mr-2">Editar</button>
                                    <button onclick="confirmarExclusao(<?php echo $row['id']; ?>)" class="text-red-600 hover:underline">Excluir</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    function confirmarExclusao(id) {
        if (confirm("Tem certeza que deseja excluir este estabelecimento?")) {
            window.location.href = "../php/delete_estabelecimento.php?id=" + id;
        }
}

async function atualizarDados() {
    try {
        const response = await fetch('../php/ajax_atualizar.php');
        const data = await response.json();


        // Atualiza o contador de pendências no menu
        const notificationBadge = document.getElementById('notification');
        if (notificationBadge) {
            if (data.pendentes_count > 0) {
                notificationBadge.innerText = data.pendentes_count;
                notificationBadge.style.display = 'inline-block';
            } else {
                notificationBadge.style.display = 'none';
            }
        }

    } catch (error) {
        console.error('Erro ao atualizar os dados:', error);
    }
}

// Atualizar a cada 5 segundos
setInterval(atualizarDados, 500);



</script>
</body>
</html>


