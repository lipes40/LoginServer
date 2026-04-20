<?php
    require ('protect.php');
    require ('connector.php');
    require ('cripto.php');

    $name_list = $_GET["lista"];
    if (!$name_list) {
        header("Location: selecionar_lista.php");
        exit();
    }
    $name_list = urldecode($name_list);
    $name_list = str_replace("strcontainmais", "+", $name_list);

    $sql = "SELECT * FROM listas WHERE user_id = ? AND nome_lista = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id'], $name_list]);

    $_SESSION['type_lista'] = $stmt->fetch();

    if (!$_SESSION["type_lista"]){
        header("Location: selecionar_lista.php");
        exit();
    }

    if ($_SESSION["type_lista"][4] == 'bloco') {
        header("Location: painelbloco.php?lista=" . $_GET['lista']);
        exit();
    }

    $_SESSION['lista'] = $_SESSION["type_lista"][3];

    if ($_SESSION["type_lista"][5] == "privado") {
        $visibilidade = [["privado", "publico"], ["Privado", "Público"]];
        $_SESSION['type_lista'][2] = decrypt_aes_gcm($_SESSION['type_lista'][2], $_SESSION['senha']);
        $_SESSION['lista'] = decrypt_aes_gcm($_SESSION['lista'], $_SESSION['senha']);
    }
    else{
        $visibilidade = [["publico", "privado"], ["Público", "Privado"]];
    }

    if ($_SESSION['type_lista'][4] == "lista"){
        $tipo = [["bloco", "linhas"], ["Bloco", "Linhas"]];
    }
    else{
        $tipo = [["linhas", "bloco"], ["Linhas", "Bloco"]];
    }

    $mostrar = true;

    $cont = 0;

    $texto = $_SESSION["lista"]; 

    
        
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_POST['items'])){
            $lista = $_POST["items"];

            $novo_nome = trim($_POST['nome']);

            $estado = $_POST['visibilidade'];
            $novo_tipo = $_POST['tipo'];

            // Formata a lista pro jeito do banco de dados

            if ($novo_tipo == 'linhas') {
                $lista = array_map('trim', $lista);

                $lista = implode("#,@SEPARATOR_LINES@,#", $lista);
            }
            else{
                $lista = implode("\n", $lista);
            }

            // Define o tipo de visibilidade das informações

            if ($estado == "privado") {
                $lista = encrypt_aes_gcm($lista, $_SESSION['senha']);
                $novo_nome = encrypt_aes_gcm($novo_nome, $_SESSION['senha']);
            }

            // Verifica se o nome já existe no banco de dados

            $stmt = $pdo->prepare('SELECT id FROM listas WHERE nome_lista = ?');
            $stmt->execute([$novo_nome]);
            $nome_existente = $stmt->fetch();


            if(!$nome_existente || $nome_existente[0] == $_SESSION['type_lista'][0]){
                $error = "";
                $sql = 'UPDATE listas SET nome_lista = ?, lista = ?, tipo = ?, visibilidade = ? WHERE id = ?';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$novo_nome, $lista, $novo_tipo, $estado, $_SESSION["type_lista"][0]]);


                $nome_lista = $novo_nome;

                if(str_contains($nome_lista, "+")){
                    $nome_lista = str_replace("+", "strcontainmais", $nome_lista); 
                }

                header("Location: painel.php?lista=" . $nome_lista);
                exit();
            }
            else{
                $error = "O nome: " . $_POST['nome'] . ", já existe!";
            }
        }

        if(isset($_POST['deletar_lista'])){
            if($_POST['deletar_lista'] == $_SESSION['type_lista'][2]){
                $stmt = $pdo->prepare('DELETE FROM listas WHERE id = ?');
                $stmt->execute([$_SESSION['type_lista'][0]]);

                header('Location: selecionar_lista.php');
                exit();
            }
        }
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
                background-color: #7B0000;
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
                margin-top: 60px;
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

            .key{
                display: flex;
                align-items: center;
                justify-content: center;
                align-self: center;
                background: none;
                width: 50px;
                height: 50px;
                margin: 0;
            }

            .key:hover{
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
                display: flex;
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
                gap: 5px;
                align-items: center;
                justify-content: center;
                flex-direction: row;
            }   

            .img-info{
                align-self: center;
                justify-self: center;
            }

            .btn-mudar-nome{
                height: 20%;
                width: 30%;
                background: none;
            }

            .btn-mudar-nome:hover{
                background: none;
            }

            .delete{
                background-color: #8B0000;
                width: 100%;
                height: 100%;
                border-radius: 15px;
                margin: 10px;
            }

            .delete:hover{
                background-color: #7B0000;
            }

            .certeza-deletar{
                display: none;
                align-items: center;
                width: 100vw;
                height: 100vh;
                justify-content: center;
                background-color: #111111;
                color: white;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 10;
                gap: 50px;
                flex-direction: column;
            }

            .input-delete{
                padding: 10px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: medium;
                width: 40%;
                height: 30%;
                max-height: 33px;
                background-color: black;
                color: white;
                border-radius: 15px;
                border: none;
            }

            .input-mudar-nome{
                background-color: white;
                width: 90%;
                height: 20px;
                color: black;
            }

            .form-delete{
                gap: 20px;
            }

            .error{
                color: red;
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
    <?php require ("frame_painel.php") ?>
    <body class="principal">
        <div id="certeza_deletar" class="certeza-deletar">
            <form method="POST" class="form-delete" action="">
                <h1>Tem certeza que deseja deletar a lista: <?php echo $_SESSION['type_lista'][2] ?>?</h1>
                <h3>Digite o nome da lista para deletar</h3>
                <input id="input_delete" placeholder="Digite o nome da lista" class="input-delete" type="text" name="deletar_lista">
                <button id="button_deletar" type="submit">Voltar</button>
            </form>
        </div>

        <form id="envia" method="POST" action="">
            <div class="inputs-container">
                <div class="list-info">
                    <h2 class="name_lista"><?php echo  $_SESSION['type_lista'][2] ?></h2>
                    <div class="dropdown">
                        <img class="img-info" src="img/informacoes.png" alt="">
                        <ul>
                            <li>
                                <label for="">Nome Da Lista:</label>
                                <input id="input_mudar_nome" class="input-mudar-nome" name="nome" type="text" value="<?php echo $_SESSION['type_lista'][2] ?>">
                            </li>
                            <li>
                                <label for="">Visibilidade:</label>
                                <select name="visibilidade" id="visibilidade" onchange="salvar()">
                                    <option value="<?php echo $visibilidade[0][0] ?>"><?php echo $visibilidade[1][0] ?></option>
                                    <option value="<?php echo $visibilidade[0][1] ?>"><?php echo $visibilidade[1][1] ?></option>
                                </select>
                            </li>
                            <li>
                                <label for="">Tipo da lista:</label>
                                <select name="tipo" id="" onchange="salvar()">
                                    <option value="<?php echo $tipo[0][0] ?>"><?php echo $tipo[1][0] ?></option>
                                    <option value="<?php echo $tipo[0][1] ?>"><?php echo $tipo[1][1] ?></option>
                                </select>
                            </li>
                            <button id="deletar_lista" class="delete" type="button">Deletar lista</button>
                        </ul>
                    </div>
                </div>
                <p class="error"><?php echo $error ?? ""; ?></p>
                <div id="inputs-container" class="inputs-container">
                    <?php 
                    $lista = explode("#,@SEPARATOR_LINES@,#", $texto);
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
                                <button type="button" class='deletar' title="Deletar"><img class="icon-lixeira" src="img/lixeira-branca.png"></button>
                            </li>
                            <li>
                                <button type="button" class="key" title="Esconder conteúdo"><img src="img/key.png"></button>
                            </li>
                            <li>
                                <button type="button" id="linha" title="Nova linha" type="button" class='linha'><img class="icon-input" src="img/input.png"></button>
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
                    <button type="button" data-submit="true" class="salvar">Salvar</button>
                    <a href="selecionar_lista.php">
                        <button type="button" class="bloco">Voltar para listas</button>
                    </a>
                    <a class="base-line" href="logout.php"><button type="button" class="sair">Sair</button></a>
                </div>
            </div>
        </form>
    </body>
    <script>

        // Modificações na lista


        const container = document.getElementById('inputs-container');
        const btnAdicionar = document.getElementById('adicionar')
        const btnDeletar = document.getElementById('deletar')

        container.querySelectorAll(".items").forEach((item) => {
            const input = item.children[1]
            if (input.value.includes("#,@LINE_DIVIDER@,#")){
                input.value = input.value.replace("#,@LINE_DIVIDER@,#", "")
                item.insertAdjacentHTML('afterend', '<hr>')
            }

            if(input.value.includes("#,@SECRET_PASSWORD@,#")){
                input.value = input.value.replace("#,@SECRET_PASSWORD@,#", "")
                input.type = "password"
            }
        });

        btnAdicionar.addEventListener('click', (event) => {
            const input = document.createElement('input');
            const last = event.target.previousElementSibling;
            console.log(last)
            let numero = 1

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
                                <button type="button" data-submit="true" id="linha" title="Nova linha" type="button" class='linha'><img class="icon-input" src="img/input.png"></button>
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

            salvar()
        });

        container.addEventListener('click', (event) => {
            const botao_deletar = event.target.closest('.deletar');
            const botao_copy = event.target.closest('.copy')
            const botao_divisoria = event.target.closest('.divisoria')
            const botao_linha = event.target.closest('.linha')
            const botao_key = event.target.closest(".key")

            if (botao_deletar) {
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

                salvar()
            }

            if (botao_divisoria) {
                const div_items = event.target.closest('.items')
                if (div_items.nextElementSibling.tagName != "HR"){
                    div_items.insertAdjacentHTML('afterend', '<hr name="hr">')
                }
                else{
                    div_items.nextElementSibling.remove()
                }
            }

            if (botao_key) {
                input = event.target.closest('.items').querySelector('input')
                if (input.type == "password"){
                    input.type = "text"
                }
                else{
                    input.type = "password"
                }
            }
        });

    // Salvar alterações na lista

    function salvar() {
        const elementos = container.children

        if(container.querySelector("hr")){
            let cont = 0

            while(cont < elementos.length){
                if(elementos[cont].tagName == "HR"){
                    input = elementos[cont-1].querySelector('input')
                    input.value += "#,@LINE_DIVIDER@,#"
                }
                
                cont++
            }
        }

        container.querySelectorAll('input').forEach((item) => {
            if (item.type == "password") {
                item.value += "#,@SECRET_PASSWORD@,#"
            }
        });

        document.getElementById("envia").submit()
    }

    document.querySelectorAll("button[data-submit='true']").forEach(btn => {
        btn.addEventListener('click', salvar);
    })

    document.getElementById("input_mudar_nome").addEventListener('change', () => {
        salvar();
    })

    // Deletar lista

    const div_deletar = document.getElementById('certeza_deletar');

    document.getElementById("deletar_lista").addEventListener('click', () => {
        div_deletar.style.display = "flex";
    });

    document.getElementById('input_delete').addEventListener('input', () => {
        const botao_deletar = document.getElementById('button_deletar');
        if (document.getElementById('input_delete').value == "<?php echo $_SESSION['type_lista'][2]; ?>"){
            botao_deletar.textContent = "Deletar"
        }
        else{
            botao_deletar.textContent = "Voltar"
        }
    })
</script>
</html> 