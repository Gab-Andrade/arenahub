<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campeonato_id = $_POST['campeonato_id'];
    $partida_id = $_POST['partida_id'];
    $placar_a = $_POST['placar_a'];
    $placar_b = $_POST['placar_b'];

    try {
        // Atualiza os campos de placar que antes eram NULL
        $stmt = $pdo->prepare("UPDATE partidas SET placar_a = ?, placar_b = ? WHERE id = ?");
        $stmt->execute([$placar_a, $placar_b, $partida_id]);
        
        $_SESSION['mensagem'] = "<p style='color: green;'>Placar atualizado com sucesso!</p>";

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red;'>Erro ao salvar placar: " . $e->getMessage() . "</p>";
    }
    
    // Devolve o Admin para a tela de placares daquele campeonato
    header("Location: atualizar_placares.php?campeonato_id=" . $campeonato_id);
    exit;
}

header("Location: painel_organizador.php");
exit;
?>