<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Estabelecimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#057D0F', // Verde Vivo
                        secondary: '#0D141A', // Preto Azulado
                        accent: '#1D1E20', // Cinza Escuro
                        background: '#FFFFFF', // Branco
                        text: '#121317' // Preto Grafite
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-text min-h-screen flex items-center justify-center p-4">
    <div class="bg-secondary text-background p-6 rounded-2xl shadow-xl w-full max-w-md">
        <h1 class="text-2xl font-bold text-center mb-6 text-primary">Cadastro de Estabelecimento</h1>
        
        <form action="./php/processa_cadastro.php" method="POST" class="space-y-4">
            <div>
                <label class="block font-medium mb-1">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary outline-none bg-accent text-background" required>
            </div>
            
            <div>
                <label class="block font-medium mb-1">Nome Fantasia</label>
                <input type="text" id="nome_fantasia" name="nome_fantasia" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary outline-none bg-accent text-background" required>
            </div>
            
            <div>
                <label class="block font-medium mb-1">Endereço</label>
                <input type="text" id="endereco" name="endereco" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary outline-none bg-accent text-background" required>
            </div>
            
            <div>
                <label class="block font-medium mb-1">Horários de Recebimento</label>
                <textarea id="horarios_recebimento" name="horarios_recebimento" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary outline-none bg-accent text-background" required></textarea>
            </div>
            
            <div>
                <label class="block font-medium mb-1">Agilidade no Atendimento</label>
                <select id="agilidade_atendimento" name="agilidade_atendimento" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary outline-none bg-accent text-background" required>
                    <option value="rapido">Rápido</option>
                    <option value="medio">Médio</option>
                    <option value="demorado">Demorado</option>
                </select>
            </div>
            
            <div>
                <label class="block font-medium mb-1">Condições do Local</label>
                <textarea id="condicoes_local" name="condicoes_local" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary outline-none bg-accent text-background" required></textarea>
            </div>
            
            <div>
                <label class="block font-medium mb-1">Mapa (URL do Google Maps)</label>
                <input type="url" id="mapa" name="mapa" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-primary outline-none bg-accent text-background" required>
            </div>
            
            <button type="submit" class="w-full bg-primary text-background p-3 rounded-lg font-semibold hover:bg-green-800 transition-all duration-200 ease-in-out">
                Cadastrar Estabelecimento
            </button>
        </form>
    </div>
</body>
</html>
