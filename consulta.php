<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Estabelecimentos</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        let page = 1;
        let isLoading = false;
        let hasMore = true;

        // Verifica token no localStorage
        window.onload = function() {
            const token = localStorage.getItem('token');
            
            if (!token) {
                window.location.href = 'index.php';
            } else {
                fetch('./php/verify_token.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        window.location.href = 'index.php';
                    } else {
                        carregarEstabelecimentos();
                    }
                })
                .catch(error => {
                    console.error('Erro ao verificar o token:', error);
                    window.location.href = 'index.php';
                });
            }
        };

        function carregarEstabelecimentos(novaBusca = false) {
            if (isLoading || !hasMore) return;
            isLoading = true;

            if (novaBusca) {
                page = 1;
                document.getElementById('estabelecimentos').innerHTML = "";
                hasMore = true;
            }

            const search = document.getElementById('search').value;
            fetch(`./php/listar_estabelecimentos.php?search=${encodeURIComponent(search)}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('estabelecimentos');
                    
                    if (data.length > 0) {
                        data.forEach(estabelecimento => {
                            container.innerHTML += `
                                <a href="detalhes_estabelecimento.php?id=${estabelecimento.id}" 
                                   class="block bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                                    <h3 class="text-lg font-semibold">${estabelecimento.nome_fantasia}</h3>
                                    <p class="text-yellow-500 text-sm">⭐ ${estabelecimento.media_avaliacao} / 5.0 (${estabelecimento.total_avaliacoes} avaliações)</p>
                                </a>
                            `;
                        });
                        page++;
                    } else {
                        hasMore = false;
                    }

                    isLoading = false;
                })
                .catch(error => {
                    console.error('Erro ao carregar estabelecimentos:', error);
                    isLoading = false;
                });
        }

        // Detecta o scroll e carrega mais estabelecimentos
        window.addEventListener('scroll', () => {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
                carregarEstabelecimentos();
            }
        });
    </script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-semibold text-center mb-6">Consulta de Estabelecimentos</h1>

        <!-- Barra de pesquisa -->
        <input type="text" id="search" placeholder="Pesquisar estabelecimento..." 
               class="w-full p-3 border border-gray-300 rounded-md shadow-sm mb-4 focus:ring focus:ring-blue-400"
               oninput="carregarEstabelecimentos(true)" />

        <div id="estabelecimentos" class="grid gap-4"></div>
    </div>
</body>
</html>
