<?php
session_start();
require_once 'conexao.php';

// PROTEÇÃO
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: painel_capitao.php");
    exit;
}

$acao       = $_POST['acao'] ?? '';
$capitao_id = $_SESSION['usuario_id'];
$time_id    = (int)($_POST['time_id'] ?? 0);

// Segurança: confirma que o time_id realmente pertence a este capitão
$stmt = $pdo->prepare("SELECT id FROM times WHERE id = ? AND capitao_id = ?");
$stmt->execute([$time_id, $capitao_id]);
if (!$stmt->fetch()) {
    $_SESSION['mensagem'] = "<p style='color:red;'>Ação não autorizada.</p>";
    header("Location: painel_capitao.php");
    exit;
}

// ── ADICIONAR JOGADOR ────────────────────────────────────────────────────────
if ($acao === 'adicionar') {
    $nome          = trim($_POST['nome'] ?? '');
    $numero_camisa = !empty($_POST['numero_camisa']) ? (int)$_POST['numero_camisa'] : null;
    $posicao       = trim($_POST['posicao'] ?? '') ?: null;

    if (empty($nome)) {
        $_SESSION['mensagem'] = "<p style='color:red;'>O nome do jogador é obrigatório.</p>";
        header("Location: gerenciar_elenco.php");
        exit;
    }

    // Verifica se já existe jogador com mesmo nome neste time
    $stmt = $pdo->prepare("SELECT id FROM jogadores WHERE time_id = ? AND nome = ?");
    $stmt->execute([$time_id, $nome]);
    if ($stmt->fetch()) {
        $_SESSION['mensagem'] = "<p style='color:orange;'>Já existe um jogador com esse nome no elenco.</p>";
        header("Location: gerenciar_elenco.php");
        exit;
    }

    // Verifica número de camisa duplicado (se informado)
    if ($numero_camisa !== null) {
        $stmt = $pdo->prepare("SELECT id FROM jogadores WHERE time_id = ? AND numero_camisa = ?");
        $stmt->execute([$time_id, $numero_camisa]);
        if ($stmt->fetch()) {
            $_SESSION['mensagem'] = "<p style='color:orange;'>Já existe um jogador com o número $numero_camisa neste time.</p>";
            header("Location: gerenciar_elenco.php");
            exit;
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO jogadores (time_id, nome, numero_camisa, posicao) VALUES (?, ?, ?, ?)");
        $stmt->execute([$time_id, $nome, $numero_camisa, $posicao]);
        $_SESSION['mensagem'] = "<p style='color:green;'>Jogador <strong>$nome</strong> adicionado ao elenco!</p>";
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color:red;'>Erro ao adicionar jogador. Tente novamente.</p>";
    }

    header("Location: gerenciar_elenco.php");
    exit;
}

// ── REMOVER JOGADOR ──────────────────────────────────────────────────────────
if ($acao === 'remover') {
    $jogador_id = (int)($_POST['jogador_id'] ?? 0);

    // Segurança: confirma que o jogador pertence ao time deste capitão
    $stmt = $pdo->prepare("SELECT id FROM jogadores WHERE id = ? AND time_id = ?");
    $stmt->execute([$jogador_id, $time_id]);
    if (!$stmt->fetch()) {
        $_SESSION['mensagem'] = "<p style='color:red;'>Jogador não encontrado.</p>";
        header("Location: gerenciar_elenco.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM jogadores WHERE id = ?");
        $stmt->execute([$jogador_id]);
        $_SESSION['mensagem'] = "<p style='color:green;'>Jogador removido do elenco.</p>";
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = "<p style='color:red;'>Erro ao remover jogador. Tente novamente.</p>";
    }

    header("Location: gerenciar_elenco.php");
    exit;
}

// Ação desconhecida
header("Location: painel_capitao.php");
exit;