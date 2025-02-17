<?php
include './php/conexao.php';  // Conexão com o banco de dados

// Verificar se o ID foi passado
if (!isset($_GET['id'])) {
    die("ID do estabelecimento não fornecido.");
}

$id_estabelecimento = $_GET['id'];

// Buscar dados do estabelecimento
$sql = "SELECT * FROM estabelecimentos WHERE id = $id_estabelecimento";
$result = mysqli_query($mysqli, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Estabelecimento não encontrado.");
}

$estabelecimento = mysqli_fetch_assoc($result);
mysqli_close($mysqli);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estabelecimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-3xl mx-auto p-6">
        <a href="./detalhes_estabelecimento.php?id=<?= $id_estabelecimento ?>" class="text-blue-500 text-sm mb-4 flex items-center">
            ← Voltar para detalhes
        </a>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <h1 class="text-2xl font-bold">Editar Estabelecimento</h1>
            <form action="./php/update_estabelecimento.php" method="POST" class="mt-4">
                <input type="hidden" name="id" value="<?= $id_estabelecimento ?>">

                <label class="block text-sm font-medium text-gray-700">Nome Fantasia</label>
                <input type="text" name="nome_fantasia" value="<?= htmlspecialchars($estabelecimento['nome_fantasia']) ?>" 
                       class="w-full p-2 border rounded-lg mt-1" required>

                <label class="block text-sm font-medium text-gray-700 mt-4">Endereço</label>
                <input type="text" name="endereco" value="<?= htmlspecialchars($estabelecimento['endereco']) ?>" 
                       class="w-full p-2 border rounded-lg mt-1" required>

                <label class="block text-sm font-medium text-gray-700 mt-4">Horário de Recebimento</label>
                <input type="text" name="horario_recebimento" value="<?= htmlspecialchars($estabelecimento['horarios_recebimento']) ?>" 
                       class="w-full p-2 border rounded-lg mt-1" required>

                <label class="block text-sm font-medium text-gray-700 mt-4">Mapa (URL)</label>
                <input type="text" name="mapa" value="<?= htmlspecialchars($estabelecimento['mapa']) ?>" 
                       class="w-full p-2 border rounded-lg mt-1">
                
                <label class="block text-sm font-medium text-gray-700 mt-4">Agilidade</label>
                <select name="agilidade_atendimento" class="w-full p-2 border rounded mt-1 ">
                    <option value="rápido" <?php echo ($estabelecimento['agilidade_atendimento'] == 'rápido') ? 'selected' : ''; ?>>Rápido</option>
                    <option value="médio" <?php echo ($estabelecimento['agilidade_atendimento'] == 'médio') ? 'selected' : ''; ?>>Médio</option>
                    <option value="demorado" <?php echo ($estabelecimento['agilidade_atendimento'] == 'demorado') ? 'selected' : ''; ?>>Demorado</option>
                </select>
                
                <label class="block text-sm font-medium text-gray-700 mt-4">Condições do Local</label>
                <textarea name="condicoes_local" class="w-full p-2 border rounded-lg mt-1"><?= htmlspecialchars($estabelecimento['condicoes_local'] ?? '') ?></textarea>

                <button type="submit" class="mt-4 px-4 py-2 text-white bg-green-500 hover:bg-green-600 rounded-lg text-sm">
                    💾 Salvar Alterações
                </button>
            </form>
        </div>
    </div>

</body>
</html>
