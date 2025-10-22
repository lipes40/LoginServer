<?php

    require("connector.php");
    require("cripto.php");

    /*if(isset($_POST)){
        echo "<pre>";

        print_r($_POST);

        echo "<pre>";
    }*/

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nome = $_POST['c-nome'];
        $email = $_POST['c-email'];
        $senha = $_POST['c-senha'];
        if (!str_contains($email, "@") or !str_contains($email, ".") or strlen($senha) < 6) {
            die("Error html alterado");
        }

        $criptoSenha = password_hash($senha, PASSWORD_DEFAULT);


        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Esse email ja foi cadastrado!";
        } else {

            $lista = encrypt_aes_gcm("", $senha);
            $bloco = "";

            $stmt = $pdo->prepare("SELECT MAX(id) FROM usuarios");
            $stmt->execute();
            $id = $stmt->fetch()[0] + 1;

            $stmt = $pdo->prepare("INSERT INTO usuarios (id, nome, email, senha, lista, bloco) VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->execute([$id, $nome, $email, $criptoSenha, $lista, $bloco]);


            header("Location: index.php");

            exit;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cadastro</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <style>
        *{
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #111111;
            display: flex;
            justify-content: center;
            height: 100vh;
            width: 100%;
            flex-direction: column;
        }

        header {
            display: flex;
            justify-content: center;
            height: 30%;
        }

        h1 {
            color: white;
        }

        h2 {
            color: white;
        }

        .conjunto {
            gap: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 30%;
        }

        .loader {
            width: 40px;
            height: 40px;
            border: 6px solid #ccc;
            border-top: 6px solid #8A2BE2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <header>
        <h1><?php echo $error; ?></h1>
    </header>

    <div class="conjunto">
        <div class="loader"></div>
        <h2>Redirecionando...</h2>
    </div>


</body>

<script>
    setTimeout(() => {
        window.location.href = "cadastrar.php";
    }, 3000);
</script>

</html>