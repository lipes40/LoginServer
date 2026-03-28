<?php
    require ('protect.php');
    require ('connector.php');
    require ('cripto.php');

    $name_list = $_GET["lista"];
    if (!$name_list) {
        header("Location: selecionar_lista.php");
    }
    $name_list = str_replace("%20", " ", $name_list);

    $sql = "SELECT * FROM listas WHERE user_id = ? AND nome_lista = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id'], $name_list]);

    $resultado = $stmt->fetch();

    $_SESSION['lista'] = $resultado[3];

    if ($resultado[5] == "privado") {
        $visibilidade = ["Privado", "Público"];
        $_SESSION['lista'] = decrypt_aes_gcm($_SESSION['lista'], $_SESSION['senha']);
    }
    else{
        $visibilidade = ["Público", "Privado"];
    }

    $mostrar = true;

    $error = '';

    $cont = 0;

    $texto = $_SESSION["lista"];

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $lista = $_POST["items"];

        $lista = array_map('trim', $lista);

        $texto = implode("#,@SEPARATOR_LINES@,#", $lista);

        if ($resultado[5] == "privado") {
            $cripto = encrypt_aes_gcm($texto, $_SESSION['senha']);
        }
        
        else{
            $cripto = $texto;
        }

        // $json = json_encode($texto, JSON_UNESCAPED_UNICODE);

        $stmt = $pdo->prepare("UPDATE listas SET lista = ? WHERE id = ?");
        $stmt->execute([$cripto, $resultado[0]]);

        header("Refresh: 0");
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

        .add{
            color: gray;
            background: none;
        }

        .add:hover{
            background: none;
            color: white;
        }

        form{
            display: flex;
            width: 100%;
            height: 100%;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            text-decoration: none;
        }

        button{
            justify-self: center;
        }

        a{
            justify-self: center;
        }

        img{
            height: 20px;
            width: 20px;
        }

        input {
            padding-left: 5px;
            background-color: #111111;
            color: white;
            border: none;
            font-size: 20px;
            margin-top: 5px;
            display: flex;
            width: 100%;
            height: 70%;
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
            width: 90%;
            color: white;
            margin-top: 5px;
            align-items: center;
            justify-content: center;
        }

        hr{
            border: 1px solid white;
            width: 90vw;
        }

        .numerador{
            margin-top: 5px;
            margin-right: 10px;
        }

        .btns-out{
            width: 80%;
            justify-content: center;
            margin-top: 10px;
            justify-content: center;
            align-items: center;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            display: grid;
        }

        .bloco{
            margin-right: 5px;
        }

        .deletar{
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: center;
            background: none;
            width: 50px;
            height: 50px;
            margin: 0;
        }

        .deletar:hover{
            background: none;
        }

        .copy{
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: center;
            background: none;
            width: 50px;
            height: 50px;
            margin: 0;
        }

        .copy:hover{
            background: none;
        }

        .divisoria{
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: center;
            background: none;
            width: 50px;
            height: 50px;
            margin: 0;
        }

        .divisoria:hover{
            background: none;
        }

        .linha{
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: center;
            background: none;
            width: 50px;
            height: 50px;
            margin: 0;
        }

        .linha:hover{
            background: none;
        }

        .icon-clipboard{
            width: 23px;
            height: 23px;
        }

        .icon-input{
            height: 12px;
            width: 40px;
        }

        .lixeira{
            width: 15px;
            height: 20px;
            margin-right: 5px;
        }

        .icon-divisoria{
            width: 30px;
            height: 3px;
        }

        .dropdown{
            color: white;
            position: relative;
            display: inline-block;
        }

        .dropdown:hover ul{
            display: flex;
        }

        ul{
            position: absolute;
            bottom: 100%;
            right: -100%;
            background-color: #8A2BE2;
            margin-right: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            flex-direction: column;
            list-style: none;
        }

        ul:hover{
            display: flex;
        }

        .list-info{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: row;
        }

        @media (max-width: 600px) {

            input{
                font-size: large;
                width: 65%;
            }

            .copy{
                width: 40px;
                height: 40px;
            }
        }

    </style>

