<?php 
    require ("connector.php");
    require ("protect.php");
    require ("cripto.php");

    $sql = "SELECT nome_lista, visibilidade FROM listas WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id']]);
    
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecione a lista desejada!</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

    <style>
        body{
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        a{
            color: white;
            text-decoration: none;
        }

        h1{
            margin-bottom: 50px;
        }

        .items{
            margin-top: 50px;
            display: flex;
            gap: 20px;
            align-items: center;
            justify-content: center;
            width: 90vw;
            flex-direction: column;
        }

        .listas{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 20px;
            justify-content: center;
            width: 80%;
        }

        .btn-lista{
            font-family: Arial, Helvetica, sans-serif;
            background-color: #8A2BE2;
            justify-self: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: none;
            height: 50px;
            width: 200px;
            border-radius: 15px;
            margin-bottom: 10px;
            transition: 0.2s;
            cursor: pointer;
        }

        .btn-lista:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        .base-line{
            display: flex;
            justify-content: center;
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
    </style>

</head>
<body>
    <?php require("frame_painel.php"); ?>
    <div class="items">
        <h1>Escolha sua lista</h1>
        <div class="listas">
            <?php foreach ($resultado as $i): ?>
                <a class="btn-lista" href="painel.php?lista=<?php
                    $lista = $i['nome_lista'];
                    if (str_contains($i['nome_lista'], "+")){
                        $lista = str_replace("+", "strcontainmais", $lista); 
                    }
                     echo $lista;
                     ?>">
                <?php
                        if ($i['visibilidade'] == 'privado'){
                            echo decrypt_aes_gcm($i['nome_lista'], $_SESSION['senha']);
                            continue;
                        }
                        echo $i['nome_lista'];
                    ?>
                </a>
            <?php endforeach ?>
        </div>
    </div>

    <div class="out-buttons">
        <a href="criar_lista.php" class="new-list">Nova lista</a>
        <a class="btn-sair" href="logout.php">Sair</a>
    </div>

</body>
</html>