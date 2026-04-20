<?php

require ("protect.php");
require ("connector.php");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <style>
        *{
            padding: 0;
            margin: 0;
            font-family: "arial", sans-serif;
        }

        .frame{
            display: flex;
            background-color: #111111;
            flex-direction: column;
        }

        h1{
            color: white;
            margin-left: 20px;
        }

        button{
            font-family: Arial, Helvetica, sans-serif;
            background-color: #8A2BE2;
            color: white;
            border: none;
            height: 50px;
            width: 200px;
            border-radius: 15px;
            font-family: "arial", sans-serif;
            margin-bottom: 10px;
            transition: 0.2s;
            cursor: pointer;
        }

        button:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        header{
            display: flex;
            width: 100vw;
            height: 10%;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .mostrar{   
            height: 70%;
            margin-right: 5px;
            cursor: pointer;
        }

        .conjunto{
            margin-top: 10px;
            display: flex;
            margin-bottom: 20px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        h2{
            color: white;
        }

        h3{
            color: white;
        }

        .info{
            display: none;
            margin-top: 50px;
            transition: 2s;
            flex-direction: column;
            width: 350px;
            height: auto;
            border: 1px solid rgba(230, 230, 230, 0.2);
            padding: 30px;
            border-radius: 15px;
            align-items: center;
        }

        .user{
            margin-top: 30px;
        }

        .btn-morte{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            padding: 6px;
            border: 1px solid red;
            color: white;
            width: 100%;
            height: 30px;
            align-items: center;
            gap: 10px;
            justify-content: center;
            border-radius: 15px;
            transition: 0.2s;
            text-decoration: none;
            text-align: center;
            margin: 5px;
        }

        .btn-morte:hover{
            transform: scale(1.05);
            background: #8B0000;
            background: linear-gradient(180deg, rgba(139, 0, 0, 0.25) 0%, rgba(139, 0, 0, 0.25) 100%);  
        }

        .btn-senha{
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            border: 1px solid #1E90FF;
            color: white;
            border-radius: 15px;
            padding: 6px;
            width: 100%;
            margin: 5px;
            height: 30px;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            text-decoration: none;
            cursor: pointer;
            backdrop-filter: 80px;
        }

        .btn-senha:hover{
            transform: scale(1.05);
            background: #1E90FF;
            background: linear-gradient(90deg, rgba(30, 144, 255, 0.25) 0%, rgba(30, 144, 255, 0.25) 100%);
        }

        .buttons{
            margin-top: 10px;
            width: 100%;
            display: flex;
            flex-direction: row;
        }

        .icon-lixeira{
            width: 17px;
            height: 20px;
        }


        @media (max-width: 600px) {
            .mostrar{
                margin: 5px;
            }

            h1{
                font-size: medium;
            }

            .info{
                width: 70%;
            }
        }
    </style>
    </head>
<body class="frame">
    <header>
        <h1>Seja bem vindo ao SecurePad</h1> 
        <button class="mostrar" onclick="mostra()">Minhas informações</button>
    </header>

    <div class="conjunto">
        <h2>Olá <?php echo $_SESSION['nome']; ?> Tudo Bem?</h2>

        <h3 class="user">Você é o usuário número: <?php echo $_SESSION['id'] ?> parabéns</h3>

        <div id="info" class="info">
            <h3><?php if($mostrar) echo "Nome: " . $_SESSION['nome']; ?></h3>
            <h3><?php if($mostrar) echo "Email: " . $_SESSION['email']; ?></h3>

            <div class="buttons">

                <a class="btn-morte" href="deletar_conta.php">
                    <img class="icon-lixeira" src="img/lixeira-branca.png">Deletar conta
                </a>

                <a class="btn-senha" href="muda_senha.php">
                    Mudar senha
                </a>
            </div>
            
        </div>
        
    </div>

</body>
<script>
    let mostrar = false;
    const obj = document.getElementById('info');

    function mostra() {
        
        if(!mostrar) {
            obj.style.display = "flex"
            mostrar = true;
        }
        else {
            obj.style.display = "none"
            mostrar = false;
        }

    }
</script>
</html>