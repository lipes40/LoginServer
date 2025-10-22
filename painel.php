<?php
    require ('protect.php');
    require ('connector.php');
    require ('cripto.php');

    $sql = "SELECT lista FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['email']]);

    $resultado = $stmt->fetch();

    $_SESSION['lista'] = $resultado[0];

    $_SESSION['lista'] = decrypt_aes_gcm($_SESSION['lista'], $_SESSION['senha']);

    $mostrar = true;

    $error = '';

    $cont = 0;

    $texto = $_SESSION["lista"];

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $lista = $_POST["items"];

        $lista = array_map('trim', $lista);

        $texto = implode("###,,,@@@", $lista);

        $cripto = encrypt_aes_gcm($texto, $_SESSION['senha']);

        // $json = json_encode($texto, JSON_UNESCAPED_UNICODE);

        $stmt = $pdo->prepare("UPDATE usuarios SET lista = ? WHERE id = ?");
        $stmt->execute([$cripto, $_SESSION["id"]]);
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
        *{
            padding: 0;
            margin: 0;
        }

        p {
            color: white
        }

        .principal{
            display: flex;
            background-color: #111111;
            flex-direction: column;
            height: 100vh;
            width: 100%;
        }

        header{
            display: flex;
            width: 100vw;
            height: 10%;
            justify-content: space-between;
            align-items: center;
        }



        .sair{
            background-color: #8B0000;
        }
        .sair:hover{
            background-color: #8B0000;
        }
        
        .conjunto{
            margin-top: 10px;
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        button{
            font-family: Arial, Helvetica, sans-serif;
            background-color: #8A2BE2;
            color: white;
            border: none;
            height: 50px;
            width: 200px;
            border-radius: 15px;
            margin-bottom: 10px;
            transition: 0.2s;
            cursor: pointer;
        }

        button:hover{
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        .btn-morte{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            background-color: red;
            color: black;
            width: 50%;
            height: 30px;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            transition: 0.2s;
            text-decoration: none;
        }

        .btn-morte:hover{
            transform: scale(1.05);
            background-color: #8B0000;
        }

        .btn-senha{
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            background-color: 	#1E90FF;
            color: black;
            border-radius: 15px;
            width: 50%;
            height: 30px;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-senha:hover{
            transform: scale(1.05);
            background-color: #00008B;
        }

        .buttons{
            width: 100%;
            display: flex;
            flex-direction: row;

        }

        .mostrar{   
            height: 70%;
            margin-right: 20px;
            cursor: pointer;
        }

        .info{
            display: none;
            margin-top: 50px;
            flex-direction: column;
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

        h1{
            color: white;
            margin-left: 20px;
        }

        h2{
            color: white;
        }

        h3{
            color: white;
        }

        .user{
            margin-top: 30px;
        }

        input {
            background-color: #111111;
            color: white;
            border: none;
            font-size: large;
            margin-top: 5px;
            display: flex;
            width: 90%;
            height: 100%;
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

        .salvar{
            margin-left: 5px;
            margin-right: 5px;
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

        .icon-lixeira{
            width: 17px;
        }

        @media (max-width: 600px) {
            .btns-out{
                flex-direction: column;
                align-items: center;
            }

            input{
                font-size: large;
                width: 80%;
            }

            h1{
                font-size: medium;
            }

            .mostrar{
                margin: 5px;
            }

            .deletar{
                width: 40px;
                height: 40px;
            }
        }

    </style>

</head>
<body class="principal">
    <header>
        <h1>Sejá bem vindo ao Painel</h1> 
        <button class="mostrar" onclick="mostra()">Minhas informações</button>
    </header>

    <div class="conjunto">
        <h2>Olá <?php echo $_SESSION['nome']; ?> Tudo Bem?</h2>

        <h3 class="user">Você é o usuário número: <?php echo $_SESSION['id'] ?> parabéns</h3>

        <div id="info" class="info">
            <h3><?php if($mostrar) echo "Nome: " . $_SESSION['nome']; ?></h3>
            <h3><?php if($mostrar) echo "Email: " . $_SESSION['email']; ?></h3>

            <div class="buttons">

                <a class="btn-morte" href="deletar_conta.php">
                    <img src="img/lixeira.png">deletar conta
                </a>

                <a class="btn-senha" href="muda_senha.php">
                    Mudar senha
                </a>
            </div>
            
        </div>

        <form method="post" action="">
            <div class="inputs-container">
                <div id="inputs-container" class="inputs-container">
                    <?php 

                    $lista = explode("###,,,@@@", $texto);
                    
                    foreach($lista as $item): 
                    ?>
                    <div class="items">
                        <p class="numerador"><?php $cont ++;
                        echo $cont ?></p>
                        <input type="text" placeholder="Adicione algo" name="items[]" value="<?php {echo htmlspecialchars(trim($item));}?>">
                        <button type="button" class='deletar'><img class="icon-lixeira" src="img/lixeira-branca.png"></button>
                    </div>
                    <?php endforeach ?>
                    
                </div>
                <div class="btns-out">
                    <button type="submit" class="add" id="adicionar">Adicionar Linha</button>
                    <button class="salvar" type="submit">Salvar</button>
                    <a href="painelbloco.php">
                        <button type="button" id="deletar" class="bloco" type="button">Ir para Bloco</button>
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

    const container = document.getElementById('inputs-container');
    const btnAdicionar = document.getElementById('adicionar')
    const btnDeletar = document.getElementById('deletar')

    btnAdicionar.addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'items[]';
        input.placeholder = 'Adicione algo'
        container.appendChild(input);
    });

    container.addEventListener('click', (event) => {
  const botao = event.target.closest('.deletar');
  if (botao) {
    botao.parentElement.remove();

  }
});

</script>
</html>
