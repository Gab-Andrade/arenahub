<?php
session_start();
require_once 'conexao.php';

// Proteção
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inscricao_id = $_POST['inscricao_id'];
    $campeonato_id = $_POST['campeonato_id'];
    $acao = $_POST['acao']; // Pode vir como 'aprovar' ou 'recusar'

    try {
        if ($acao === 'aprovar') {
            // Atualiza o status para aprovada
            $stmt = $pdo->prepare("UPDATE inscricoes SET status = 'aprovada' WHERE id = ?");
            $stmt->execute([$inscricao_id]);
            $_SESSION['mensagem'] = "<p style='color: green;'>Time aprovado com sucesso!</p>";
            
        } elseif ($acao === 'recusar') {
            // Se recusar, a gente apaga a inscrição para liberar espaço
            $stmt = $pdo->prepare("DELETE FROM inscricoes WHERE id = ?");
            $stmt->execute([$inscricao_id]);
            $_SESSION['mensagem'] = "<p style='color: red;'>Inscrição recusada e removida.</p>";
        }

    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color: red;'>Erro ao processar: " . $e->getMessage() . "</p>";
    }
    
    // Devolve o organizador para a tela do campeonato correto
    header("Location: gerenciar_campeonato.php?id=" . $campeonato_id);
    exit;
}

header("Location: painel_organizador.php");
exit;
?>