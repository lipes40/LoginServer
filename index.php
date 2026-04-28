<?php

require('connector.php');
require('cripto.php');

session_start();

$slug = trim($_SERVER['REQUEST_URI'], '/');
$slug = str_replace("login", "", $slug);

if ($slug != "" && $slug != "/index.php"){
    $slug = str_replace("/", "", $slug);
    header("Location: publico.php?lista=" . $slug);
    exit();
}

if (isset($_SESSION['id'])) {
        header("Location: painel.php?lista=" . $slug);
        exit;
}

$error = '';

if (isset($_POST['email']) && isset($_POST['senha'])) {

    if (strlen($_POST['email']) == 0) {
        $error = "Preencha seu email!";
    } elseif (strlen($_POST['senha']) == 0) {
        $error = "Preencha sua senha!";
    } else {

        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        $usuario = $stmt->fetch();



        if ($usuario) {

            $verify = password_verify($senha, $usuario['senha']);

            if ($verify) {

                if (!isset($_SESSION)) {
                    session_start();
                }

                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['senha'] = $senha;
                $_SESSION["cripto_senha"] = $usuario['senha'];

                if($usuario['lista'] != ""){
                    $nome_lista = encrypt_aes_gcm('Linhas antigas', $_SESSION['senha']);
                    $lista = decrypt_aes_gcm($usuario['lista'], $_SESSION['senha']);
                    $lista = str_replace("###,,,@@@", "#,@SEPARATOR_LINES@,#", $lista);
                    $lista = encrypt_aes_gcm($lista, $_SESSION['senha']);
                    
                    $nome_bloco = encrypt_aes_gcm('Bloco antigo', $_SESSION['senha']);
                    $bloco = $usuario['bloco'];

                    $stmt = $pdo->prepare('INSERT INTO listas (user_id, nome_lista, lista, tipo, visibilidade, publico_editavel) VALUES (?, ?, ?, ?, ?, ?), (?, ?, ?, ?, ?, ?)');
                    $stmt->execute(
                        [$_SESSION['id'], 
                        $nome_lista, 
                        $lista, 
                        'linhas', 
                        'privado', 
                        'nao editavel',
                        
                        $_SESSION['id'], 
                        $nome_bloco, 
                        $bloco, 
                        'bloco', 
                        'privado', 
                        'nao editavel',
                    ]);

                    $stmt = $pdo->prepare("UPDATE usuarios SET lista = ?, bloco = ? WHERE id = ?");
                    $stmt->execute(['', '', $_SESSION['id']]);
                }

                header("Location: painel.php");

                exit;
            } else {
                $error = "Falha ao logar! email ou senha incorretos!";
            }
        } else {
            $error = "Falha ao logar! email ou senha incorretos!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">

    <style>
        * {
            padding: 0;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }


        body {
            display: flex;
            background-color: #111111;
            color: white;
            height: 90vh;
            width: 100vw;
            justify-content: center;
        }

        header {
            display: flex;
            height: 100px;
            width: 100vw;
            justify-content: center;
            align-items: center;
        }

        h1 {
            color: white;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        .creditos {
            font-family: Arial, Helvetica, sans-serif;
            position: absolute;
            bottom: 10px;
        }

        .box-center {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        input {
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            background-color: black;
            color: white;
            border: none;
            height: 50px;
            max-width: 550px;            
            width: 90%;
            border-radius: 10px;
            padding-left: 5px;
        }

        .senha-password {
            display: flex;
            width: 100%;
            height: 100%;
        }

        .senhas {
            flex-direction: row;
            width: 100%;
            height: 100%;
        }

        .ver-senha {
            width: 24px;
            display: flex;
            align-self: center;
            right: 2px;
            height: 24px;
            cursor: pointer;
            border: none;
            position: absolute;
            background: none;
        }

        .box-senha {
            display: flex;
            align-items: center;
            height: 8%;
            position: relative;
            width: 91.6%;
            max-width: 555px;
        }

        .entrar {
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #8A2BE2;
            color: white;
            border: none;
            height: 5%;
            width: 90%;
            max-width: 555px;
            border-radius: 5px;
            transition: 0.2s;
            cursor: pointer;
            font-size: large;
        }

        .entrar:hover {
            transform: scale(1.05);
            background-color: #7B68EE;
            color: black;
        }

        p {
            text-decoration: none;
            padding: 0;
            gap: 0;
            margin: 0;
        }

        a{
            color: white;
        }

        .conta {
            text-decoration: none;
            padding: 0;
            gap: 0;
            height: 5px;
            color: #8A2BE2;
        }
    </style>

</head>

<body>
    <form action="" class="box-center" method="POST">

        <h1>Acesse sua conta!</h1>

        <input type="text" id="troca" name="email" placeholder="Nome de usuário" value="<?php echo $_POST['email'] ?? ''; ?>">

        <div class="box-senha">
            <input type="password" id="senha" name="senha" class="senha-password" placeholder="Senha">


            <button class="ver-senha" onclick="visivel()" type="button">
                <img id="iconeSenha" class="ver-senha" src="img/olho-aberto-w.png" alt="Mostrar senha">
            </button>
        </div>

        <button type="submit" class="entrar">Entrar</button>

        <span style="
        font-family: Arial, Helvetica, sans-serif; 
        display: flex; 
        color: red;"><?php if (isset($error) && $error != "") echo $error; ?></span>

        <p>ou</p>

        <a class="conta" type="button" href="cadastrar.php">
            <p>Crie sua conta!</p>
        </a>

    </form>

    
        <span class="creditos">Criado por: <a href="https://github.com/lipes40">Fellipe Teixeira</a></span>
    

</body>

<script>
    const btn = document.getElementById("senha");
    const icone = document.getElementById("iconeSenha")

    function visivel() {
        if (btn.type === "text") {
            btn.type = "password";
            icone.src = "img/olho-aberto-w.png";
            icone.alt = "Mostrar senha"
            return;
        }

        btn.type = "text";
        icone.src = "img/olho-fechado-w.png"
        icone.alt = "Ocultar senha"
    }
</script>

</html>