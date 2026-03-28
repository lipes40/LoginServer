<?php 
    require ("connector.php");
    require ("protect.php");

    $sql = "SELECT nome_lista FROM listas WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id']]);
    
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $lista = [];

    foreach ($resultado as $i) {
        $lista[] = $i['nome_lista'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecione a lista</title>
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
    </style>

</head>
<body>
    <?php require("frame_painel.php"); ?>
    <div class="items">
        <h1>Escolha sua lista</h1>
        <div class="listas">
            <?php foreach ($lista as $i): ?>
                <a class="btn-lista" href="painel.php?lista=<?php echo $i ?>"><?php echo $i ?></a>
            <?php endforeach ?>
        </div>
    </div>

</body>
</html>