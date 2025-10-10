<?php 
    require("connector.php");

    if(!isset($_SESSION)) {
        session_start();
    }

    $error = '';
    $verify = false;

    $id = $_SESSION['id'];

    if(isset($_POST['senha']) && isset($_POST['conta'])) {
        $senha = $_POST['senha'];
        if(strlen($_POST['senha']) == 0) {
            $error = "Digite a senha";
        }

        elseif(strlen($_POST['conta']) == 0) {
            $error = "Digite o texto de verificação";
        }

        elseif(!password_verify($senha, $_SESSION['senha'])){
            $error = "Senha errada";
        }

        elseif($_POST['conta'] == "deletar minha conta") {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);

            session_destroy();
            header("Location: index.php");

            exit;
        }
            
        else{
            $error = "Digite o texto de verificação corretamente";
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deletar Conta</title>
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
        }

        body{
            background-color: #111111;

        }

        header{
            margin-top: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .btn-morte{
            display: flex;
            background-color: red;
            color: black;
            height: 30px;
            width: 70%;
            align-items: center;
            justify-content: center;
        }

        .btn-morte:hover{
            transform: scale(1.05);
            background-color: #8B0000;
        }

        .mostrar{
            margin-right: 20px;
        }

        .info{
            display: none;
            margin-top: 100px;
            flex-direction: column;
        }

        form{
            display: flex;
            margin-top: 20px;
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

        .header-delet{
            margin-top: 100px;
            display: flex;
            flex-direction: column;
        }

        .form-delet{
            display: flex;
            flex-direction: column;
            justify-content: baseline;
            align-items: center;
            height: 60vh;
            margin: 0;
        }

        b{
            color: red;
        }

        .error{
            margin-top: 10px;
        }

        .input-delet{
            background-color: black;
            color: white;
            border-radius: 15px;
            border: none;
            padding-left: 10px;
            height: 10%;
            width: 30%;
        }

        .div-delet{
            display: flex;
            margin-top: 50px;
            justify-content: space-between;
            width: 80%;
            height: 10%;
        }

        .btn-delet{
            background-color: red;
            border: none;
            border-radius: 15px;
            color: white;
            width: 20%;
            height: 100%;
            cursor: pointer;
        }


        .a-delet{
            width: 20%;
            height: 100%;
        }

        .p-delet{
            color: white;
        }

        .btn-volt{
            cursor: pointer;
            background-color: black;
            border: none;
            border-radius: 15px;
            color: white;
            height: 100%;
            width: 100%;
        }

        @media(max-width: 600px) {
            .input-delet{
                width: 70%;
            }

            .btn-delet{
                width: 40%;
            }

            .a-delet{
                width: 40%;
            }
        }

    </style>
</head>
<body>

    <header>
        <h1 class="h1-delet">Tem certeza que quer <b>Excluir?</b></h1>

        <p class="p-delet">Depois de excluida sua conta não poderá ser acessada novamente</p>
    </header>

    <form method="POST" class="form-delet" action="">
        <h3>Digite Sua senha para continuar</h3>
        <input class="input-delet" id="minhaSenha" name="senha" value="" type="password" placeholder="Senha">

        <h3>Digite <b>"deletar minha conta"</b></h3>
        <input class="input-delet" id="minhaConta" name="conta" value="" type="text" placeholder="deletar minha conta">

        <b class="error"><?php echo $error; ?></b>

        <div class="div-delet">
            <a class="a-delet" href="painel.php"><button type="button" onclick="abrirModal()" class="btn-volt">Voltar</button></a>
            <button type="submit" class="btn-delet">Deletar</button>
        </div>
    </form>
</body>

<script>
    const modal = document.getElementById("delet");

    function abrirModal() {
        if (modal.style.display === "none") {
            modal = "flex";
        }
        else{
            modal.style.display = "flex";
        }
    }

    const conta = document.getElementById("minhaConta");
    const senha = document.getElementById("minhaSenha");

    function enviar() {
        if(conta.value === "deletar minha conta") {
            return true;
        }

        return false;
    }
</script>
</html>

