<?php

    require('protect.php');
    require('connector.php');
    require('cripto.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nome_lista = $_POST['nome'];
        $tipo = $_POST['tipo'];
        $visibilidade = $_POST['visibilidade'];

        if($visibilidade == 'privado'){
            $nome_lista = encrypt_aes_gcm($nome_lista, $_SESSION['senha']);
        }

        $stmt = $pdo->prepare('INSERT INTO listas (user_id, nome_lista, lista, tipo, visibilidade) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['id'], $nome_lista, "", $tipo, $visibilidade]);

        if(str_contains($nome_lista, "+")){
            $nome_lista = str_replace("+", "strcontainmais", $nome_lista); 
        }

        header("Location: painel.php?lista=" . $nome_lista);
    }

    require('frame_painel.php')

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crie sua lista!</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <style>
        body{
            display: flex;
            align-items: center;
        }
        a{
            text-decoration: none;
            color: white;
        }

        .out-buttons{
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            bottom: 0;
            position: absolute;
            width: 100%;
            flex-direction: row;
            gap: 20px;
        }

        .btn-sair{
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            width: 200px;
            height: 50px;
            background-color: #8B0000;
            
        }
        .btn-sair:hover{
            background-color: #7B0000;
        }

        .new-list{
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            width: 200px;
            text-align: center;
            background-color: #8A2BE2;
            margin: 0;
            height: 50px;
            transition: 0.2s;
            cursor: pointer;
        }

        .new-list:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
        }
        
        form{
            width: 70vw;
            display: flex;
            height: 60vh;
            gap: 15px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        input{
            padding: 10px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: medium;
            width: 58%;
            height: 100%;
            max-height: 33px;
            background-color: black;
            color: white;
            border-radius: 15px;
            border: none;
        }

        label{
            margin-top: 20px;
            color: white;
        }

        select{
            padding: 10px;
            height: 100%;
            max-height: 53px;
            width: 60%;
            background-color: black;
            color: white;
            border: none;
            border-radius: 15px;
        }

        option{
            padding: 10px;
            height: 100%;
            width: 70%;
        }
    </style>
</head>
<body>
    <form method="POST" action="">
        <label for="">Nome:</label>
        <input type="text" name="nome" placeholder="Insira o nome da lista">

        <label for="">Tipo</label>
        <select name="tipo" id="">
            <option value="lista">Listas</option>
            <option value="bloco">Bloco</option>
        </select>

        <label for="">Visibilidade:</label>
        <select name="visibilidade" id="">
            <option value="privado">Privada</option>
            <option value="publico">Pública</option>
        </select>

        <button>Criar</button>
    </form>
    <div class="out-buttons">
        <a href="selecionar_lista.php" class="new-list">Voltar</a>
        <a class="btn-sair" href="logout.php">Sair</a>
    </div>
</body>
</html>