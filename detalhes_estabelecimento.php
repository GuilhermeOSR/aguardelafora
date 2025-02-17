<?php
include './php/conexao.php';  // Conexão com o banco de dados

// Buscar estabelecimento pelo ID
if (isset($_GET['id'])) {
    $id_estabelecimento = $_GET['id'];
    $sql = "SELECT * FROM estabelecimentos WHERE id = $id_estabelecimento";
    $result = mysqli_query($mysqli, $sql);

    if ($result) {
        $estabelecimento = mysqli_fetch_assoc($result);
    } else {
        die("Estabelecimento não encontrado.");
    }
} else {
    die("ID não fornecido.");
}

mysqli_close($mysqli);

// URL do mapa estático usando a coluna 'mapa' diretamente
$google_maps_url = $estabelecimento['mapa'];  // Assume-se que o valor da coluna 'mapa' já é um link válido para o Google Maps

if (strpos($google_maps_url, 'embed?pb=') === false) {
    // Caso não seja um link de incorporação do Google Maps, tenta construir a URL para o mapa
    // Aqui assumimos que o valor da coluna 'mapa' contém coordenadas como 'latitude,longitude'
    $google_maps_url = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyDA9zdFMI_vSBSFp1U4Afc3D8Loib2-wQI&q=' . urlencode($google_maps_url);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Estabelecimento</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-3xl mx-auto p-6">
        <!-- Voltar -->
        <a href="./consulta.php" class="text-blue-500 text-sm mb-4 flex items-center">
            ← Voltar para a lista
        </a>

        <!-- Card do Estabelecimento -->
        <div class="bg-white p-6 rounded-xl shadow-md" id="card-estabelecimento">
            <h1 class="text-2xl font-bold" id="nome-estabelecimento">Carregando...</h1>
            <a href="#card-avaliacoes" class="">
                <p class="text-yellow-500 text-sm mt-1" id="avaliacao-estabelecimento">Carregando avaliação...
                </p>
            </a>
            
            
        </div>

        <!-- Horários de Recebimento -->
        <div class="bg-white p-4 rounded-xl shadow-md mt-4" id="card-horarios">
            <h2 class="text-lg font-semibold">Horários de Recebimento</h2>
            <ul id="horarios" class="text-gray-600 mt-2">
                <li>Carregando...</li>
            </ul>
        </div>

        <!-- Mapa de Localização -->
        <!-- Mapa de Localização -->
        <div class="bg-white p-4 rounded-xl shadow-md mt-4" id="card-localizacao">
            <h2 class="text-lg font-semibold">Localização</h2>
            <div id="localizacao" class="mt-2">
                <!-- Mapa do Google (Incorporado com iframe) -->
                <div class="mt-5">
                    <iframe
                        src="<?= $google_maps_url ?>" 
                        class="w-full h-64 sm:h-80 lg:h-96"  <!-- Responsividade do mapa -->
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Avaliações -->
        <div class="bg-white p-4 rounded-xl shadow-md mt-4" id="card-avaliacoes">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold">Avaliações</h2>
                <div id="media-avaliacoes" class="flex items-center">
                    <span id="media-texto" class="text-yellow-500 font-semibold">Carregando...</span>
                    <div id="media-estrelas" class="flex ml-2">
                        <!-- As estrelas serão carregadas aqui -->
                    </div>
                </div>
            </div>
            <div id="avaliacoes" class="space-y-4 mt-4">
                <!-- Avaliações serão carregadas aqui -->
            </div>
        </div>
    </div>

    <script>

        // Extraindo latitude e longitude do link do Google Maps
        function getCoordinatesFromMapLink(mapLink) {
            const regex = /@(-?\d+\.\d+),(-?\d+\.\d+)/;
            const match = mapLink.match(regex);
            if (match) {
                return {
                    lat: parseFloat(match[1]),
                    lng: parseFloat(match[2])
                };
            }
            return null; // Retorna null se não encontrar coordenadas
        }

        // Obter o ID do estabelecimento da URL
        const urlParams = new URLSearchParams(window.location.search);
        const estabelecimentoId = urlParams.get('id');

        if (estabelecimentoId) {
            // Fazer a requisição AJAX para o backend
            fetch(`./php/view_estabelecimentos.php?id=${estabelecimentoId}`)
                .then(response => response.json())
                .then(data => {
                    const estabelecimento = data.estabelecimento;
                    const avaliacoes = data.avaliacoes;

                    // Preencher os dados do estabelecimento
                    document.getElementById('nome-estabelecimento').innerText = estabelecimento.nome_fantasia;
                    document.getElementById('avaliacao-estabelecimento').textContent = `⭐ ${estabelecimento.media_avaliacoes}`;


                    // Exibir a média de avaliações com estrelas
                    const mediaAvaliacoes = estabelecimento.media_avaliacoes;
                    const mediaTexto = document.getElementById('media-texto');
                    const mediaEstrelas = document.getElementById('media-estrelas');

                    // Atualizar texto
                    mediaTexto.textContent = `${mediaAvaliacoes}`;

                    // Adicionar as estrelas
                    mediaEstrelas.innerHTML = ''; // Limpar antes de adicionar
                    for (let i = 1; i <= 5; i++) {
                        const estrela = document.createElement('span');
                        estrela.classList.add('text-xl');
                        if (i <= Math.round(mediaAvaliacoes)) {
                            estrela.classList.add('text-yellow-500'); // Estrela cheia
                            estrela.textContent = '★';
                        } else {
                            estrela.classList.add('text-gray-300'); // Estrela vazia
                            estrela.textContent = '★';
                        }
                        mediaEstrelas.appendChild(estrela);
                    }

                    // Exibir horários
                    const horariosElement = document.getElementById('horarios');
                    horariosElement.innerHTML = ''; // Limpar antes de adicionar
                    estabelecimento.horarios.forEach(horario => {
                        const li = document.createElement('li');
                        li.textContent = horario;
                        horariosElement.appendChild(li);
                    });

                    // Exibir avaliações
                    const avaliacoesElement = document.getElementById('avaliacoes');
                    avaliacoesElement.innerHTML = ''; // Limpar antes de adicionar
                    avaliacoes.forEach(avaliacao => {
                        const div = document.createElement('div');
                        div.classList.add('border-b', 'pb-4');
                        div.innerHTML = `<strong>${avaliacao.usuario_nome || 'Usuário Anônimo'}</strong> <span class="text-yellow-500">⭐ ${avaliacao.nota}</span><p class="mt-2">${avaliacao.comentario}</p>`;
                        avaliacoesElement.appendChild(div);
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar os dados do estabelecimento:', error);
                });
        }


    </script>

</body>
</html>
