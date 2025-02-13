<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/styleinicio.css">
    <title>Aguarde Lá Fora</title>
    <script>
     // Verificar se o token está no localStorage
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
            }
        })
        .catch(error => {
            console.error('Erro ao verificar o token:', error);
            window.location.href = 'index.php';
        });
    }
};
 </script>
</head>
<body>
    <header>
    <div class="container">
            <h1><img src="imgs/logo-aguarde-la-fora.jpg" height="80" width="80" alt=""><span>Aguarde Lá Fora</span></h1>
        <div class="menu-h">
            <button class="btn-h" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
    </header>

    <section class="content-main">
        <div class="main-logo">
            <img src="imgs/logo-aguarde-la-fora.jpg" width="460" height="200" alt="Logo">
        </div>
        
        <div class="btn-container">
            <button onclick="window.location.href='consulta.php'">Consultar Estabelecimento</button>
            <button onclick="window.location.href='cadastro.php'">Cadastrar Estabelecimento</button>
            <button>Ajudante Freelancer</button>
            <button>Quem somos</button>
            <button onclick="window.location.href='./admin/index.html'">(Temporário) Admin</button>
        </div>
    </section>
</body>
</html>