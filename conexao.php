<?php

$host = 'localhost';
$dbname = 'arenahub';
$usuario = 'root';
$senha = '';       

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $usuario, $senha);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //echo "Conexão com o banco do ArenaHub realizada com sucesso!"; 
    
} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>