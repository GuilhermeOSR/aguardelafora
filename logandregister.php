    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login e Registro</title>
        <!-- Link do TailwindCSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-200 flex justify-center items-center min-h-screen">

        <!-- Container principal -->
        <div class="bg-white p-6 rounded-lg shadow-lg w-full sm:w-96 md:w-1/3 lg:w-1/4 xl:w-1/5 h-auto max-w-lg transition-all duration-300">

            <!-- Aba de navegação (Login / Registro) -->
            <div class="flex justify-between mb-4">
                <button id="login-tab" class="w-full text-center py-2 px-4 font-medium text-gray-600 bg-gray-100 rounded-l-lg focus:outline-none hover:bg-gray-300">Login</button>
                <button id="register-tab" class="w-full text-center py-2 px-4 font-medium text-gray-600 bg-gray-100 rounded-r-lg focus:outline-none hover:bg-gray-300">Registrar</button>
            </div>

            <!-- Formulário de Login -->
            <div id="login-form" class="space-y-4 opacity-0 transform scale-90 transition-all duration-500 ease-in-out">
                <form id="loginForm" action="./php/login.php" method="POST">
                    <input type="email" name="email" placeholder="E-mail" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400">
                    <input type="password" name="senha" placeholder="Senha" id="senha" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400">
                    <button class="w-full py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700">Entrar</button>
                </form>
            </div>

            <!-- Formulário de Registro -->

            <div id="register-form" class="space-y-4 opacity-0 transform scale-90 transition-all duration-500 ease-in-out hidden">
                <form action="./php/processa_cadastro_users.php" method="POST">
                    <input type="text" name="nome_usuario" placeholder="Nome de Usuário" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400" required>
                    <input type="email" name="email" placeholder="E-mail" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400" required>
                    <input type="password" name="senha" placeholder="Senha" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400" required>
                    <button type="submit" class="w-full py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700">Registrar</button>
                </form>
            </div>

            <!-- Links para alternar entre login e registro -->
            <div class="text-center mt-4">
                <span id="toggle-text" class="text-gray-600 cursor-pointer hover:text-gray-800">Ainda não tem uma conta? Registre-se</span>
            </div>
        </div>

        <script>
            // Alternar entre o formulário de Login e Registro
            const loginTab = document.getElementById("login-tab");
            const registerTab = document.getElementById("register-tab");
            const loginForm = document.getElementById("login-form");
            const registerForm = document.getElementById("register-form");
            const toggleText = document.getElementById("toggle-text");

            loginTab.addEventListener("click", () => {
                loginForm.classList.remove("hidden");
                registerForm.classList.add("hidden");
                loginForm.classList.remove("opacity-0", "scale-90");
                loginForm.classList.add("opacity-100", "scale-100");
                registerForm.classList.remove("opacity-100", "scale-100");
                registerForm.classList.add("opacity-0", "scale-90");

                loginTab.classList.add("bg-gray-200");
                registerTab.classList.remove("bg-gray-200");
                toggleText.textContent = "Ainda não tem uma conta? Registre-se";
            });

            registerTab.addEventListener("click", () => {
                registerForm.classList.remove("hidden");
                loginForm.classList.add("hidden");
                registerForm.classList.remove("opacity-0", "scale-90");
                registerForm.classList.add("opacity-100", "scale-100");
                loginForm.classList.remove("opacity-100", "scale-100");
                loginForm.classList.add("opacity-0", "scale-90");

                registerTab.classList.add("bg-gray-200");
                loginTab.classList.remove("bg-gray-200");
                toggleText.textContent = "Já tem uma conta? Faça login";
            });

            // Alternar entre os textos de login e registro
            toggleText.addEventListener("click", () => {
                if (loginForm.classList.contains("hidden")) {
                    loginForm.classList.remove("hidden");
                    registerForm.classList.add("hidden");
                    loginForm.classList.remove("opacity-0", "scale-90");
                    loginForm.classList.add("opacity-100", "scale-100");
                    registerForm.classList.remove("opacity-100", "scale-100");
                    registerForm.classList.add("opacity-0", "scale-90");

                    loginTab.classList.add("bg-gray-200");
                    registerTab.classList.remove("bg-gray-200");
                    toggleText.textContent = "Ainda não tem uma conta? Registre-se";
                } else {
                    registerForm.classList.remove("hidden");
                    loginForm.classList.add("hidden");
                    registerForm.classList.remove("opacity-0", "scale-90");
                    registerForm.classList.add("opacity-100", "scale-100");
                    loginForm.classList.remove("opacity-100", "scale-100");
                    loginForm.classList.add("opacity-0", "scale-90");

                    registerTab.classList.add("bg-gray-200");
                    loginTab.classList.remove("bg-gray-200");
                    toggleText.textContent = "Já tem uma conta? Faça login";
                }
            });

            document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const senha = document.getElementById("senha").value.trim();

    if (!email || !senha) {
        alert("Preencha todos os campos.");
        return;
    }

    fetch("./php/login.php", {
        method: "POST",
        body: new URLSearchParams({
            email: email,
            senha: senha
        }),
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.token) {
            localStorage.setItem("token", data.token);
            alert("Login bem-sucedido! Você será redirecionado.");
            window.location.href = "inicio.php";
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Erro ao fazer login:", error);
        alert("Ocorreu um erro, tente novamente.");
    });
});
        </script>

    </body>
    </html>
