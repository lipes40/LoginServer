<?php

    // Adiciona arquivos necessários
    require ('protect.php');
    require ('connector.php');
    require ('cripto.php');

    // Verifica se o get com nome da lista existe

    $name_list = $_GET['lista'];
    if (!$name_list) {
        header("Location: selecionar_lista.php");
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
        die($name_list);
        // header("Location: selecionar_lista.php");
        // exit();
    }

    if ($_SESSION['type_lista'][5] == "privado"){
        $_SESSION['type_lista'][2]  = decrypt_aes_gcm($_SESSION['type_lista'][2], $_SESSION['senha']);
    }

    if ($_SESSION['type_lista'][4] == "lista"){
        header("Location: painel.php?lista=" . $_GET['lista']);
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
        // Criptografa o novo nome se necessário
        
        $lista = $_POST['items'];
        $novo_nome = trim($_POST['nome']);
        $estado = $_POST['visibilidade'];
        $novo_tipo = $_POST['tipo'];


        if ($estado == 'privado') {
            $novo_nome = encrypt_aes_gcm($novo_nome, $_SESSION['senha']);
            $lista = encrypt_aes_gcm($lista, $_SESSION['senha']);
        }

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
            margin-top: 20px;
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

        li{

        }

        .form-mudar-nome{

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
        </ul>
    </div>
</div>
        <div class="inputs-container">
            <div id="inputs-container" class="inputs-container">
                <div class="items">
                <textarea type="text" placeholder="Adicione algo" name="items"><?php {echo htmlspecialchars(trim($lista));}?></textarea>
                </div>
            </div>
            <div class="btns-out">
                <button class="salvar" type="submit">Salvar</button>
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
    function salvar() {
        document.getElementById("envia").submit()
    }

    document.getElementById("input_mudar_nome").addEventListener('change', () => {
        salvar();
    })
</script>
</html>