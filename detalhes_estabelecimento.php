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
$google_maps_url = $estabelecimento['mapa'];

if (strpos($google_maps_url, 'embed?pb=') === false) {
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
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold" id="nome-estabelecimento">Carregando...</h1>

                <!-- Botão Editar -->
                <a href="./editar_estabelecimento.php?id=<?= $id_estabelecimento ?>" 
                   class="px-4 py-2 text-white bg-blue-500 hover:bg-blue-600 rounded-lg text-sm">
                   ✏️ Editar
                </a>
            </div>

            <a href="#card-avaliacoes">
                <p class="text-yellow-500 text-sm mt-1" id="avaliacao-estabelecimento">Carregando avaliação...</p>
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
        <div class="bg-white p-4 rounded-xl shadow-md mt-4" id="card-localizacao">
            <h2 class="text-lg font-semibold">Localização</h2>
            <div id="localizacao" class="mt-2">
                <div class="mt-5">
                    <iframe src="<?= $google_maps_url ?>" 
                        class="w-full h-64 sm:h-80 lg:h-96"
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Formulário de Cadastro de Avaliação Compacto -->
        <div class="bg-white p-4 rounded-xl shadow-md mt-4" id="card-cadastro-avaliacao">
            <h2 class="text-lg font-semibold mb-4">Deixe sua Avaliação</h2>
            <form action="./php/processa_avaliacao.php" method="post">
                <!-- Campo oculto com o id do estabelecimento -->
                <input type="hidden" name="id_estabelecimento" value="<?= $id_estabelecimento ?>">
                <!-- Campo oculto para armazenar a nota selecionada -->
                <input type="hidden" name="nota" id="nota" value="0">
                
                <!-- Campo para Comentário -->
                <div class="mb-4">
                    <textarea name="comentario" id="comentario" rows="3" class="w-full p-2 border rounded" placeholder="Escreva seu comentário aqui"></textarea>
                </div>
                
                <!-- Estrelas Interativas para Seleção de Nota -->
                <div class="flex items-center mb-4">
                    <span class="star cursor-pointer text-gray-300 text-2xl" data-value="1">★</span>
                    <span class="star cursor-pointer text-gray-300 text-2xl" data-value="2">★</span>
                    <span class="star cursor-pointer text-gray-300 text-2xl" data-value="3">★</span>
                    <span class="star cursor-pointer text-gray-300 text-2xl" data-value="4">★</span>
                    <span class="star cursor-pointer text-gray-300 text-2xl" data-value="5">★</span>
                </div>
                
                <!-- Botão de Envio -->
                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                    Enviar Avaliação
                </button>
            </form>
        </div>



        <!-- Avaliações -->
<!-- Avaliações -->
<div class="bg-white p-4 rounded-xl shadow-md mt-4" id="card-avaliacoes">
  <div class="flex justify-between items-center">
    <h2 class="text-lg font-semibold">Avaliações</h2>
    <div id="media-avaliacoes" class="flex items-center">
      <span id="media-texto" class="text-yellow-500 font-semibold">Carregando...</span>
      <div id="media-estrelas" class="flex ml-2"></div>
    </div>
  </div>
  <!-- Container das avaliações com altura fixa e scroll interno -->
  <div id="avaliacoes-container" class="mt-4">
    <div id="avaliacoes" class="space-y-4 max-h-96 overflow-y-auto"></div>
    <div id="verMaisContainer" class="mt-4"></div>
  </div>
</div>

        <script>
  const urlParams = new URLSearchParams(window.location.search);
  const estabelecimentoId = urlParams.get('id');

  // Variáveis para controle da paginação das avaliações
  let allReviews = [];      // Aqui armazenaremos todas as avaliações recebidas do backend
  let currentOffset = 0;    // Índice atual
  const limit = 5;          // Quantidade de avaliações a exibir por vez

  const reviewsContainer = document.getElementById('avaliacoes');

  if (estabelecimentoId) {
    fetch(`./php/view_estabelecimentos.php?id=${estabelecimentoId}`)
      .then(response => response.json())
      .then(data => {
        const estabelecimento = data.estabelecimento;
        allReviews = data.avaliacoes; // Armazena todas as avaliações

        document.getElementById('nome-estabelecimento').innerText = estabelecimento.nome_fantasia;
        document.getElementById('avaliacao-estabelecimento').textContent = `⭐ ${estabelecimento.media_avaliacoes}`;

        const mediaAvaliacoes = estabelecimento.media_avaliacoes;
        const mediaTexto = document.getElementById('media-texto');
        const mediaEstrelas = document.getElementById('media-estrelas');

        mediaTexto.textContent = `${mediaAvaliacoes}`;
        mediaEstrelas.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
          const estrela = document.createElement('span');
          estrela.classList.add('text-xl');
          estrela.textContent = '★';
          if (i <= Math.round(mediaAvaliacoes)) {
            estrela.classList.add('text-yellow-500');
          } else {
            estrela.classList.add('text-gray-300');
          }
          mediaEstrelas.appendChild(estrela);
        }

        const horariosElement = document.getElementById('horarios');
        horariosElement.innerHTML = '';
        estabelecimento.horarios.forEach(horario => {
          const li = document.createElement('li');
          li.textContent = horario;
          horariosElement.appendChild(li);
        });

        // Inicia a exibição das avaliações paginadas
        reviewsContainer.innerHTML = '';
        currentOffset = 0;
        renderNextReviews();
      })
      .catch(error => {
        console.error('Erro ao carregar os dados do estabelecimento:', error);
      });
  }

  // Função que renderiza o próximo conjunto de avaliações
  function renderNextReviews() {
    const chunk = allReviews.slice(currentOffset, currentOffset + limit);
    chunk.forEach(avaliacao => {
      // Converte a data para um objeto Date e formata somente a data (ex: "17/02/2025")
      const dataAvaliacao = new Date(avaliacao.data);
      const dataFormatada = dataAvaliacao.toLocaleDateString('pt-BR');

      const div = document.createElement('div');
      div.classList.add('border-b', 'pb-4');
      div.innerHTML = `
          <div class="flex justify-between items-center">
              <strong>${avaliacao.usuario_nome || 'Usuário Anônimo'}</strong>
              <small class="text-gray-500 text-sm">${dataFormatada}</small>
          </div>
          <div class="mt-2">
              <span class="text-yellow-500">⭐ ${avaliacao.nota}</span>
              <p class="mt-2">${avaliacao.comentario}</p>
          </div>
      `;
      reviewsContainer.appendChild(div);
    });

    currentOffset += limit;
    // Se ainda houver avaliações, renderiza (ou re-renderiza) o botão "Ver mais"
    if (currentOffset < allReviews.length) {
      renderVerMaisButton();
    } else {
      removeVerMaisButton();
    }
  }

  // Cria ou exibe o botão "Ver mais"
  function renderVerMaisButton() {
    let verMaisButton = document.getElementById('verMaisButton');
    if (!verMaisButton) {
      verMaisButton = document.createElement('button');
      verMaisButton.id = 'verMaisButton';
      verMaisButton.className = 'w-full bg-gray-200 text-blue-500 p-2 rounded mt-4';
      verMaisButton.textContent = 'Ver mais';
      verMaisButton.addEventListener('click', function() {
        verMaisButton.remove();
        renderNextReviews();
        // Opcional: rolar suavemente para o novo conteúdo
        verMaisButton.scrollIntoView({ behavior: 'smooth' });
      });
      // Adiciona o botão logo após o container de avaliações
      reviewsContainer.parentElement.appendChild(verMaisButton);
    }
  }

  // Remove o botão "Ver mais" se não houver mais avaliações
  function removeVerMaisButton() {
    const verMaisButton = document.getElementById('verMaisButton');
    if (verMaisButton) {
      verMaisButton.remove();
    }
  }

  // Lógica das estrelas interativas (já existente)
  const stars = document.querySelectorAll('.star');
  let rating = 0;
  
  stars.forEach(star => {
    star.addEventListener('mouseover', function() {
      const value = parseInt(this.getAttribute('data-value'));
      highlightStars(value);
    });
    star.addEventListener('mouseout', function() {
      highlightStars(rating);
    });
    star.addEventListener('click', function() {
      rating = parseInt(this.getAttribute('data-value'));
      document.getElementById('nota').value = rating;
      highlightStars(rating);
    });
  });
  
  function highlightStars(ratingValue) {
    stars.forEach(star => {
      const starValue = parseInt(star.getAttribute('data-value'));
      if (starValue <= ratingValue) {
        star.classList.remove('text-gray-300');
        star.classList.add('text-yellow-500');
      } else {
        star.classList.remove('text-yellow-500');
        star.classList.add('text-gray-300');
      }
    });
  }
</script>

    </div>
</body>
</html>
