<?php
// Inclui a conexão com o banco de dados
require_once '../php/conexao.php';

// Consulta para contar estabelecimentos cadastrados por mês
$sql = "SELECT DATE_FORMAT(data_criacao, '%b') AS mes, COUNT(*) AS total
        FROM estabelecimentos
        WHERE YEAR(data_criacao) = YEAR(CURDATE())
        GROUP BY mes
        ORDER BY MONTH(data_criacao)";

$result = $mysqli->query($sql);

$labels = [];
$data = [];

//Estabelecimentos (gráfico)
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['mes']; // Nome do mês
    $data[] = $row['total']; // Quantidade de estabelecimentos
}

// Convertendo para JSON para uso no JavaScript
$labels_json = json_encode($labels);
$data_json = json_encode($data);

// Consulta para contar estabelecimentos pendentes por mês
$sql_pendencias = "SELECT DATE_FORMAT(data_cadastro, '%b') AS mes, COUNT(*) AS total
                   FROM estabelecimentos_pendentes
                   WHERE YEAR(data_cadastro) = YEAR(CURDATE())
                   GROUP BY mes
                   ORDER BY MONTH(data_cadastro)";

$result_pendencias = $mysqli->query($sql_pendencias);

$labels_pendencias = [];
$data_pendencias = [];

//Estabelecimentos pendentes (gráfico)
while ($row = $result_pendencias->fetch_assoc()) {
    $labels_pendencias[] = $row['mes'];
    $data_pendencias[] = $row['total'];
}

// Convertendo para JSON para uso no JavaScript
$labels_pendencias_json = json_encode($labels_pendencias);
$data_pendencias_json = json_encode($data_pendencias);

// Consulta para contar o número total de estabelecimentos cadastrados
$sql_total = "SELECT COUNT(*) AS total FROM estabelecimentos";
$result_total = $mysqli->query($sql_total);
$estabelecimentos_count = $result_total->fetch_assoc()['total'] ?? 0;

// Consulta para contar o número de estabelecimentos pendentes
$sql_pendentes = "SELECT COUNT(*) AS total FROM estabelecimentos_pendentes";
$result_pendentes = $mysqli->query($sql_pendentes);
$pendentes_count = $result_pendentes->fetch_assoc()['total'] ?? 0;

//Pendencias Recentes


$sql_pendentes_recentes = "SELECT id, nome_fantasia, data_cadastro FROM estabelecimentos_pendentes ORDER BY data_cadastro DESC LIMIT 2";
$result_pendentes_recentes = $mysqli->query($sql_pendentes_recentes);

