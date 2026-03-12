<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campeonato_id = $_POST['campeonato_id'];
    $time_a_id = $_POST['time_a_id'];
    $time_b_id = $_POST['time_b_id'];
    $data_hora = $_POST['data_hora'];

    // Validação de negócio: Um time não pode jogar contra ele mesmo!
    if ($time_a_id === $time_b_id) {
        $_SESSION['mensagem'] = "<p style='color: red;'>Erro: Um time não pode jogar contra si mesmo. Selecione times diferentes!</p>";
        header("Location: gerenciar_campeonato.php?id=" . $campeonato_id);
        exit;
    }

    try {
        // Insere a partida no banco de dados. 
        // Os placares começam vazios (NULL) e podem ser atualizados depois.
        $stmt = $pdo->prepare("INSERT INTO partidas (campeonato_id, time_a_id, time_b_id, data_hora) VALUES (?, ?, ?, ?)");
        $stmt->execute([$campeonato_id, $time_a_id, $time_b_id, $data_hora]);
        
        $_SESSION['mensagem'] = "<p style='color: green;'>Partida agendada com sucesso!</p>";

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red;'>Erro ao agendar partida: " . $e->getMessage() . "</p>";
    }
    
    // Devolve o organizador para a tela
    header("Location: gerenciar_campeonato.php?id=" . $campeonato_id);
    exit;
}

header("Location: painel_organizador.php");
exit;
?>  