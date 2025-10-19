<?php 
    require("connector.php");

    if(!isset($_SESSION)) {
        session_start();
    }

    $error = '';

    if(isset($_POST['senha']) && isset($_POST['newSenha']) && isset($_POST['reSenha'])) {
        $senha = $_POST['senha'];
        $newSenha = $_POST['newSenha'];
        $criptoSenha = password_hash($newSenha, PASSWORD_DEFAULT);

        if(strlen($_POST['senha']) == 0) {
            $error = "Preencha sua senha!";
        }

        elseif(strlen($_POST['newSenha']) == 0) {
            $error = "Preencha sua nova senha!";
        }

        elseif(strlen($_POST['newSenha']) < 6) {
            $error = "A senha deve ter pelo menos 6 caracteres!";
        }

        elseif(strlen($_POST['reSenha']) == 0) {
            $error = "Confirme sua senha!";
        }

        elseif(strlen($_POST['reSenha']) < 6) {
            $error = "A senha deve ter pelo menos 6 caracteres!";
        }

        elseif($_POST['newSenha'] != $_POST['reSenha']) {
            $error = "Suas novas senhas estão diferentes!";
        }

        elseif(password_verify($senha, $_SESSION['cripto_senha'])) {
            $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->execute([$criptoSenha, $_SESSION['id']]);

            header("Location: logout.php");
            exit;
        }
        else{
            $error = "Senha incorreta";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mudar senha</title>
    <style>
        body{
            background-color: #111111;
        }

        a{
            margin: 0;
            padding: 0;
        }

        header{
            display: flex;
            justify-content: center;
            max-height: 60px;
            position: relative;
            align-items: center;
        }

        h1{
            color: white;
            text-align: right;
            font-family: Arial, Helvetica, sans-serif;
        }

        h3{
            color: white;
            font-family: Arial, Helvetica, sans-serif;
        }

        .error{
            color: red;
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
            cursor: pointer;
            transition: 0.2s;
        }

        .button:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        .button-log{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            position: absolute; 
            left: 20px;
            align-items: center;
            align-self: center;
            justify-content: center;
            background-color: #8A2BE2;
            color: white;
            border: none;
            width: 15%;
            min-width: 40px;
            height: 30px;
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

        @media (max-width: 600px) {
            input{
                width: 70%;
            }

            .button{
                width: 70%;
            }

            .button-log{
                align-items: center;
                justify-content: center;
                display: inline-flex;
                left: 0;
                margin-top: 5px;
                position: relative;
                max-height: 3%;
                width: 20%;
                padding-bottom: 5px;
            }
        }

        @media (max-width: 350px) {
            h1{
                font-size: 150%;
            }
        }

    </style>
</head>
<body>
    <header>
        <a href="painel.php">
        <button id="voltar" class="button-log">Voltar ao painel</button>
        </a>
        <h1>Mude sua senha!</h1>
    </header>

        <form action="" method="POST">
            <h3>Insira a senha atual</h3>
            <input type="password" name="senha" id="senha" placeholder="Senha" value="<?php echo $_POST['senha'] ?? ''; ?>">

            <h3>Insira a nova senha</h3>
            <input type="password" minlength="6" name="newSenha" id="newSenha" placeholder="Nova Senha" value="<?php echo $_POST['newSenha'] ?? ''; ?>">

            <h3>Confirme a senha</h3>
            <input type="password" minlength="6" name="reSenha" id="reSenha" placeholder="Confirme a Senha" value="<?php echo $_POST['reSenha'] ?? ''; ?>">

            <h3 class="error"><?php echo $error; ?></h3>

            <input class="button" type="submit" value="Enviar">
            
        </form>

</body>
<script>
    const voltar = document.getElementById("voltar")

    if (window.innerWidth <= 600) {
        voltar.innerText = "⬅";
        voltar.style.fontSize = '250%'
    }

    else{
        voltar.innerText = "Voltar ao painel";
    }
    
</script>
</html>