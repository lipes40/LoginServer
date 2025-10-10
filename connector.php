<?php
//echo "ok";

$usuario = "root";
$senha = "";
$dbname = "login";
$host = "localhost";

try{

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $usuario, $senha);

}

catch(PDOException $e){
    echo $e->getMessage();
}


?>