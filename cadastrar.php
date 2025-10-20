<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <style>
        body{
            background-color: #111111;
        }

        header{
            display: flex;
            justify-content: center;
            align-items: center;
            max-height: 60px;
            position: relative;
        }

        h1{
            color: white;
            font-family: Arial, Helvetica, sans-serif;
        }

        h3{
            color: white;
            font-family: Arial, Helvetica, sans-serif;
        }

        form{
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            width: 100%;
        }

        input{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            background-color: black;
            color: white;
            border: none;
            height: 8%;
            width: 30%;
            border-radius: 10px;
            padding-left: 5px;
        }

        .button{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #8A2BE2;
            color: white;
            border: none;
            height: 10%;
            width: 30%;
            margin-top: 20px;
            border-radius: 20px;
            transition: 0.2s;
            cursor: pointer;
        }

        .button:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        a{
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .button-log{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            position: absolute; 
            left: 20px;
            align-items: center;
            justify-content: center;
            background-color: #8A2BE2;
            color: white;
            border: none;
            width: 10%;
            cursor: pointer;
            border-radius: 20px;
            transition: 0.2s;
        }

        .button-log:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        .p-conta{
            color: white;
            margin: 11px 0;
        }

        .p-login{
            color: #8A2BE2;
            margin: 5px 0;
        }

        @media (max-width: 800px) {
            input{
                width: 70%;
            }

            .button{
                width: 70%;
            }

            .button-log{
                width: 15%;
            }

            header{
                justify-content: center;
            }

            .button-log{
                width: 15%;
                left: 0;
            }
        }
    </style>
</head>
<body>

    <header>
        <a href="index.php">
        <button class="button-log">Fazer login</button>
        </a>
        <h1>Crie sua conta!</h1>
    </header>
    
    <form id="formCadastro" action="cria_usuario.php" method="POST" onsubmit="return enviar()">
        <h3>Insira o nome</h3>
        <input type="text" maxlength="40" name="c-nome" id="name" placeholder="Nome" value="<?php echo $_POST['c-nome'] ?? ''; ?>">

        <h3>Insira o email</h3>
        <input type="email" maxlength="255" name="c-email" id="email" placeholder="Email" value="<?php echo $_POST['c-email'] ?? ''; ?>">

        <h3>Insira a senha</h3>
        <input type="password" minlength="6" name="c-senha" id="senha" placeholder="Senha" value="<?php echo $_POST['c-senha'] ?? ''; ?>">

        <h3>Confirme a senha</h3>
        <input type="password" minlength="6" name="c-reSenha" id="reSenha" placeholder="Confirme a Senha" value="<?php echo $_POST['c-reSenha'] ?? ''; ?>">

        <div class="g-recaptcha" data-sitekey="6LevQ0orAAAAANHpOQFzlJkHndBPRTD1Jsg2JleE"></div>
        
        <input class="button" type="submit" value="Enviar">

        <p class="p-conta">Já tem uma conta?</p>

        <a href="index.php"><p class="p-login">Fazer login</p></a>
        
    </form>



</body>
<script>
    let name = document.getElementById("name")
    let email = document.getElementById("email")
    let senha = document.getElementById("senha")
    let reSenha = document.getElementById("reSenha")

    function enviar() {
        if(name.value.length == 0) {
            alert("Preencha seu nome!");
            return false;
        }
        if(email.value.length == 0) {
            alert("Preencha seu email!");
            return false;
        }

        if(senha.value.length == 0) {
            alert("Preencha a senha!");
            return false;
        }

        if(senha.value.length < 6) {
            alert("A senha deve ter pelo menos 6 caracteres!");
            return false;
        }

        if(reSenha.value.length == 0) {
            alert("confirme a senha!");
            return false;
        }

        if(reSenha.value.length < 6) {
            alert("A senha deve ter pelo menos 6 caracteres!");
            return false;
        }

        if(senha.value != reSenha.value) {
            alert("Suas senhas estão Diferentes!");
            return false;
        }

        if (!email.value.includes("@") || !email.value.includes(".")) {
            alert("O Campo de email deve ser preenchido corretamente");
            return false;
        }

        const response = grecaptcha.getResponse();

        if (response.length === 0) {
            alert("Por favor, confirme que você não é um robô.");
            return false;
        }
        
        return true;
    }
</script>
</html>