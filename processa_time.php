<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['perfil'] == 'capitao') {
    $nome = $_POST['nome'];
    $contato = $_POST['contato'];
    $logo = $_POST['caminho_logo'];
    $capitao_id = $_SESSION['usuario_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO times (nome, contato, caminho_logo, capitao_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $contato, $logo, $capitao_id]);

        $_SESSION['mensagem'] = "<p style='color: green;'>Time cadastrado com sucesso!</p>";
        header("Location: painel_capitao.php");
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
        header("Location: painel_capitao.php");
    }
}
?>