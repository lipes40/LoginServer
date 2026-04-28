<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <style>

        *{
            font-family: Arial, Helvetica, sans-serif;
        }

        body{
            background-color: #111111;
            width: 100vw;
            margin-left: 0;
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
            width: 100vw;
        }

        input{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            background-color: black;
            color: white;
            border: none;
            min-height: 50px;
            font-size: large;
            max-width: 500px;
            width: 90%;
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
            height: 50px;
            width: 70%;
            max-width: 400px;
            margin-top: 20px;
            border-radius: 20px;
            transition: 0.2s;
            cursor: pointer;
            font-size: large;
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

        .g-recaptcha{
            margin-top: 18px;
        }

        @media (max-width: 400px) {
            header{
                justify-content: space-around;
            }

            .button-log{
                max-height: 40px;
                width: 15%;
                left: 0;
            }
        }
    </style>
</head>
<body>

    <header>
        <a href="index.php">
        <button id="voltar" class="button-log">Fazer login</button>
        </a>
        <h1>Crie sua conta!</h1>
    </header>
    
    <form id="formCadastro" action="cria_usuario.php" method="POST" onsubmit="return enviar()">
        <h3>Insira o nome</h3>
        <input type="text" maxlength="40" name="c-nome" id="name" placeholder="Nome" value="<?php echo $_POST['c-nome'] ?? ''; ?>">

        <h3>Insira o nome de usuário</h3>
        <input type="text" maxlength="255" name="c-email" id="email" placeholder="Nome de usuário" value="<?php echo $_POST['c-email'] ?? ''; ?>">

        <h3>Insira a senha</h3>
        <input type="password" name="c-senha" id="senha" placeholder="Senha" value="<?php echo $_POST['c-senha'] ?? ''; ?>">

        <h3>Confirme a senha</h3>
        <input type="password" name="c-reSenha" id="reSenha" placeholder="Confirme a Senha" value="<?php echo $_POST['c-reSenha'] ?? ''; ?>">

        <div class="g-recaptcha" data-sitekey="6LcovfMrAAAAAP-Y8MWJjYB7IPYm2xOaEJj24F_l"></div>
        
        <input class="button" type="submit" value="Enviar">

        <p class="p-conta">Já tem uma conta?</p>

        <a href="index.php"><p class="p-login">Fazer login</p></a>
        
    </form>



</body>
<script>
    const voltar = document.getElementById("voltar")

    if (window.innerWidth <= 600) {
        voltar.innerText = "⬅";
        voltar.style.fontSize = '250%'
    }

    else{
        voltar.innerText = "Fazer Login";
    }

    let name = document.getElementById("name")
    let email = document.getElementById("email")
    let senha = document.getElementById("senha")
    let reSenha = document.getElementById("reSenha")

    function enviar() {
        if(name.value.length == 0) {
            alert("Preencha seu Nome!");
            return false;
        }
        if(email.value.length == 0) {
            alert("Preencha seu Nome de usuário!");
            return false;
        }

        if(senha.value.length == 0) {
            alert("Preencha a Senha!");
            return false;
        }

        if(reSenha.value.length == 0) {
            alert("confirme a Senha!");
            return false;
        }

        if(senha.value != reSenha.value) {
            alert("Suas senhas estão Diferentes!");
            return false;
        }

        const response = grecaptcha.getResponse();

        if (response.length === 0) {
            alert("Por favor, confirme que você NÃO é um robô.");
            return false;
        }
        
        return true;
    }
</script>
</html>