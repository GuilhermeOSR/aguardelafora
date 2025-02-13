<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Avaliações Recentes</h2>

        <?php
        require_once 'php/conexao.php';

        $sql = "SELECT a.id, a.comentario, a.nota, a.data, e.nome_fantasia
                FROM avaliacoes a
                JOIN estabelecimentos e ON a.id_estabelecimento = e.id
                ORDER BY a.data DESC";

        $result = $mysqli->query($sql);
        ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-white p-4 rounded-lg shadow-md mb-4 border-l-4 border-blue-500">
                    <h3 class="text-lg font-semibold text-gray-800">📍 <?= $row['nome_fantasia']; ?></h3>
                    <p class="text-gray-600 mt-2"><strong>Comentário:</strong> "<?= $row['comentario']; ?>"</p>
                    <div class="flex items-center mt-2">
                        <p class="text-yellow-500 text-xl">⭐ <?= $row['nota']; ?> / 5</p>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">📅 <?= date('d/m/Y', strtotime($row['data'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-gray-500">Nenhuma avaliação encontrada.</p>
        <?php endif; ?>

        <?php $mysqli->close(); ?>
    </div>
</body>
</html>
