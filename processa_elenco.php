<?php
session_start();
require_once 'conexao.php';

// Garante que só capitão logado acesse
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}

// AÇÃO 1: ADICIONAR JOGADOR
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'adicionar') {
    $nome_jogador = $_POST['nome_jogador'];
    $time_id = $_POST['time_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO jogadores (nome, time_id) VALUES (?, ?)");
        $stmt->execute([$nome_jogador, $time_id]);
        
        $_SESSION['mensagem'] = "<p style='color: green; font-weight: bold;'>Integrante adicionado com sucesso!</p>";
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red; font-weight: bold;'>Erro ao adicionar: " . $e->getMessage() . "</p>";
    }
    
    header("Location: gerenciar_elenco.php");
    exit;
}

// AÇÃO 2: REMOVER JOGADOR
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['acao']) && $_GET['acao'] == 'remover') {
    $id_jogador = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM jogadores WHERE id = ?");
        $stmt->execute([$id_jogador]);
        
        $_SESSION['mensagem'] = "<p style='color: green; font-weight: bold;'>Integrante removido da equipe.</p>";
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red; font-weight: bold;'>Erro ao remover: " . $e->getMessage() . "</p>";
    }
    
    header("Location: gerenciar_elenco.php");
    exit;
}

header("Location: painel_capitao.php");
exit;
?>