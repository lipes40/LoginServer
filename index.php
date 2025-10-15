<?php

    require('connector.php');

    session_start();

    if(isset($_SESSION['id'])) {
        header("Location: painel.php");
        exit;
    }

    $error = '';

    if(isset($_POST['email']) && isset($_POST['senha'])) {

        if(strlen($_POST['email']) == 0) {
            $error = "Preencha seu email!";
        }
        elseif (strlen($_POST['senha']) == 0) {
            $error = "Preencha sua senha!";
        }
        else{

            $email = $_POST['email'];
            $senha = $_POST['senha'];

            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);

            $usuario = $stmt->fetch();

            
            
            if($usuario) {

                $verify = password_verify($senha, $usuario['senha']);

                if($verify) {

                    if(!isset($_SESSION)) {
                        session_start();
                    }

                    $_SESSION['id'] = $usuario['id'];
                    $_SESSION['nome'] = $usuario['nome'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['senha'] = $senha;

                    header("Location: painel.php");

                    exit;
                }
                else{
                    $error = "Falha ao logar! email ou senha incorretos!";
                }

            }   else{
                    $error = "Falha ao logar! email ou senha incorretos!";
                }
            
        
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <style>
        *{
            padding: 0;
            margin: 0;
        }


        body{
            display: flex;
            background-color: #111111;
            color: white;
            height: 90vh;
            width: 100vw;
            justify-content: center;
        }
        
        header{
            display: flex;
            height: 100px;
            width: 100vw;
            justify-content: center;
            align-items: center;
        }

        h1{
            color: white;
            font-family: Arial, Helvetica, sans-serif;
        }

        .creditos{
            font-family: Arial, Helvetica, sans-serif;
            position: absolute;
            bottom: 10px;
        }

        .box-center{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
            width: 35%;
        }

        input{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            background-color: black;
            color: white;
            border: none;
            height: 8%;
            width: 100%;
            border-radius: 10px;
            padding-left: 5px;
        }

        .senha-password{
            display: flex;
            width: 100%;
            height: 100%;
        }

        .senhas{
            flex-direction: row;
            width: 100%;
            height: 100%;
        }

        .ver-senha{
            width: 24px;
            display: flex;
            align-self: center;
            right: 2px;
            height: 24px;
            cursor: pointer;
            border: none;
            position: absolute;
            background: none;
        }

        .box-senha{
            display: flex;
            align-items: center;
            height: 8%;
            position: relative;
            width: 100%;
        }

        .entrar{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #8A2BE2;
            color: white;
            border: none;
            height: 5%;
            width: 100%;
            border-radius: 5px;
            transition: 0.2s;
            cursor: pointer;
        }

        .entrar:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        p{
            text-decoration: none;
            padding: 0;
            gap: 0;
            margin: 0;
        }

        .conta{
            text-decoration: none;
            padding: 0;
            gap: 0;
            height: 5px;
            color: #8A2BE2;
        }

        @media (max-width: 600px) {
            .box-center {
                width: 70%;
            }
        }
    </style>

</head>
<body>
    <form action="" class="box-center" method="POST">

        <h1>Acesse sua conta!</h1>

        <input type="email" name="email" placeholder="Email" value="<?php echo $_POST['email'] ?? ''; ?>">

        <div class="box-senha">
        <input type="password" id="senha" name="senha" class="senha-password" placeholder="Senha">


        <button class="ver-senha" onclick="visivel()" type="button">
            <img id="iconeSenha" class="ver-senha" src="img/olho-aberto-w.png" alt="Mostrar senha">
        </button>
        </div>

        <button type="submit" class="entrar">Entrar</button>

        <span style="
        font-family: Arial, Helvetica, sans-serif; 
        display: flex; 
        color: red;"><?php if(isset($error) && $error != "") echo $error; ?></span>

        <p>ou</p>

        <a class="conta" type="button" href="cadastrar.php"><p>Crie sua conta!</p></a>

    </form>

    <span class="creditos">Criado por: Fellipe Teixeira</span>
    
</body>

<script>
    const btn = document.getElementById("senha");
    const icone = document.getElementById("iconeSenha")

    function visivel() {
        if(btn.type === "text"){
            btn.type = "password";
            icone.src = "img/olho-aberto-w.png";
            icone.alt = "Mostrar senha"
            return;
        }

        btn.type = "text";
        icone.src = "img/olho-fechado-w.png"
        icone.alt = "Ocultar senha"
    }
</script>

</html>