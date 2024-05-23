<?php

$host = 'localhost';
$db = 'bemol';
$port = 3306;
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'Erro na conexão com o banco de dados: ' . $e->getMessage();
    exit;
}
/*
este trecho de código PHP estabelece uma conexão com um banco de dados MySQL usando a classe PDO e 
configura a manipulação de erros para lançar exceções em caso de problemas de conexão.
*/
?>