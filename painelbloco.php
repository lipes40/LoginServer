<?php
    require ('protect.php');
    require ('connector.php');
    require ('cripto.php');

    $sql = "SELECT bloco FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['email']]);

    $resultado = $stmt->fetch();

    $_SESSION['bloco'] = $resultado[0];

    if($_SESSION['bloco'] == null){
        $_SESSION['bloco'] = "";
    }
    else{
        $_SESSION['bloco'] = decrypt_aes_gcm($_SESSION['bloco'], $_SESSION['senha']);
    }

    $mostrar = true;

    $error = '';

    $cont = 0;

    $lista = $_SESSION['bloco'];

    // echo $_SESSION["bloco"];
    // $lista = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $lista = $_POST["items"];

        $lista = encrypt_aes_gcm($lista, $_SESSION['senha']);

        // echo gettype($lista) . $lista;

        $stmt = $pdo->prepare("UPDATE usuarios SET bloco = ? WHERE id = ?");

        $stmt->execute([$lista, $_SESSION["id"]]);

        $lista = decrypt_aes_gcm($lista, $_SESSION['senha']);

        header("Location: painelbloco.php");
    }
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    
    <style>
        p {
            color: white
        }

        .sair{
            background-color: #8B0000;
        }
        .sair:hover{
            background-color: #8B0000;
        }

        form{
            display: flex;
            width: 100%;
            height: 100%;
            margin-top: 20px;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            text-decoration: none;
        }

        img{
            height: 20px;
            width: 20px;
        }

        textarea {
            background-color: #111111;
            color: white;
            border: none;
            font-size: large;
            margin-top: 5px;
            display: flex;
            width: 90%;
            height: 500px;
            padding-left: 5px;
        }

        .inputs-container{
            display: flex;
            margin: 0;
            padding: 0;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .items{
            display: flex;
            width: 100%;
            margin-top: 5px;
            align-items: center;
            justify-content: center;
        }

        .numerador{
            margin-top: 5px;
            margin-right: 10px;
        }

        .btns-out{
            justify-content: center;
            margin-top: 10px;
            display: flex;
        }

        .add{
            margin-left: 5px;
            margin-right: 5px;
        }

        @media (max-width: 600px) {
            .btns-out{
                flex-direction: column;
                align-items: center;
            }

            input{
                font-size: large;
            }
        }
    </style>

</head>
<body class="principal">
    <?php require ("frame_painel.php") ?>

    <form method="post" action="">
        <div class="inputs-container">
            <div id="inputs-container" class="inputs-container">
                <div class="items">
                <textarea type="text" placeholder="Adicione algo" name="items"><?php {echo htmlspecialchars(trim($lista));}?></textarea>
                </div>
            </div>
            <div class="btns-out">
                <button class="salvar" type="submit">Salvar</button>
                <a href="painel.php">
                <button type="button" class="add" id="adicionar">Voltar para Linhas</button>
                </a>
                <a class="base-line" href="logout.php"><button type="button" class="sair">Sair</button></a>
            </div>
        </div>
        


    
    </form>
        
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