$pendentes_recentes = [];
while ($row = $result_pendentes_recentes->fetch_assoc()) {
    $pendentes_recentes[] = $row;
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
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    
    <!-- Sidebar -->
    <div class="flex h-screen">
        <nav class="bg-blue-600 text-white w-64 p-5 hidden md:block">
            <h1 class="text-2xl font-bold mb-5">Admin</h1>
            <ul>
                <li class="mb-3"><a href="./" class="block p-2 hover:bg-blue-500 rounded">Dashboard</a></li>
                <li class="mb-3"><a href="./estabelecimentos.php" class="block p-2 hover:bg-blue-500 rounded">Estabelecimentos</a></li>
                <li class="mb-3"><a href="#" class="block p-2 hover:bg-blue-500 rounded">Ajudantes</a></li>
                <li class="mb-3 relative">
                    <a href="./configuracoes.php" class="block p-2 bg-blue-700 rounded flex justify-between">
                        <span>Pendências</span>
                        <?php if ($pendentes_count > 0): ?>
                            <span id="notification" class="bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded-full ml-2" >
                                <?php echo $pendentes_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <h2 class="text-2xl font-semibold mb-5">Dashboard</h2>
            
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-5 rounded shadow-md text-center">
                    <h3 id="estabelecimentosCount" class="text-xl font-bold text-blue-600">0</h3>
                    <p>Estabelecimentos</p>
                </div>
                <div class="bg-white p-5 rounded shadow-md text-center">
                    <h3 class="text-xl font-bold text-green-600">35</h3>
                    <p>Ajudantes Cadastrados</p>
                </div>
                <div class="bg-white p-5 rounded shadow-md text-center">
                    <h3 id="pendenciasCount" class="text-2xl font-bold text-yellow-500">0</h3>
                    <p>Pendências</p>
                </div>
            </div>

            <!-- Estabelecimentos Pendentes Recentes -->
<div class="bg-white p-5 rounded shadow-md mb-6">
                <h3 class="text-lg font-semibold mb-3">Estabelecimentos Pendentes </h3>
                <ul id="listaPendentes" class="space-y-3">
    <?php foreach ($pendentes_recentes as $pendente): ?>
        <li class="flex justify-between items-center">
            <span><?php echo $pendente['nome_fantasia']; ?></span>
            <div class="ml-4">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id_estabelecimento" value="<?php echo $pendente['id']; ?>">
                    <button type="submit" name="aprovar" class="bg-green-500 text-white px-2 py-1 rounded">Aprovar</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id_estabelecimento" value="<?php echo $pendente['id']; ?>">
                    <button type="submit" name="rejeitar" class="bg-red-500 text-white px-2 py-1 rounded ml-2">Rejeitar</button>
                </form>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
                <a href="configuracoes.php" class="block text-center text-blue-600 mt-4 hover:underline">Ver mais</a>
            </div>
            
            <!-- Gráficos -->
            <div class="grid grid-cols-1 md:grid-cols-4">
                <div class="bg-white p-8 rounded col-4 shadow-md">
                    <h3 class="text-lg font-semibold mb-3">Dados</h3>
                    <canvas id="chartCombinado"></canvas>
                </div>
            </div>

        </div>
    </div>


    <script>

async function atualizarDados() {
    try {
        const response = await fetch('../php/ajax_atualizar.php');
        const data = await response.json();

        // Atualiza os contadores
        document.getElementById('estabelecimentosCount').innerText = data.estabelecimentos_count;
        document.getElementById('pendenciasCount').innerText = data.pendentes_count;

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

        // Atualiza a lista de pendentes
        const listaPendentes = document.getElementById('listaPendentes');
        if (listaPendentes) {
            listaPendentes.innerHTML = ""; // Limpa a lista
            data.pendentes_recentes.forEach(pendente => {
                const li = document.createElement('li');
                li.className = "flex justify-between items-center";
                li.innerHTML = `
                    <span>${pendente.nome_fantasia}</span>
                    <div class="ml-4">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_estabelecimento" value="${pendente.id}">
                            <button type="submit" name="aprovar" class="bg-green-500 text-white px-2 py-1 rounded">Aprovar</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_estabelecimento" value="${pendente.id}">
                            <button type="submit" name="rejeitar" class="bg-red-500 text-white px-2 py-1 rounded ml-2">Rejeitar</button>
                        </form>
                    </div>
                `;
                listaPendentes.appendChild(li);
            });
        }

    } catch (error) {
        console.error('Erro ao atualizar os dados:', error);
    }
}

// Atualiza os dados a cada 10 segundos
setInterval(atualizarDados, 300);

// Dados para o gráfico
const labelsEstabelecimentos = <?php echo $labels_json; ?>;
const dataEstabelecimentos = <?php echo $data_json; ?>;
const labelsPendencias = <?php echo $labels_pendencias_json; ?>;
const dataPendencias = <?php echo $data_pendencias_json; ?>;

const ctx = document.getElementById('chartCombinado').getContext('2d');
const chartCombinado = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: labelsEstabelecimentos,
        datasets: [
            {
                label: 'Estabelecimentos Cadastrados',
                data: dataEstabelecimentos,
                backgroundColor: '#2563eb',
            },
            {
                label: 'Pendências',
                data: dataPendencias,
                backgroundColor: '#F59E0B',
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
        }
    }
});

//Atualizar contador de pendencias no menu

    </script>
</body>
</html>