</head>
<body class="principal">
    <?php require ("frame_painel.php") ?>
        <form method="post" action="">
            <div class="inputs-container">
                <div class="list-info">
                    <h2><?php echo $name_list ?></h2>
                    <div class="dropdown">
                        <img src="img/informacoes.png" alt="">
                        <ul>
                            <label for="">Visibilidade:</label>
                            <select name="" id="">
                                <option value="<?php echo $visibilidade[0] ?>"><?php echo $visibilidade[0]?></option>
                                <option value="<?php echo $visibilidade[0] ?>"><?php echo $visibilidade[1]?></option>
                            </select>
                        </ul>
                    </div>
                </div>
                <div id="inputs-container" class="inputs-container">
                    <?php 

                    $lista = explode("#,@SEPARATOR_LINES@,#", $texto);

                    // if (str_contains($texto, "#,@SEPARATOR_LINES@,#")) {
                    //     
                    // }
                    // else{
                    //     $lista = $texto;
                    // }

                    foreach($lista as $item): 
                    ?>
                    <div class="items">
                        <p class="numerador"><?php $cont ++;
                        echo $cont ?></p>

                        <input type="text" placeholder="Adicione algo" name="items[]" value="<?php {echo htmlspecialchars(trim($item));}?>">
                        <button type="button" class='copy'><img class="icon-clipboard" title="Copiar" src="img/clipboard.png"></button>

                        <div class="dropdown">
                            ...
                            <ul>
                                <li>
                                    <button type="button" class='deletar'><img class="icon-lixeira" src="img/lixeira-branca.png" title="Deletar"></button>
                                </li>
                                <li>
                                    <button type="submit" id="linha" title="Nova linha" type="button" class='linha'><img class="icon-input" src="img/input.png"></button>
                                </li>
                                <li>
                                    <button id="divisoria" type="button" title="Adicionar Divisória" class='divisoria'><img class="icon-divisoria" src="img/divisoria.png"></button>
                                </li>
                            </ul>
                        </div>

                        <!-- <button type="button" class='deletar'><img class="icon-lixeira" src="img/lixeira-branca.png"></button> -->

                    </div>
                    <?php endforeach ?>
                    <button type="button" class="add" id="adicionar">Adicionar Linha</button>
                    
                </div>
                <div class="btns-out">
                    <button type="submit" class="salvar">Salvar</button>
                    <a href="selecionar_lista.php">
                        <button type="button" class="bloco">Escolher outra lista</button>
                    </a>
                    <a href="painelbloco.php">
                        <button type="submit" id="deletar" class="bloco">Ir para Bloco</button>
                    </a>
                    <a class="base-line" href="logout.php"><button type="submit" class="sair">Sair</button></a>
                </div>
            </div>
            


        
        </form>
</body>
<script>
    const container = document.getElementById('inputs-container');
    const btnAdicionar = document.getElementById('adicionar')
    const btnDeletar = document.getElementById('deletar')

    btnAdicionar.addEventListener('click', (event) => {
        const input = document.createElement('input');
        const last = event.target.previousElementSibling;
        let numero = last.querySelector('p').innerText;
        numero = parseInt(numero) +1

        const html = `
            <div class="items">
                <p class="numerador"><?php $cont ++; echo $cont ?></p>
                <input type="text" placeholder="Adicione algo" name="items[]" value="">
                <button type="button" class='copy'><img class="icon-clipboard" title="Copiar" src="img/clipboard.png"></button>
                <div class="dropdown">
                    ...
                    <ul>
                        <li>
                            <button type="button" class='deletar'><img class="icon-lixeira" src="img/lixeira-branca.png" title="Deletar"></button>
                        </li>
                        <li>
                            <button type="submit" id="linha" title="Nova linha" type="button" class='linha'><img class="icon-input" src="img/input.png"></button>
                        </li>
                        <li>
                            <button id="divisoria" type="button" title="Adicionar Divisória" class='divisoria'><img class="icon-divisoria" src="img/divisoria.png"></button>
                        </li>
                    </ul>
                </div>

                <!-- <button type="button" class='deletar'><img class="icon-lixeira" src="img/lixeira-branca.png"></button> -->

            </div>
            `;

        last.insertAdjacentHTML('afterend', html)

        console.log(last)

        // input.type = 'text';
        // input.style.width = "90%"
        // input.name = 'items[]';
        // input.placeholder = 'Adicione algo'
        // container.appendChild(input);
    });

    container.addEventListener('click', (event) => {
        const botao = event.target.closest('.deletar');
        const botao_copy = event.target.closest('.copy')
        const botao_divisoria = event.target.closest('.divisoria')
        const botao_linha = event.target.closest('.linha')

        if (botao) {
            const deletar = event.target.closest('.items')
            deletar.remove()
        }

        if (botao_copy) {
            const div_items = event.target.closest('.items')

            let input_btn = div_items.querySelector('input')
            navigator.clipboard.writeText(input_btn.value)
        }

        if (botao_linha) {
            const div_items = event.target.closest('.items')
            let numero = div_items.querySelector('p').innerText;
            numero = parseInt(numero) +1

            const html = `
            <div class="items">
                <p class="numerador">${numero}</p>
                <input type="text", name="items[]", placeholder="Adicione algo" style="width: 90%;">
                <button type="button" class="copy">
                    <img class="icon-clipboard" title="Copiar" src="img/clipboard.png">
                </button>...
            </div>
            `;

            div_items.insertAdjacentHTML('afterend', html)
        }

        if (botao_divisoria) {
            const div_items = event.target.closest('.items')
            if (div_items.nextElementSibling.tagName != "HR"){
                div_items.insertAdjacentHTML('afterend', '<hr>')
            }
            else{
                div_items.nextElementSibling.remove()
            }
        }


});

</script>
</html>