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
        }

        .items{
            margin-top: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 90vw;
            flex-direction: column;
        }
    </style>

</head>
<body>
    <?php require("frame_painel.php"); ?>
    <div class="items">
        <?php foreach ($lista as $i): ?>

            <a href="painel.php?lista=<?php echo $i ?>"><?php echo $i ?></a>

        <?php endforeach ?>
    </div>

</body>
</html>