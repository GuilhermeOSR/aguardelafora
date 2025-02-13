<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Estabelecimentos</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
     // Verificar se o token está no localStorage e carregar estabelecimentos
     window.onload = function() {
        const token = localStorage.getItem('token');
        
        if (!token) {
            // Caso não haja token, redireciona para o login
            window.location.href = 'index.php';
        } else {
            // Verificar a validade do token no backend (via AJAX)
            fetch('./php/verify_token.php', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json' // Assegura que o backend saiba que está esperando um JSON
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    // Se o backend retornar erro (token inválido ou expirado), redireciona
                    window.location.href = 'index.php';
                } else {
                    // Se o token for válido, carregue os estabelecimentos
                    carregarEstabelecimentos();
                }
            })
            .catch(error => {
                console.error('Erro ao verificar o token:', error);
                window.location.href = 'index.php';
            });
        }
    };

    function carregarEstabelecimentos() {
        const search = document.getElementById('search').value;
        
        fetch(`./php/listar_estabelecimentos.php?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('estabelecimentos');
                container.innerHTML = "";

                if (data.length > 0) {
                    data.forEach(estabelecimento => {
                        // Adicionar caminho para id relacionado ao estabelecimento
                        container.innerHTML += `
                            <a href="detalhes_estabelecimento.php?id=${estabelecimento.id}" 
                               class="block bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                                <h3 class="text-lg font-semibold">${estabelecimento.nome_fantasia}</h3>
                                <p class="text-yellow-500 text-sm">⭐ ${estabelecimento.media_avaliacao} / 5.0 (${estabelecimento.total_avaliacoes} avaliações)</p>
                            </a>
                        `;
                    });
                } else {
                    container.innerHTML = "<p class='text-center text-gray-600'>Nenhum estabelecimento encontrado.</p>";
                }
            })
            .catch(error => console.error('Erro ao carregar estabelecimentos:', error));
    }
    </script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold text-center mb-6">Consulta de Estabelecimentos</h1>

        <!-- Barra de pesquisa -->
        <input type="text" id="search" placeholder="Pesquisar estabelecimento..." 
               class="w-full p-3 border border-gray-300 rounded-md shadow-sm mb-4 focus:ring focus:ring-blue-400"
               oninput="carregarEstabelecimentos()" />

        <div id="estabelecimentos" class="grid gap-4"></div>
    </div>
</body>
</html>
