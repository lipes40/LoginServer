<?php

    // Adiciona arquivos necessários
    require ('protect.php');
    require ('connector.php');
    require ('cripto.php');

    // Verifica se o get com nome da lista existe

    $name_list = $_GET['lista'];
    if (!$name_list) {
        header("Location: selecionar_lista.php");
        exit();
    }
    $name_list = urldecode($name_list);
    $name_list = str_replace("strcontainmais", "+", $name_list);

    // Puxa os dados com base no get lista ja tratado

    $sql = "SELECT * FROM listas WHERE user_id = ? AND nome_lista = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id'], $name_list]);

    $_SESSION['type_lista'] = $stmt->fetch();

    // Tratamentos no arquivo puxado do banco

    if(!$_SESSION['type_lista']){
        header("Location: selecionar_lista.php");
        exit();
    }

    if ($_SESSION['type_lista'][5] == "privado"){
        $_SESSION['type_lista'][2]  = decrypt_aes_gcm($_SESSION['type_lista'][2], $_SESSION['senha']);
    }

    if ($_SESSION['type_lista'][4] == "lista"){
        header("Location: painel.php?lista=" . $_GET['lista']);
        exit();
    }

    if ($_SESSION['type_lista'][5] == "publico") {
        $visibilidade = [["publico", "privado"], ["Público", "Privado"]];
    }
    else{
        $visibilidade = [["privado", "publico"], ["Privado", "Público"]];
    }

    if ($_SESSION['type_lista'][4] == "lista"){
        $tipo = [["lista", "bloco"], ["Lista", "Bloco"]];
    }
    else{
        $tipo = [["bloco", "lista"], ["Bloco", "Lista"]];
    }

    $_SESSION['bloco'] = $_SESSION['type_lista'][3];
    
    if ($_SESSION['type_lista'][5] == "privado"){
        $_SESSION['bloco'] = decrypt_aes_gcm($_SESSION['bloco'], $_SESSION['senha']);
    }

    $mostrar = true;

    $error = '';

    $cont = 0;

    $lista = $_SESSION['bloco'];

    // Código de post

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (isset($_POST['items'])) {
            // Criptografa o novo nome se necessário
            
            $lista = $_POST['items'];
            $novo_nome = trim($_POST['nome']);
            $estado = $_POST['visibilidade'];
            $novo_tipo = $_POST['tipo'];


            if ($estado == 'privado') {
                $novo_nome = encrypt_aes_gcm($novo_nome, $_SESSION['senha']);
                $lista = encrypt_aes_gcm($lista, $_SESSION['senha']);
            }

            // Verifica se o nome já existe no banco de dados

            $stmt = $pdo->prepare('SELECT id FROM listas WHERE nome_lista = ?');
            $stmt->execute([$novo_nome]);
            $nome_existente = $stmt->fetch();

            if(!$nome_existente || $nome_existente[0] == $_SESSION['type_lista'][0]){
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

        if (isset($_POST['deletar_lista'])){
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

        form{
            display: flex;
            width: 100%;
            height: 100%;
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
            padding: 10px;
            background-color: #111111;
            color: white;
            border-radius: 10px;
            border: 2px solid white;
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
            margin-top: 10px;
            margin-bottom: 20px;
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
            gap: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: row;
        }

        .input-mudar-nome{
            background-color: white;
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

        .form-delete{
            gap: 20px;
        }

        .error{
                color: red;
        }

        .checkbox{
            display: flex;
            flex-direction: row;
            gap: 5px;
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
<body class="frame">
    <div id="certeza_deletar" class="certeza-deletar">
            <form method="POST" class="form-delete" action="">
                <h1>Tem certeza que deseja deletar a lista: <?php echo $_SESSION['type_lista'][2] ?>?</h1>
                <h3>Digite o nome da lista para deletar</h3>
                <input placeholder="Digite o nome da lista" class="input-delete" type="text" name="deletar_lista">
                <button type="button" data-submit="true">Deletar</button>
            </form>
        </div>
    <?php require ("frame_painel.php") ?>
    <form id="envia" method="post" action="">

    <div class="list-info">
    <h2><?php echo $_SESSION['type_lista'][2] ?></h2>
    <div class="dropdown">
        <img src="img/informacoes.png" alt="">
        <ul>
            <li>
                <p>Nome Da Lista:</p>
                    <input id="input_mudar_nome" class="input-mudar-nome" name="nome" type="text" value="<?php echo $_SESSION['type_lista'][2] ?>">
            </li>
            <li>
                <label for="">Visibilidade:</label>
                <select name="visibilidade" id="visibilidade" onchange="salvar()">
                    <option value="<?php echo $visibilidade[0][0] ?>"><?php echo $visibilidade[1][0]?></option>
                    <option value="<?php echo $visibilidade[0][1] ?>"><?php echo $visibilidade[1][1]?></option>
                </select>
            </li>
            <li>
                <label for="">Tipo da lista:</label>
                <select name="tipo" id="" onchange="salvar()">
                    <option value="<?php echo $tipo[0][0] ?>"><?php echo $tipo[1][0] ?></option>
                    <option value="<?php echo $tipo[0][1] ?>"><?php echo $tipo[1][1] ?></option>
                </select>
            </li>
            <li class="checkbox">
                <p>Link redirecionável:</p>
                <input id="checkbox" class="checkbox" type="checkbox" value="">
            </li>
            <button id="deletar_lista" class="delete" type="button">Deletar lista</button>
        </ul>
    </div>
</div>
        <p class="error"><?php echo $error ?? ""; ?></p>
        <div class="inputs-container">
            <div id="inputs-container" class="inputs-container">
                <div class="items">
                <textarea id="text" type="text" placeholder="Adicione algo" name="items"><?php {echo htmlspecialchars(trim($lista));}?></textarea>
                </div>
            </div>
            <div class="btns-out">
                <button class="salvar" type="button" data-submit="true">Salvar</button>
                <a href="selecionar_lista.php">
                <button type="button" class="add" id="adicionar">Voltar para Listas</button>
                </a>
                <a class="base-line" href="logout.php"><button type="button" class="sair">Sair</button></a>
            </div>
        </div>
        


    
    </form>
        
    </div>

</body>
<script>
    const checkbox = document.getElementById('checkbox')
    const text = document.getElementById("text")

    if(text.innerHTML.includes("#,@LINK_REDIRECT@,#")){
        text.innerHTML = text.innerHTML.replace("#,@LINK_REDIRECT@,#", "")
        checkbox.checked = true
    }

    const div_deletar = document.getElementById('certeza_deletar');

    document.getElementById("deletar_lista").addEventListener('click', () => {
        div_deletar.style.display = "flex";
    });

    document.getElementById("input_mudar_nome").addEventListener('change', () => {
        salvar();
    })

    document.querySelectorAll("button[data-submit='true']").forEach(btn => {
        btn.addEventListener('click', salvar);
    })
    
    function salvar() {
        if (checkbox.checked){
            text.innerHTML += "#,@LINK_REDIRECT@,#"
        }

        document.getElementById("envia").submit()
    }
</script>
</html>