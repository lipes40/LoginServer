<?php

require('connector.php');

$name_list = $_GET['lista'];
if (!$name_list){
    header("Location: index.php");
    exit();
}

$name_list = urldecode($name_list);
$name_list = str_replace("strcontainmais", "+", $name_list);

$sql = 'SELECT * FROM listas WHERE nome_lista = ? AND visibilidade = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$name_list, "publico"]);

$resultado = $stmt->fetch();

if ($resultado){
    if ($resultado[6] == "editavel"){
        session_start();
        $_SESSION['publico'] = true;
        header("Location: painel.php?lista=" . urlencode($name_list));
        exit();
    }
    
    $title = $resultado[2];
    $estado = $resultado[4];
    $lista = $resultado[3];
}
else{
    $estado = "nada";
    $title = $name_list . " Não encontrado";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?></title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <style>
        body{
            justify-content: center;
            background-color: #111111;
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        textarea{
            padding: 10px;
            background-color: #111111;
            color: white;
            border-radius: 10px;
            border: 2px solid white;
            font-size: large;
            margin-top: 5px;
            display: flex;
            width: 90%;
            height: 450px;
            padding-left: 5px;
            margin-bottom: 20px;
        }
        
        h1{
            margin-left: 20px;
            align-self: baseline;
            color: white;
        }

        h2{
            color: white;
        }

        p{
            color: white;
            margin-right: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .inputs-container{
            /* margin-top: 150px; */
        }

        img{
            max-width: 300px;
            max-height: 100%;
        }

        .items{
            width: 80vw;
            display: flex;
            align-self: center;
            align-items: center;
            justify-content: center;
        }

        input {
                padding-left: 5px;
                font-family: Arial, Helvetica, sans-serif;
                background-color: #111111;
                color: white;
                border: none;
                font-size: 20px;
                display: flex;
                width: 100%;
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

        .link-imagem{
            width: 300px;
            height: 100%;
        }

        .copy{
                display: flex;
                align-items: center;
                justify-content: center;
                align-self: center;
                background: none;
                width: 50px;
                border: none;
                height: 50px;
                margin: 0;
                transition: 0.2s;
                cursor: pointer;
            }

        .copy:hover{
            transform: scale(1.05);
            background: none;
        }

        .icon-clipboard{
                width: 23px;
                height: 23px;
        }

        .buttons{
            margin-top: 100px;
            display: grid;
            width: 100vw;
            grid-template-columns: repeat(auto-fit, minmax(210px, 2fr));
            align-items: center;
            justify-content: center;
            gap: 20px;
            bottom: 0;
        }
        
        a{
            align-items: center;
            display: flex;
            justify-content: center;
            text-decoration: none;
        }
        
    </style>
</head>
<body>
    <h1>Seja bem vindo ao SecurePad</h1>
    <h2><?php echo $title ?></h2>
    <div id="inputs-container" class="inputs-container">

        <?php if ($estado == "linhas"): ?>
            <?php
             $cont = 0;
             $lista = explode("#,@SEPARATOR_LINES@,#", $lista);

            foreach($lista as $item): ?>
                <div class="items">
                    <p><?php $cont++; echo $cont?></p>
                    <input placeholder="Adicione algo" type="text" value="<?php echo $item ?>">
                    <button type="button" class='copy'><img class="icon-clipboard" title="Copiar" src="img/clipboard.png"></button>
                </div>
            <?php endforeach ?>

        <?php elseif ($estado == "bloco"): ?>
            <?php if(str_contains($lista, "#,@LINK_REDIRECT@,#")) {
                $lista = str_replace("#,@LINK_REDIRECT@,#", "", $lista);
                header("Location: " . $lista);
                exit();
            } ?>
            <div class="items">
                <textarea name="" id=""><?php echo $lista ?></textarea>
            </div>
            
        <?php else: ?>
        <?php endif ?>

    </div>

    <div class="buttons">
        <a href="./">
            <button>Entrar</button>
        </a>

        <a href="./cadastrar.php">
            <button>Cadastre-se</button>
        </a>
    </div>

</body>
<script>
    const container = document.getElementById('inputs-container')

    container.addEventListener('click', (event) => {
        const div_items = event.target.closest('.items')
        let input = div_items.querySelector('input')
        navigator.clipboard.writeText(input.value)
    })

    container.querySelectorAll(".items").forEach((item) => {
            const input = item.children[1]
            if (input.value.includes("#,@LINE_DIVIDER@,#")){
                input.value = input.value.replace("#,@LINE_DIVIDER@,#", "")
                item.insertAdjacentHTML('afterend', '<hr>')
            }

            if(input.value.includes("#,@SECRET_PASSWORD@,#")){
                input.parentElement.remove()
            }

            if(input.value.includes("#,@LINK_IMG@,#")){
                input.value.replace("#,@LINK_IMG@,#", "")
                const img = document.createElement('img')
                img.src = input.value.replace("#,@LINK_IMG@,#", "")
                img.className = "link-imagem"
                input.replaceWith(img)
            }
    });
</script>
</html>
