<?php
require_once '../php/conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do estabelecimento não fornecido.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM estabelecimentos WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Estabelecimento não encontrado.");
}

$estabelecimento = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estabelecimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav class="bg-blue-600 text-white w-64 p-5 hidden md:block">
            <h1 class="text-2xl font-bold mb-5">Admin</h1>
            <ul>
                <li class="mb-3"><a href="./" class="block p-2 hover:bg-blue-500 rounded">Dashboard</a></li>
                <li class="mb-3"><a href="./estabelecimentos.php" class="block p-2 bg-blue-500 rounded">Estabelecimentos</a></li>
                <li class="mb-3"><a href="#" class="block p-2 hover:bg-blue-500 rounded">Ajudantes</a></li>
                <li class="mb-3"><a href="#" class="block p-2 hover:bg-blue-500 rounded">Pendências</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <h2 class="text-2xl font-semibold mb-5">Editar Estabelecimento</h2>

            <!-- Formulário de Edição -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-md">
                <form action="../php/update_estabelecimento.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $estabelecimento['id']; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- CNPJ -->
                        <div>
                            <label class="block text-sm font-medium">CNPJ</label>
                            <input type="text" name="cnpj" class="w-full p-2 border rounded mt-1 dark:bg-gray-700 dark:border-gray-600" value="<?php echo htmlspecialchars($estabelecimento['cnpj']); ?>">
                        </div>
                        <!-- Razão Social -->

                        <!-- Nome Fantasia -->
                        <div>
                            <label class="block text-sm font-medium">Nome Fantasia</label>
                            <input type="text" name="nome_fantasia" class="w-full p-2 border rounded mt-1 dark:bg-gray-700 dark:border-gray-600" value="<?php echo htmlspecialchars($estabelecimento['nome_fantasia']); ?>">
                        </div>
                        <!-- Endereço -->
                        <div>
                            <label class="block text-sm font-medium">Endereço</label>
                            <input type="text" name="endereco" class="w-full p-2 border rounded mt-1 dark:bg-gray-700 dark:border-gray-600" value="<?php echo htmlspecialchars($estabelecimento['endereco']); ?>">
                        </div>
                    </div>

                    <!-- Dias e Horários de Recebimento -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium">Dias e Horários de Recebimento</label>
                        <input type="text" name="horarios_recebimento" class="w-full p-2 border rounded mt-1 dark:bg-gray-700 dark:border-gray-600" value="<?php echo htmlspecialchars($estabelecimento['horarios_recebimento']); ?>">
                    </div>

                    <!-- Agilidade no Recebimento -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium">Agilidade no Recebimento</label>
                        <select name="agilidade_atendimento" class="w-full p-2 border rounded mt-1 dark:bg-gray-700 dark:border-gray-600">
                            <option value="rápido" <?php echo ($estabelecimento['agilidade_atendimento'] == 'rápido') ? 'selected' : ''; ?>>Rápido</option>
                            <option value="médio" <?php echo ($estabelecimento['agilidade_atendimento'] == 'médio') ? 'selected' : ''; ?>>Médio</option>
                            <option value="demorado" <?php echo ($estabelecimento['agilidade_atendimento'] == 'demorado') ? 'selected' : ''; ?>>Demorado</option>
                        </select>
                    </div>
                  
                    <!-- Botões -->
                    <div class="mt-6 flex justify-between">
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Salvar</button>
                        <button onclick="window.location.href='./estabelecimentos.php'" type="button" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>