<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campeonato_id = $_POST['campeonato_id'];
    $time_id = $_POST['time_id'];

    try {
        // 1. Verifica se o time já está inscrito ou pendente neste campeonato
        $stmt_check = $pdo->prepare("SELECT id, status FROM inscricoes WHERE time_id = ? AND campeonato_id = ?");
        $stmt_check->execute([$time_id, $campeonato_id]);
        $inscricao_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($inscricao_existente) {
            $status = $inscricao_existente['status'] == 'pendente' ? 'em análise' : 'aprovada';
            $_SESSION['mensagem'] = "<p style='color: #e67e22;'>Sua inscrição já está <strong>{$status}</strong> neste campeonato!</p>";
        } else {
            // 2. Se não estiver inscrito, insere como 'pendente'
            $stmt = $pdo->prepare("INSERT INTO inscricoes (time_id, campeonato_id, status) VALUES (?, ?, 'pendente')");
            $stmt->execute([$time_id, $campeonato_id]);
            
            $_SESSION['mensagem'] = "<p style='color: green;'>Solicitação enviada! Aguarde a aprovação do organizador.</p>";
        }

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red;'>Erro ao solicitar inscrição: " . $e->getMessage() . "</p>";
    }
    
    header("Location: inscrever_campeonato.php");
    exit;
}

header("Location: painel_capitao.php");
exit;
?>