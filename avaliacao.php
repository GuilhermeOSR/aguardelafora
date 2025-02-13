<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar Estabelecimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Avaliar Estabelecimento</h2>
        <form action="./php/processa_avaliacao.php" method="POST" class="space-y-4">
            <div>
                <label for="estabelecimento" class="block text-gray-700 font-medium">Estabelecimento:</label>
                <select name="id_estabelecimento" id="estabelecimento" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                    <?php 
                    require_once 'php/conexao.php';
                    $sql = "SELECT id, nome_fantasia FROM estabelecimentos";
                    $result = $mysqli->query($sql);
                    while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id']; ?>"><?= $row['nome_fantasia']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label for="comentario" class="block text-gray-700 font-medium">Comentário:</label>
                <textarea name="comentario" id="comentario" rows="4" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300"></textarea>
            </div>

            <div>
                <label for="nota" class="block text-gray-700 font-medium">Nota (1 a 5):</label>
                <input type="number" name="nota" id="nota" min="1" max="5" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Enviar Avaliação</button>
        </form>
    </div>
</body>
</html>
