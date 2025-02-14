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
// Função para animar o contador
function animateCounter(id, currentValue, targetValue) {


    // Não iniciar a animação se os valores forem iguais ou ambos zero
    if (currentValue === targetValue || targetValue === 0) return;

    const increment = (targetValue + currentValue) / 50;
    const element = document.getElementById(id);

    const interval = setInterval(function() {
        currentValue += increment;
        
        if ((increment > 0 && currentValue >= targetValue) || (increment < 0 && currentValue <= targetValue)) {
            clearInterval(interval);
            element.innerText = targetValue;
        } else {
            element.innerText = Math.floor(currentValue);
        }
    }, 5);
}

// Definir os valores para os estabelecimentos e pendências
const estabelecimentosCount = <?php echo $estabelecimentos_count; ?>;
const pendenciasCount = <?php echo $pendentes_count; ?>;

// Iniciar as animações
animateCounter('estabelecimentosCount', 0, estabelecimentosCount);
animateCounter('pendenciasCount', 0, pendenciasCount);

async function atualizarDados() {
    try {
        const response = await fetch('../php/ajax_atualizar.php');
        const data = await response.json();

        if (data.estabelecimentos_count !== estabelecimentosCount) {
            animateCounter('estabelecimentosCount', estabelecimentosCount, data.estabelecimentos_count);
        }
        if (data.pendentes_count !== pendenciasCount) {
            animateCounter('pendenciasCount', pendenciasCount, data.pendentes_count);
        }

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

        // Atualiza os dados do gráfico
        chartCombinado.data.labels = data.labels;
        chartCombinado.data.datasets[0].data = data.data;
        chartCombinado.data.datasets[1].data = data.data_pendencias;
        chartCombinado.update();
    } catch (error) {
        console.error('Erro ao atualizar os dados:', error);
    }
}

// Atualizar a cada 5 segundos
setInterval(atualizarDados, 500);

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
