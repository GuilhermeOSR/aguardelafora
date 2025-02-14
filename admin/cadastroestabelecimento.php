<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Estabelecimento</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white min-h-screen flex items-center justify-center">
    <div class="bg-white text-gray-900 p-8 rounded-lg shadow-lg w-full max-w-lg">
        <h1 class="text-2xl font-bold text-center mb-6 text-blue-600">Cadastro de Estabelecimento</h1>
        
        <form action="../php/processa_cadastro.php" method="POST" class="space-y-4">
            <div>
                <label class="block font-medium">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
            </div>
            
            <div>
                <label class="block font-medium">Nome Fantasia</label>
                <input type="text" id="nome_fantasia" name="nome_fantasia" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
            </div>
            
            <div>
                <label class="block font-medium">Endereço</label>
                <input type="text" id="endereco" name="endereco" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
            </div>
            
            <div>
                <label class="block font-medium">Horários de Recebimento</label>
                <textarea id="horarios_recebimento" name="horarios_recebimento" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required></textarea>
            </div>
            
            <div>
                <label class="block font-medium">Agilidade no Atendimento</label>
                <select id="agilidade_atendimento" name="agilidade_atendimento" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
                    <option value="rapido">Rápido</option>
                    <option value="medio">Médio</option>
                    <option value="demorado">Demorado</option>
                </select>
            </div>
            
            <div>
                <label class="block font-medium">Condições do Local</label>
                <textarea id="condicoes_local" name="condicoes_local" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required></textarea>
            </div>
            
            <div>
                <label class="block font-medium">Mapa (URL do Google Maps)</label>
                <input type="url" id="mapa" name="mapa" class="w-full p-2 border rounded focus:ring focus:ring-blue-300" required>
            </div>

            <input type="hidden" name="origem" value="admin">
            
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">Cadastrar Estabelecimento</button>
        </form>
    </div>
</body>
</html>
